# Prism plugin for Craft CMS
A field type that provides syntax highlighting capabilities using PrismJS.

![Themes](screenshots/themes_demo_1.png?raw=true "Themes") 

This plugin enables a new field type called "Prism Syntax Highlighter" within the control panel. When used in an entry, a code block will appear and you can enter highlighted text from a choice of over ~170 different languages.

Code blocks are fully themeable, and it includes all of Prism's default themes. Additional themes can also be added by configuring them in the settings area. You can even have multiple themes and languages running at once!

Features:
- Syntax highlighting in realtime
- Over 170 different supported syntaxes
- Ships with 8 default themes, and supports adding custom themes
- Line numbers
- Configurable tab widths and editor height
- Shortcut keys (indentation and undo/redo)
- Support for Matrix & Super Table
- Works with Live Preview

![Twig](screenshots/twig.png?raw=true "Twig")

## Installation
You can install Prism via the plugin store, or through Composer.

### Craft Plugin Store
To install **Prism**, navigate to the _Plugin Store_ section of your Craft control panel, search for `Prism`, and click the _Try_ button.

### Composer
You can also add the package to your project using Composer and the command line.

1. Open your terminal and go to your Craft project:
```shell
cd /path/to/project
```

2. Then tell Composer to require the plugin, and Craft to install it:
```shell
composer require verbb/prism && php craft plugin/install prism
```

## Configuring Prism
General settings are configurable from the plugin settings screen, and the following defaults can be set:

- Theme
- Syntax
- Editor Height
- Tab Width
- Display Line Numbers
- Custom Theme Directory

![Settings](screenshots/settings.png?raw=true "Settings")

### Themes
Themes are configurable using the dropdown select menu. To customize which themes appear in the select menu, the plugin config file must be edited. Copy the `vendor/verbb/prism/src/config.php` file to `config/prism.php` and open it up.

1. Edit the `themes` array key
2. Setting the array to `['*']` will load all default themes
3. You can show specific themes by listing the style handles e.g. `['prism-coy','prism-dark']`
4. Custom themes are specified by adding a key => value entry e.g. `['prism-my-custom-theme' => 'My Custom Title']`

```
'themes' => ['*', 'prism-my-custom-theme' => 'My Custom Title']
```

Note: Custom themes must follow these rules:

- Exist in a directory specified on the plugin settings screen
- Be named after the handle. E.g `prism-my-custom-theme.css`
- Be namespaced after the theme name e.g. `.prism-my-custom-theme .token { color: #000 }` - This is to prevent conflicts with Craft's styles

### Languages
Languages are configurable in the same way as themes. Use `['*']` to show all 177 languages, or just list the ones you'd like to show in the config. Custom languages aren't supported at this time, but may be in the future. 

## Credits
Originally created by the team at [Josh Smith <me@joshsmith.dev>](https://www.joshsmith.dev).

## Show your Support
Prism is licensed under the MIT license, meaning it will always be free and open source – we love free stuff! If you'd like to show your support to the plugin regardless, [Sponsor](https://github.com/sponsors/verbb) development.

<h2></h2>

<a href="https://verbb.io" target="_blank">
    <img width="100" src="https://verbb.io/assets/img/verbb-pill.svg">
</a>
