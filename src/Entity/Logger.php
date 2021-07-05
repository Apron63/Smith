<?php

namespace App\Entity;

use App\Repository\LoggerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LoggerRepository::class)
 */
class Logger
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $moment;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $method;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="smallint")
     */
    private $responseCode;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $responseBody;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoment(): ?\DateTimeInterface
    {
        return $this->moment;
    }

    public function setMoment(\DateTimeInterface $moment): self
    {
        $this->moment = $moment;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function setResponseCode(int $responseCode): self
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }

    public function setResponseBody(?string $responseBody): self
    {
        $this->responseBody = $responseBody;

        return $this;
    }
}
