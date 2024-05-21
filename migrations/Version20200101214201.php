<?php

declare(strict_types=1);

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200101214201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE shopping_store (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, address LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_shopping_list (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', account_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_4F144C4E9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_account (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', email VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_shopping_list_log_entry (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', item_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', list_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, quantity VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_B4A42A71126F525E (item_id), INDEX IDX_B4A42A713DAE168B (list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_shopping_list_item (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', list_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', store_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, done_at DATETIME DEFAULT NULL, quantity VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_18F10CCE3DAE168B (list_id), INDEX IDX_18F10CCEB092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shopping_shopping_list ADD CONSTRAINT FK_4F144C4E9B6B5FBA FOREIGN KEY (account_id) REFERENCES shopping_account (id)');
        $this->addSql('ALTER TABLE shopping_shopping_list_log_entry ADD CONSTRAINT FK_B4A42A71126F525E FOREIGN KEY (item_id) REFERENCES shopping_shopping_list_item (id)');
        $this->addSql('ALTER TABLE shopping_shopping_list_log_entry ADD CONSTRAINT FK_B4A42A713DAE168B FOREIGN KEY (list_id) REFERENCES shopping_shopping_list (id)');
        $this->addSql('ALTER TABLE shopping_shopping_list_item ADD CONSTRAINT FK_18F10CCE3DAE168B FOREIGN KEY (list_id) REFERENCES shopping_shopping_list (id)');
        $this->addSql('ALTER TABLE shopping_shopping_list_item ADD CONSTRAINT FK_18F10CCEB092A811 FOREIGN KEY (store_id) REFERENCES shopping_store (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE shopping_shopping_list_item DROP FOREIGN KEY FK_18F10CCEB092A811');
        $this->addSql('ALTER TABLE shopping_shopping_list_log_entry DROP FOREIGN KEY FK_B4A42A713DAE168B');
        $this->addSql('ALTER TABLE shopping_shopping_list_item DROP FOREIGN KEY FK_18F10CCE3DAE168B');
        $this->addSql('ALTER TABLE shopping_shopping_list DROP FOREIGN KEY FK_4F144C4E9B6B5FBA');
        $this->addSql('ALTER TABLE shopping_shopping_list_log_entry DROP FOREIGN KEY FK_B4A42A71126F525E');
        $this->addSql('DROP TABLE shopping_store');
        $this->addSql('DROP TABLE shopping_shopping_list');
        $this->addSql('DROP TABLE shopping_account');
        $this->addSql('DROP TABLE shopping_shopping_list_log_entry');
        $this->addSql('DROP TABLE shopping_shopping_list_item');
    }
}
