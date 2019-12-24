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
final class Version20191224081133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE shopping_list_item_log_entry (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', item_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', list_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, quantity VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_3E5B8CFD126F525E (item_id), INDEX IDX_3E5B8CFD3DAE168B (list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shopping_list_item_log_entry ADD CONSTRAINT FK_3E5B8CFD126F525E FOREIGN KEY (item_id) REFERENCES shopping_list_item (id)');
        $this->addSql('ALTER TABLE shopping_list_item_log_entry ADD CONSTRAINT FK_3E5B8CFD3DAE168B FOREIGN KEY (list_id) REFERENCES shopping_list (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE shopping_list_item_log_entry');
    }
}
