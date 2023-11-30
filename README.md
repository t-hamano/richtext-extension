# richtext-extension

[![Tests](https://github.com/t-hamano/richtext-extension/actions/workflows/run-test.yml/badge.svg)](https://github.com/t-hamano/richtext-extension/actions/workflows/run-test.yml)
[![Tests and Deploy](https://github.com/t-hamano/richtext-extension/actions/workflows/run-test-and-deploy.yml/badge.svg)](https://github.com/t-hamano/richtext-extension/actions/workflows/run-test-and-deploy.yml)

Adds useful decoration features to the Gutenberg RichText editor toolbar.

## Features

### Highlighter

![Highlighter](https://raw.githubusercontent.com/t-hamano/richtext-extension/main/.wordpress-org/screenshot-4.png)

![Highlighter](https://raw.githubusercontent.com/t-hamano/richtext-extension/main/.wordpress-org/screenshot-1.png)

Draw a marker on the inline text.

You can set the color, thickness, and opacity of the marker to you can create various marker styles.

Up to four types of settings can be made.

### Font size

![Font size](https://raw.githubusercontent.com/t-hamano/richtext-extension/main/.wordpress-org/screenshot-5.png)

![Font size](https://raw.githubusercontent.com/t-hamano/richtext-extension/main/.wordpress-org/screenshot-2.png)

Change inline text size.

The size is specified as a percentage of the base font size, and up to four types of settings can be made.

### Underline

Create underline.

**Note: The underline specifications have changed from version 2.0.0. Try clearing the format if existing underlines do not work.**

### Clear format

Removes all formatting.

## How to build

Download this folder in your plugins directory.

```sh
npm install
npm run build
```

## Author

[Aki Hamano (Github)](https://github.com/t-hamano)
