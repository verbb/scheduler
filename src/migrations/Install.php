<?php
namespace verbb\scheduler\migrations;

use craft\db\Migration;

class Install extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->createTables();

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropTables();

        return true;
    }

    public function createTables(): void
    {
        $this->archiveTableIfExists('{{%scheduler_jobs}}');
        $this->createTable('{{%scheduler_jobs}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string()->notNull(),
            'date' => $this->dateTime()->notNull(),
            'context' => $this->string()->defaultValue('global'),
            'settings' => $this->text(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);
    }

    public function dropTables(): void
    {
        $this->dropTableIfExists('{{%scheduler_jobs}}');
    }

}
