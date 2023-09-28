<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230925093548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author_entity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE author_entity_book_entity (author_entity_id INT NOT NULL, book_entity_id INT NOT NULL, INDEX IDX_AD3F793AC1AA74 (author_entity_id), INDEX IDX_AD3F79938B6B1B (book_entity_id), PRIMARY KEY(author_entity_id, book_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE author_entity_book_entity ADD CONSTRAINT FK_AD3F793AC1AA74 FOREIGN KEY (author_entity_id) REFERENCES author_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE author_entity_book_entity ADD CONSTRAINT FK_AD3F79938B6B1B FOREIGN KEY (book_entity_id) REFERENCES book_entity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author_entity_book_entity DROP FOREIGN KEY FK_AD3F793AC1AA74');
        $this->addSql('ALTER TABLE author_entity_book_entity DROP FOREIGN KEY FK_AD3F79938B6B1B');
        $this->addSql('DROP TABLE author_entity');
        $this->addSql('DROP TABLE author_entity_book_entity');
    }
}
