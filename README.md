![Icon](resources/img/icon.png?raw=true "Icon") 
# Prism Syntax Highlighting plugin for Craft CMS 3.x

Adds a new field type that provides syntax highlighting capabilities using PrismJS.

![Themes](resources/img/themes_demo_1.png?raw=true "Themes") 

## Prism Syntax Highlighting Overview

This plugin enables a new field type called "Prism Syntax Highlighter" within the control panel. When used in an entry, a code block will appear and you can enter highlighted text from a choice of over ~170 different languages.

Code blocks are fully themeable, and it includes all of Prism's default themes. Additional themes can also be added by configuring them in the settings area. You can even have multiple themes and languages running at once!

Features:
- Syntax highlighting in realtime
- Over 170 different supported syntaxes
- Ships with 8 default themes, and supports adding custom themes
- Line numbers
- Configurable tab widths and editor height
- Shortcut keys (indentation and undo/redo)
- Support for Matrices & Supertables
- Works with Live Preview

Coming soon:
- Redactor support. Insert code blocks within your content
- Automatic rendering on the front end (including styles + scripts)

![Twig](resources/img/twig.png?raw=true "Twig")

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require thejoshsmith/craft-prism-syntax-highlighting

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Prism Syntax Highlighting.

## Configuring Prism Syntax Highlighting

General settings are configurable from the plugin settings screen, and the following defaults can be set:

- Theme
- Syntax
- Editor Height
- Tab Width
- Display Line Numbers
- Custom Theme Directory

![Settings](resources/img/settings.png?raw=true "Settings")

### Themes

Themes are configurable using the dropdown select menu. To customize which themes appear in the select menu, the plugin config file must be edited. Copy the `vendor/thejoshsmith/craft-prism-syntax-highlighter/src/config/prismsyntaxhighlighter.example.php` file to `config/prismsyntaxhighlighter.php` and open it up.

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


## Using Prism Syntax Highlighting

Simply create a new field type of "Prism Syntax Highlighting", assign to a section and start writing!

## Prism Syntax Highlighting Roadmap

Some things to do, and ideas for potential features:

* Add support for custom languages.
* Create a Redactor plugin that allows you to write code inline of content.
* Create a Twig function that auto-renders the same style'd code block on the front end, as the control panel.

Brought to you by [Josh Smith <me@joshsmith.dev>](https://www.joshsmith.dev)
