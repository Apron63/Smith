<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210705060154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE logger (id INT AUTO_INCREMENT NOT NULL, moment DATETIME NOT NULL, method VARCHAR(25) NOT NULL, url VARCHAR(255) NOT NULL, response_code SMALLINT NOT NULL, response_body LONGBLOB DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, news_id INT NOT NULL, url VARCHAR(255) NOT NULL, type VARCHAR(25) NOT NULL, INDEX IDX_6A2CA10CB5A459A0 (news_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, guid VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, author VARCHAR(255) DEFAULT NULL, pub_date DATETIME NOT NULL, INDEX idx_guid (guid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CB5A459A0 FOREIGN KEY (news_id) REFERENCES news (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CB5A459A0');
        $this->addSql('DROP TABLE logger');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE news');
    }
}
