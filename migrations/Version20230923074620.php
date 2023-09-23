<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230923074620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_category_entity DROP FOREIGN KEY FK_85771C68796A8F92');
        $this->addSql('ALTER TABLE book_category_entity ADD CONSTRAINT FK_85771C68796A8F92 FOREIGN KEY (parent_category_id) REFERENCES book_category_entity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_category_entity DROP FOREIGN KEY FK_85771C68796A8F92');
        $this->addSql('ALTER TABLE book_category_entity ADD CONSTRAINT FK_85771C68796A8F92 FOREIGN KEY (parent_category_id) REFERENCES book_category_entity (id)');
    }
}
