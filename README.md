# richtext-extension

[![Deploy to WordPress.org](https://github.com/t-hamano/richtext-extension/actions/workflows/wp-plugin-deploy.yml/badge.svg)](https://github.com/t-hamano/richtext-extension/actions/workflows/wp-plugin-deploy.yml)

Adds useful decoration features to the Gutenberg RichText editor toolbar.

## Features

### Highlighter

![Highlighter](https://github.com/t-hamano/richtext-extension/blob/images/highlighter_option.png)

![Highlighter](https://github.com/t-hamano/richtext-extension/blob/images/highlighter_editor.png)

Draw a marker on the inline text.

You can set the color, thickness, and opacity of the marker to you can create various marker styles.

Up to four types of settings can be made.

### Font size

![Font size](https://github.com/t-hamano/richtext-extension/blob/images/fontsize_option.png)

![Font size](https://github.com/t-hamano/richtext-extension/blob/images/fontsize_editor.png)

Change inline text size.

The size is specified as a percentage of the base font size, and up to four types of settings can be made.

### Underline

Create underline.

**Note: The underline specifications have changed from version 2.0.0. Try clearing the format if existing underlines do not work.**

### Clear format

Removes all formatting.

## How to build

Download this folder in your plugins directory.

```
$ npm install
$ npm run build
```

## Author

[Aki Hamano (Github)](https://github.com/t-hamano)
