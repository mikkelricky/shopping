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
final class Version20200103222439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE shopping_store ADD account_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE shopping_store ADD CONSTRAINT FK_FEF5202D9B6B5FBA FOREIGN KEY (account_id) REFERENCES shopping_account (id)');
        $this->addSql('CREATE INDEX IDX_FEF5202D9B6B5FBA ON shopping_store (account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE shopping_store DROP FOREIGN KEY FK_FEF5202D9B6B5FBA');
        $this->addSql('DROP INDEX IDX_FEF5202D9B6B5FBA ON shopping_store');
        $this->addSql('ALTER TABLE shopping_store DROP account_id');
    }
}
