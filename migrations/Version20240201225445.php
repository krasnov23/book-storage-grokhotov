<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240201225445 extends AbstractMigration
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
        $this->addSql('CREATE TABLE book_category_entity (id INT AUTO_INCREMENT NOT NULL, parent_category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, level INT NOT NULL, INDEX IDX_85771C68796A8F92 (parent_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_category_entity_book_entity (book_category_entity_id INT NOT NULL, book_entity_id INT NOT NULL, INDEX IDX_CBA76A00893E6EF (book_category_entity_id), INDEX IDX_CBA76A00938B6B1B (book_entity_id), PRIMARY KEY(book_category_entity_id, book_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_entity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, amount_of_pages INT DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, short_description VARCHAR(2000) DEFAULT NULL, long_description LONGTEXT DEFAULT NULL, published_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback_entity (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, message LONGTEXT NOT NULL, phone_number BIGINT DEFAULT NULL, created_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, name_selector VARCHAR(255) NOT NULL, amount_book_pagination INT DEFAULT NULL, admin_email VARCHAR(255) DEFAULT NULL, source_json VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_entity (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_6B7A5F55E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE author_entity_book_entity ADD CONSTRAINT FK_AD3F793AC1AA74 FOREIGN KEY (author_entity_id) REFERENCES author_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE author_entity_book_entity ADD CONSTRAINT FK_AD3F79938B6B1B FOREIGN KEY (book_entity_id) REFERENCES book_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_category_entity ADD CONSTRAINT FK_85771C68796A8F92 FOREIGN KEY (parent_category_id) REFERENCES book_category_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_category_entity_book_entity ADD CONSTRAINT FK_CBA76A00893E6EF FOREIGN KEY (book_category_entity_id) REFERENCES book_category_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_category_entity_book_entity ADD CONSTRAINT FK_CBA76A00938B6B1B FOREIGN KEY (book_entity_id) REFERENCES book_entity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author_entity_book_entity DROP FOREIGN KEY FK_AD3F793AC1AA74');
        $this->addSql('ALTER TABLE author_entity_book_entity DROP FOREIGN KEY FK_AD3F79938B6B1B');
        $this->addSql('ALTER TABLE book_category_entity DROP FOREIGN KEY FK_85771C68796A8F92');
        $this->addSql('ALTER TABLE book_category_entity_book_entity DROP FOREIGN KEY FK_CBA76A00893E6EF');
        $this->addSql('ALTER TABLE book_category_entity_book_entity DROP FOREIGN KEY FK_CBA76A00938B6B1B');
        $this->addSql('DROP TABLE author_entity');
        $this->addSql('DROP TABLE author_entity_book_entity');
        $this->addSql('DROP TABLE book_category_entity');
        $this->addSql('DROP TABLE book_category_entity_book_entity');
        $this->addSql('DROP TABLE book_entity');
        $this->addSql('DROP TABLE feedback_entity');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE user_entity');
    }
}
