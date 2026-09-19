/** Seed a current Craft entry containing a realistic PHP example in a Prism field. */

use craft\elements\Entry;
use craft\fieldlayoutelements\CustomField;
use craft\helpers\Json;
use craft\models\EntryType;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use verbb\prism\fields\PrismField;

$fields = Craft::$app->getFields();
$entries = Craft::$app->getEntries();
$elements = Craft::$app->getElements();
$site = Craft::$app->getSites()->getPrimarySite();
$fieldHandle = 'codeExample';
$sectionHandle = 'screenshotCodeExamples';

$field = $fields->getFieldByHandle($fieldHandle);

if (!$field instanceof PrismField) {
    $field = new PrismField([
        'name' => 'PHP example',
        'handle' => $fieldHandle,
    ]);
}

$field->editorTheme = 'prism-okaidia';
$field->editorLanguage = 'php';
$field->editorHeight = '14';
$field->editorTabWidth = '4';
$field->editorLineNumbers = true;
$field->editorLanguageFiles = [];

if (!$fields->saveField($field)) {
    throw new RuntimeException('Unable to save Prism field: ' . Json::encode($field->getErrors()));
}

$section = $entries->getSectionByHandle($sectionHandle);

if (!$section) {
    $entryType = new EntryType([
        'name' => 'Code examples',
        'handle' => $sectionHandle . 'Type',
        'hasTitleField' => true,
    ]);

    $layout = new FieldLayout(['type' => Entry::class]);
    $tab = new FieldLayoutTab([
        'name' => Craft::t('app', 'Content'),
        'layout' => $layout,
    ]);
    $tab->setElements([new CustomField($field)]);
    $layout->setTabs([$tab]);
    $entryType->setFieldLayout($layout);

    if (!$entries->saveEntryType($entryType)) {
        throw new RuntimeException('Unable to save Prism entry type: ' . Json::encode($entryType->getErrors()));
    }

    $section = new Section([
        'name' => 'Code examples',
        'handle' => $sectionHandle,
        'type' => Section::TYPE_CHANNEL,
    ]);
    $section->setEntryTypes([$entryType]);
    $section->setSiteSettings([
        new Section_SiteSettings([
            'siteId' => $site->id,
            'enabledByDefault' => true,
            'hasUrls' => false,
        ]),
    ]);

    if (!$entries->saveSection($section)) {
        throw new RuntimeException('Unable to save Prism section: ' . Json::encode($section->getErrors()));
    }
}

$entryType = $entries->getEntryTypesBySectionId($section->id)[0] ?? null;

if (!$entryType) {
    throw new RuntimeException('Prism section has no entry type.');
}

$code = <<<'PHP'
<?php

use craft\elements\Entry;

$articles = Entry::find()
    ->section('articles')
    ->with(['featuredImage'])
    ->orderBy(['postDate' => SORT_DESC])
    ->limit(6)
    ->all();

foreach ($articles as $article) {
    echo $article->title;
}
PHP;

$entry = Entry::find()
    ->sectionId($section->id)
    ->slug('recent-articles-query')
    ->siteId($site->id)
    ->status(null)
    ->one();

if (!$entry) {
    $entry = new Entry([
        'sectionId' => $section->id,
        'typeId' => $entryType->id,
        'siteId' => $site->id,
        'slug' => 'recent-articles-query',
        'enabled' => true,
    ]);
}

$entry->title = 'Recent articles query';
$entry->setFieldValue($fieldHandle, $code);

if (!$elements->saveElement($entry)) {
    throw new RuntimeException('Unable to save Prism screenshot entry: ' . Json::encode($entry->getErrors()));
}

echo Json::encode([
    'entryEditRoute' => parse_url((string)$entry->getCpEditUrl(), PHP_URL_PATH),
], JSON_THROW_ON_ERROR);
