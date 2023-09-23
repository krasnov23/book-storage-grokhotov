<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230919195918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book_category_entity (id INT AUTO_INCREMENT NOT NULL, parent_category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, level INT NOT NULL, INDEX IDX_85771C68796A8F92 (parent_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_category_entity_book_entity (book_category_entity_id INT NOT NULL, book_entity_id INT NOT NULL, INDEX IDX_CBA76A00893E6EF (book_category_entity_id), INDEX IDX_CBA76A00938B6B1B (book_entity_id), PRIMARY KEY(book_category_entity_id, book_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_category_entity ADD CONSTRAINT FK_85771C68796A8F92 FOREIGN KEY (parent_category_id) REFERENCES book_category_entity (id)');
        $this->addSql('ALTER TABLE book_category_entity_book_entity ADD CONSTRAINT FK_CBA76A00893E6EF FOREIGN KEY (book_category_entity_id) REFERENCES book_category_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_category_entity_book_entity ADD CONSTRAINT FK_CBA76A00938B6B1B FOREIGN KEY (book_entity_id) REFERENCES book_entity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_category_entity DROP FOREIGN KEY FK_85771C68796A8F92');
        $this->addSql('ALTER TABLE book_category_entity_book_entity DROP FOREIGN KEY FK_CBA76A00893E6EF');
        $this->addSql('ALTER TABLE book_category_entity_book_entity DROP FOREIGN KEY FK_CBA76A00938B6B1B');
        $this->addSql('DROP TABLE book_category_entity');
        $this->addSql('DROP TABLE book_category_entity_book_entity');
    }
}
