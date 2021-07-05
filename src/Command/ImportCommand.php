<?php

namespace App\Command;

use App\Entity\Logger;
use App\Entity\Media;
use App\Entity\News;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use DOMElement;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XMLReader;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';
    /** @var string */
    public string $defaultUrl;
    private EntityManagerInterface $em;
    private OutputInterface $output;
    /** @var XMLReader */
    private XMLReader $reader;

    private string $method = 'GET';

    /**
     * ImportCommand constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->defaultUrl = 'http://static.feed.rbc.ru/rbc/logical/footer/news.rss';
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Импорт RSS')
            ->setHelp('Выполняет загрузку RSS');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set("memory_limit", "512M");

        $this->output = $output;
        $this->output->writeln('Начинаем импорт данных...');

        $result = $this->getContent($this->defaultUrl);
        $logger = new Logger();
        $logger
            ->setMoment(new DateTime())
            ->setUrl($this->defaultUrl)
            ->setMethod($this->method)
            ->setResponseCode($result['responseCode'])
            ->setResponseBody($result['responseBody']);
        $this->em->persist($logger);
        $this->em->flush();

        if (null === $result['content']) {
            $this->output->writeln('Невозможно загрузить контент...');
            return Command::FAILURE;
        }

        $this->parseContent($result['content']);

        $this->output->writeln('Импорт завершен.');
        return Command::SUCCESS;
    }

    /**
     * @param string $url
     * @return array
     */
    private function getContent(string $url): array
    {
        $headers = [
            'Connection: keep-alive',
            'Pragma: no-cache',
            'Cache-Control: no-cache',
            'Accept: */*',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.67 Safari/537.36 Edg/87.0.664.52',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: ru,en;q=0.9,en-GB;q=0.8,en-US;q=0.7',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_POST, true);
        if ($this->method == 'GET') {
            curl_setopt($ch, CURLOPT_POST, false);
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $html = curl_exec($ch);
        $ch_info = curl_getinfo($ch);

        $responseCode = $ch_info['http_code'];
        $responseBody = null;
        $content = null;

        if (!empty($html)) {
            $responseBody = mb_substr($html, $ch_info['header_size']);
            if ($responseCode == 200 && !empty($responseBody)) {
                try {
                    $content = gzdecode($responseBody);
                } catch (Exception $e) {
                }
                if ($content) {
                    $responseBody = $content;
                }
            }
        }

        curl_close($ch);

        return [
            'content' => $content,
            'responseCode' => $responseCode,
            'responseBody' => $responseBody,
        ];
    }

    /**
     * @param string $content
     */
    private function parseContent(string $content): void
    {
        $this->reader = new XMLReader();
        $this->reader->XML($content);
        $this->reader->moveToAttribute('item');

        while ($this->reader->read()) {
            if ($this->reader->nodeType === XMLReader::ELEMENT) {

                if ($this->reader->localName == 'item') {
                    /** @var DOMElement $dom */
                    $dom = $this->reader->expand();

                    if ($dom === false) {
                        continue;
                    }
                    $data = $this->parseNode($dom);
                    if (!empty($data)) {
                        try {
                            $this->saveToDb($data);
                        } catch (Exception $e) {
                            $this->output->writeln('Невозможно сохранить запись в БД...');
                        }
                    }
                }
            }
        }
    }

    /**
     * @param DOMElement $dom
     * @return array
     */
    private function parseNode(DOMElement $dom): array
    {
        $children = $dom->childNodes;
        $data = [];
        $media = [];

        foreach ($children as $child) {

            if ($child->nodeName == '#text') {
                continue;
            }

            if ($child->nodeName == 'enclosure') {
                $tmp = [];
                if ($child->hasAttributes()) {
                    foreach ($child->attributes as $attr) {
                        switch ($attr->name) {
                            case 'url':
                                $tmp['url'] = $attr->value;
                                break;
                            case 'type':
                                $tmp['type'] = $attr->value;
                                break;
                        }
                    }
                }
                $media[] = $tmp;
            } else {
                $data[$child->nodeName] = $child->nodeValue;
            }
            $data['media'] = $media;
        }

        return $data;
    }

    /**
     * @param array $data
     * @throws Exception
     */
    private function saveToDb(array $data)
    {
        $news = $this->em->getRepository(News::class)
            ->findOneBy(['guid' => $data['guid']]);
        if ($news) {
            return;
        }

        $news = new News();
        $news
            ->setGuid($data['guid'])
            ->setLink($data['link'])
            ->setTitle($data['title'])
            ->setAuthor($data['author'] ?? null)
            ->setDescription($data['description'])
            ->setPubDate(new DateTime($data['pubDate']));

        $this->em->persist($news);
        foreach ($data['media'] as $item) {
            $media = new Media();
            $media
                ->setUrl($item['url'])
                ->setType($item['type'])
                ->setNews($news);

            $this->em->persist($media);
        }

        $this->em->flush();
    }
}

