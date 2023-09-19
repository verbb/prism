# Configuration
Create a `prism.php` file under your `/config` directory with the following options available to you. You can also use multi-environment options to change these per environment.

The below shows the defaults already used by Prism, so you don't need to add these options unless you want to modify the values.

```php
<?php

return [
    '*' => [
        'editorTheme' => '',
        'editorLanguage' => '',
        'editorThemes' => [],
        'editorLanguages' => [],
        'editorHeight' => '4',
        'editorTabWidth' => '4',
        'editorLineNumbers' => false,
        'customThemesDir' => '',
    ],
];
```

## Configuration options
- `editorTheme` - Choose an editor theme.
- `editorLanguage` - Choose a default syntax to highlight text with.
- `editorThemes` - A collection of themes available for the field.
- `editorLanguages` - A collection of languages available for the field.
- `editorHeight` - Set the default number of rows the editor will be displayed at.
- `editorTabWidth` - Set the tab width. This uses the experimental CSS property `tab-size`.
- `editorLineNumbers` - Choose whether line numbers are displayed or not.
- `customThemesDir` - The path to the theme directory where your custom CSS files are located.

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
