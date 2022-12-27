<?php
namespace verbb\prism\migrations;

use verbb\prism\fields\PrismField;

use Craft;
use craft\db\Migration;

class m221217_000000_verbb_migration extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->update('{{%fields}}', ['type' => PrismField::class], ['type' => 'thejoshsmith\prismsyntaxhighlighting\fields\PrismSyntaxHighlightingField']);

        // Don't make the same config changes twice
        $projectConfig = Craft::$app->getProjectConfig();
        $schemaVersion = $projectConfig->get('plugins.prism.schemaVersion', true);

        if (version_compare($schemaVersion, '2.0.0', '>=')) {
            return true;
        }

        $fields = $projectConfig->get('fields') ?? [];

        $fieldMap = [
            'thejoshsmith\prismsyntaxhighlighting\fields\PrismSyntaxHighlightingField' => PrismField::class,
        ];

        $fieldsService = Craft::$app->getFields();

        foreach ($fields as $fieldUid => $field) {
            $type = $field['type'] ?? null;

            if (isset($fieldMap[$type])) {
                $field['type'] = $fieldMap[$type];

                $projectConfig->set('fields.' . $fieldUid, $field);

                // Resave all fields to update the cached asset bundle paths
                $fieldObject = $fieldsService->getFieldByHandle($field['handle']);

                if ($fieldObject) {
                    $fieldsService->saveField($fieldObject);
                }
            }
        }

        return true;
    }

    public function safeDown(): bool
    {
        echo "m221217_000000_verbb_migration cannot be reverted.\n";
        return false;
    }
}