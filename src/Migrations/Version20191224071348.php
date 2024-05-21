<?php

declare(strict_types=1);

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2019 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191224071348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE shopping_list (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', account_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_3DC1A4599B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE account (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_list_item (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', list_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, done_at DATETIME DEFAULT NULL, quantity VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_4FB1C2243DAE168B (list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shopping_list ADD CONSTRAINT FK_3DC1A4599B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE shopping_list_item ADD CONSTRAINT FK_4FB1C2243DAE168B FOREIGN KEY (list_id) REFERENCES shopping_list (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE shopping_list_item DROP FOREIGN KEY FK_4FB1C2243DAE168B');
        $this->addSql('ALTER TABLE shopping_list DROP FOREIGN KEY FK_3DC1A4599B6B5FBA');
        $this->addSql('DROP TABLE shopping_list');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE shopping_list_item');
    }
}
