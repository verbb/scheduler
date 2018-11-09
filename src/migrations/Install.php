<?php

namespace supercool\scheduler\migrations;

use craft\db\Migration;

class Install extends Migration
{

  public function safeUp()
  {
    $this->createTables();
  }


  public function safeDown()
  {
    $this->dropTableIfExists('{{%scheduler_jobs}}');
  }


  /**
   * Creates tables
   *
   * @return void
   */
  private function createTables()
  {
    // create the supercal_events table
    $this->createTable('{{%scheduler_jobs}}', [
      'id' => $this->primaryKey(),
      'type' => $this->string()->notNull(),
      'date' => $this->dateTime()->notNull(),
      'context' => $this->string()->defaultValue('global'),
      'settings' => $this->text(),
      'dateCreated' => $this->dateTime()->notNull(),
      'dateUpdated' => $this->dateTime()->notNull(),
      'uid' => $this->uid()
    ]);
  }

}
