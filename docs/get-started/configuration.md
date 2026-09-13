# Configuration

You can customise Prism’s settings using a PHP configuration file. This is optional: each setting has a default, so you only need to include the values you want to change.

To override a setting, create `prism.php` in your Craft project’s `/config` directory and return an array of setting names and values. For example, the following will show line numbers in the editor:

```php
<?php

return [
    'editorLineNumbers' => true,
];
```

All other settings keep their defaults. Add any further settings you want to change to the same array. The options below explain the available settings and their defaults.

## Configuration Options

::: reference
### `editorTheme`

**Type:** `string` · **Default:** `''`

Choose an editor theme.
:::


::: reference
### `editorLanguage`

**Type:** `string` · **Default:** `''`

Choose a default syntax to highlight text with.
:::


::: reference
### `editorThemes`

**Type:** `array` · **Default:** `[]`

A collection of themes available for the field.
:::


::: reference
### `editorLanguages`

**Type:** `array` · **Default:** `[]`

A collection of languages available for the field.
:::


::: reference
### `editorHeight`

**Type:** `string` · **Default:** `'4'`

Set the default number of rows the editor will be displayed at.
:::


::: reference
### `editorTabWidth`

**Type:** `string` · **Default:** `'4'`

Set the tab width. This uses the experimental CSS property `tab-size`.
:::


::: reference
### `editorLineNumbers`

**Type:** `bool` · **Default:** `false`

Choose whether line numbers are displayed or not.
:::


::: reference
### `customThemesDir`

**Type:** `string` · **Default:** `''`

The path to the theme directory where your custom CSS files are located.
:::


### Themes
You can show specific themes by listing the style handles e.g. `['prism-coy','prism-dark']`. Custom themes are specified by adding a key => value entry e.g. `['prism-my-custom-theme' => 'My Custom Title']`.

```php
'themes' => ['*', 'prism-my-custom-theme' => 'My Custom Title'],
```

Note: Custom themes must follow these rules:
- Exist in a directory specified on the plugin settings screen
- Be named after the handle. E.g `prism-my-custom-theme.css`
- Be namespaced after the theme name e.g. `.prism-my-custom-theme .token { color: #000 }` - This is to prevent conflicts with Craft's styles

### Languages
Languages are configurable in the same way as themes. Use `['*']` to show all 177 languages, or just list the ones you'd like to show in the config. Custom languages aren't supported at this time, but may be in the future. 

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Prism.
