<?php
namespace verbb\scheduler\migrations;

use verbb\scheduler\fields\ScheduleJob;

use Craft;
use craft\db\Migration;

class m231228_000000_verbb_migration extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->update('{{%fields}}', ['type' => ScheduleJob::class], ['type' => 'supercool\scheduler\fields\ScheduleJob']);

        // Don't make the same config changes twice
        $projectConfig = Craft::$app->getProjectConfig();
        $schemaVersion = $projectConfig->get('plugins.scheduler.schemaVersion', true);

        if (version_compare($schemaVersion, '1.1.0', '>=')) {
            return true;
        }

        $fields = $projectConfig->get('fields') ?? [];

        $fieldMap = [
            'supercool\\scheduler\\fields\\ScheduleJob' => ScheduleJob::class,
        ];

        foreach ($fields as $fieldUid => $field) {
            $type = $field['type'] ?? null;

            if (isset($fieldMap[$type])) {
                $field['type'] = $fieldMap[$type];

                $projectConfig->set('fields.' . $fieldUid, $field);
            }
        }

        return true;
    }

    public function safeDown(): bool
    {
        echo "m231228_000000_verbb_migration cannot be reverted.\n";
        return false;
    }
}