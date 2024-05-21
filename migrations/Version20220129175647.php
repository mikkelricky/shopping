<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220129175647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Migrate old uuids to new uuids.
        // https://stackoverflow.com/questions/69772365/migrating-old-guid-symfony-5-2-to-new-uuid-component-symfony-5-3-as-en

        // Clean up invalid datetimes.
        $this->addSql('UPDATE shopping_account SET created_at = NOW(), updated_at = NOW()');
        $this->addSql('UPDATE shopping_user SET created_at = NOW(), updated_at = NOW()');

        // Add temporary uuid columns.
        $this->addSql('ALTER TABLE shopping_account ADD id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_location ADD id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD store_id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_shopping_list ADD id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD account_id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_shopping_list_item ADD id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD list_id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_list_item_store ADD shopping_list_item_id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD store_id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_shopping_list_log_entry ADD id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD item_id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD list_id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_store ADD id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD account_id_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_user ADD id_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');

        // Migrate ids to binary uuids.
        $this->addSql('UPDATE shopping_account SET id_uuid = UNHEX(REPLACE(id, "-", ""))');
        $this->addSql('UPDATE shopping_location SET id_uuid = UNHEX(REPLACE(id, "-", "")), store_id_uuid = UNHEX(REPLACE(store_id, "-", ""))');
        $this->addSql('UPDATE shopping_shopping_list SET id_uuid = UNHEX(REPLACE(id, "-", "")), account_id_uuid = UNHEX(REPLACE(account_id, "-", ""))');
        $this->addSql('UPDATE shopping_shopping_list_item SET id_uuid = UNHEX(REPLACE(id, "-", "")), list_id_uuid = UNHEX(REPLACE(list_id, "-", ""))');
        $this->addSql('UPDATE shopping_list_item_store SET shopping_list_item_id_uuid = UNHEX(REPLACE(shopping_list_item_id, "-", "")), store_id_uuid = UNHEX(REPLACE(store_id, "-", ""))');
        $this->addSql('UPDATE shopping_shopping_list_log_entry SET id_uuid = UNHEX(REPLACE(id, "-", "")), item_id_uuid = UNHEX(REPLACE(item_id, "-", "")), list_id_uuid = UNHEX(REPLACE(list_id, "-", ""))');
        $this->addSql('UPDATE shopping_store SET id_uuid = UNHEX(REPLACE(id, "-", "")), account_id_uuid = UNHEX(REPLACE(account_id, "-", ""))');
        $this->addSql('UPDATE shopping_user SET id_uuid = UNHEX(REPLACE(id, "-", ""))');

        // Drop foreign key constraints.
        $this->addSql('ALTER TABLE shopping_shopping_list DROP FOREIGN KEY FK_4F144C4E9B6B5FBA');
        $this->addSql('ALTER TABLE shopping_store DROP FOREIGN KEY FK_FEF5202D9B6B5FBA');
        $this->addSql('ALTER TABLE shopping_location DROP FOREIGN KEY FK_E08B1396B092A811');
        $this->addSql('ALTER TABLE shopping_shopping_list_item DROP FOREIGN KEY FK_18F10CCE3DAE168B');
        $this->addSql('ALTER TABLE shopping_shopping_list_log_entry DROP FOREIGN KEY FK_B4A42A713DAE168B');
        $this->addSql('ALTER TABLE shopping_list_item_store DROP FOREIGN KEY FK_A48FAB721CAF1D95');
        $this->addSql('ALTER TABLE shopping_shopping_list_log_entry DROP FOREIGN KEY FK_B4A42A71126F525E');
        $this->addSql('ALTER TABLE shopping_list_item_store DROP FOREIGN KEY FK_A48FAB72B092A811');

        // Drop the id columns.
        $this->addSql('ALTER TABLE shopping_account DROP id');
        $this->addSql('ALTER TABLE shopping_location DROP id, DROP store_id');
        $this->addSql('ALTER TABLE shopping_shopping_list DROP id, DROP account_id');
        $this->addSql('ALTER TABLE shopping_shopping_list_item DROP id, DROP list_id');
        $this->addSql('ALTER TABLE shopping_list_item_store DROP shopping_list_item_id, DROP store_id');
        $this->addSql('ALTER TABLE shopping_shopping_list_log_entry DROP id, DROP item_id, DROP list_id');
        $this->addSql('ALTER TABLE shopping_store DROP id, DROP account_id');
        $this->addSql('ALTER TABLE shopping_user DROP id');

        // Rename uuid columns.
        $this->addSql('ALTER TABLE shopping_account CHANGE id_uuid id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_location CHANGE id_uuid id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE store_id_uuid store_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_shopping_list CHANGE id_uuid id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE account_id_uuid account_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_shopping_list_item CHANGE id_uuid id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE list_id_uuid list_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_list_item_store CHANGE shopping_list_item_id_uuid shopping_list_item_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE store_id_uuid store_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_shopping_list_log_entry CHANGE id_uuid id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE item_id_uuid item_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE list_id_uuid list_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_store CHANGE id_uuid id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE account_id_uuid account_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping_user CHANGE id_uuid id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');

        // Update user id.
        $this->addSql('UPDATE shopping_user SET id = UNHEX(REPLACE(UUID(), "-", ""))');

        // Set primary keys.
        $this->addSql('ALTER TABLE shopping_account ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE shopping_location ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE shopping_shopping_list ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE shopping_shopping_list_item ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE shopping_list_item_store ADD PRIMARY KEY (shopping_list_item_id, store_id)');
        $this->addSql('ALTER TABLE shopping_shopping_list_log_entry ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE shopping_store ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE shopping_user ADD PRIMARY KEY (id)');

        // Add foreign key constraints.
        $this->addSql('ALTER TABLE shopping_location ADD CONSTRAINT FK_E08B1396B092A811 FOREIGN KEY (store_id) REFERENCES shopping_store (id)');
        $this->addSql('ALTER TABLE shopping_shopping_list ADD CONSTRAINT FK_4F144C4E9B6B5FBA FOREIGN KEY (account_id) REFERENCES shopping_account (id)');
        $this->addSql('ALTER TABLE shopping_shopping_list_item ADD CONSTRAINT FK_18F10CCE3DAE168B FOREIGN KEY (list_id) REFERENCES shopping_shopping_list (id)');
        $this->addSql('ALTER TABLE shopping_list_item_store ADD CONSTRAINT FK_A48FAB721CAF1D95 FOREIGN KEY (shopping_list_item_id) REFERENCES shopping_shopping_list_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shopping_list_item_store ADD CONSTRAINT FK_A48FAB72B092A811 FOREIGN KEY (store_id) REFERENCES shopping_store (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shopping_shopping_list_log_entry ADD CONSTRAINT FK_B4A42A71126F525E FOREIGN KEY (item_id) REFERENCES shopping_shopping_list_item (id)');
        $this->addSql('ALTER TABLE shopping_shopping_list_log_entry ADD CONSTRAINT FK_B4A42A713DAE168B FOREIGN KEY (list_id) REFERENCES shopping_shopping_list (id)');
        $this->addSql('ALTER TABLE shopping_store ADD CONSTRAINT FK_FEF5202D9B6B5FBA FOREIGN KEY (account_id) REFERENCES shopping_account (id)');

        // Add indexes.
        $this->addSql('CREATE INDEX IDX_E08B1396B092A811 ON shopping_location (store_id)');
        $this->addSql('CREATE INDEX IDX_4F144C4E9B6B5FBA ON shopping_shopping_list (account_id)');
        $this->addSql('CREATE INDEX IDX_18F10CCE3DAE168B ON shopping_shopping_list_item (list_id)');
        $this->addSql('CREATE INDEX IDX_A48FAB721CAF1D95 ON shopping_list_item_store (shopping_list_item_id)');
        $this->addSql('CREATE INDEX IDX_A48FAB72B092A811 ON shopping_list_item_store (store_id)');
        $this->addSql('CREATE INDEX IDX_B4A42A71126F525E ON shopping_shopping_list_log_entry (item_id)');
        $this->addSql('CREATE INDEX IDX_B4A42A713DAE168B ON shopping_shopping_list_log_entry (list_id)');
        $this->addSql('CREATE INDEX IDX_FEF5202D9B6B5FBA ON shopping_store (account_id)');
    }

    public function down(Schema $schema): void
    {
        // There is no going back …
    }
}
