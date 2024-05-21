<?php

declare(strict_types=1);

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200103234043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE shopping_list_item_store (shopping_list_item_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', store_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_A48FAB721CAF1D95 (shopping_list_item_id), INDEX IDX_A48FAB72B092A811 (store_id), PRIMARY KEY(shopping_list_item_id, store_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shopping_list_item_store ADD CONSTRAINT FK_A48FAB721CAF1D95 FOREIGN KEY (shopping_list_item_id) REFERENCES shopping_shopping_list_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shopping_list_item_store ADD CONSTRAINT FK_A48FAB72B092A811 FOREIGN KEY (store_id) REFERENCES shopping_store (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shopping_shopping_list_item DROP FOREIGN KEY FK_18F10CCEB092A811');
        $this->addSql('DROP INDEX IDX_18F10CCEB092A811 ON shopping_shopping_list_item');
        $this->addSql('ALTER TABLE shopping_shopping_list_item DROP store_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE shopping_list_item_store');
        $this->addSql('ALTER TABLE shopping_shopping_list_item ADD store_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE shopping_shopping_list_item ADD CONSTRAINT FK_18F10CCEB092A811 FOREIGN KEY (store_id) REFERENCES shopping_store (id)');
        $this->addSql('CREATE INDEX IDX_18F10CCEB092A811 ON shopping_shopping_list_item (store_id)');
    }
}
