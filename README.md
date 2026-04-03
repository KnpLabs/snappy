<div align="center">
    <img src="snappy_banner.png" alt="Snappy Banner">
    <p style="text-align: center;"><b>A PHP library for generating PDF files from HTML</b></p>
</div>

<p align="center">
    <a href=""><img src="https://github.com/KnpLabs/snappy/actions/workflows/build.yaml/badge.svg" alt="Build Status"></a>
    <a href="#contributors"><img src="https://img.shields.io/github/contributors/KnpLabs/snappy" alt="Contributors"></a>
    <a href="#license"><img src="https://img.shields.io/github/license/KnpLabs/snappy" alt="License"></a>
    <a href="https://phpunit.de/index.html"><img src="https://img.shields.io/badge/tested%20with-phpunit-green" alt="Tested with PHPUnit"></a>
</p>

<hr/>

## About Snappy

Snappy is a PHP library that allows you to generate PDF files from HTML by leveraging different backends such as Dompdf, WkHtmlToPdf, or Chrome Headless.

## Table of contents

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Usage](#usage)
- [Frontends](#frontends)
- [Options](#options)
- [Symfony Bundle Configuration](#symfony-bundle-configuration)
- [Creating Custom Adapters](#creating-custom-adapters)
- [Chrome Headless Notes](#chrome-headless-notes)
- [Bugs and Support](#bugs-and-support)
- [License](#license)
- [Contributing](#contributing)
- [Contributors](#contributors)

## Installation

Install the core library:

```bash
composer require knplabs/snappy
```

Then install the adapter for your preferred backend:

```bash
# For Dompdf
composer require dompdf/dompdf

# For WkHtmlToPdf (requires wkhtmltopdf binary)
# No additional package needed, but binary must be installed on your system.

# For Chrome Headless (requires google-chrome binary)
# No additional package needed, but binary must be installed on your system.
```

## Quick Start

### Using Dompdf

```php
use KNPLabs\Snappy\Backend\Dompdf\DompdfAdapter;
use KNPLabs\Snappy\Backend\Dompdf\DompdfFactory;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Frontend\HtmlToPdf;

$factory = new DompdfFactory($streamFactory);
$adapter = $factory->create(Options::create());
$frontend = new HtmlToPdf($adapter, $streamFactory);

$pdf = $frontend->generateFromHtml('<h1>Hello World</h1>');
```

### Using WkHtmlToPdf

```php
use KNPLabs\Snappy\Backend\WkHtmlToPdf\WkHtmlToPdfFactory;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Frontend\HtmlToPdf;

$factory = new WkHtmlToPdfFactory('/usr/local/bin/wkhtmltopdf', 60, $streamFactory, $uriFactory);
$adapter = $factory->create(Options::create());
$frontend = new HtmlToPdf($adapter, $streamFactory);

$pdf = $frontend->generateFromHtml('<h1>Hello World</h1>');
```

### Using Chrome Headless

```php
use KNPLabs\Snappy\Backend\ChromeHeadless\ChromeHeadlessFactory;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Frontend\HtmlToPdf;

$factory = new ChromeHeadlessFactory('google-chrome', 60, $streamFactory, $uriFactory);
$adapter = $factory->create(Options::create());
$frontend = new HtmlToPdf($adapter, $streamFactory);

$pdf = $frontend->generateFromHtml('<h1>Hello World</h1>');
```

## Usage

Snappy provides multiple frontends to handle different input types. Each frontend acts as a smart conversion layer that delegates to the underlying adapter.

### HTML String

```php
use KNPLabs\Snappy\Core\Frontend\HtmlToPdf;

$frontend = new HtmlToPdf($adapter, $streamFactory);
$pdf = $frontend->generateFromHtml('<html>...</html>');
```

### HTML File

```php
use KNPLabs\Snappy\Core\Frontend\HtmlFileToPdf;

$frontend = new HtmlFileToPdf($adapter);
$pdf = $frontend->generateFromHtmlFile(new \SplFileInfo('path/to/file.html'));
```

### URI

```php
use KNPLabs\Snappy\Core\Frontend\UriToPdf;

$frontend = new UriToPdf($adapter);
$pdf = $frontend->generateFromUri($uriFactory->createUri('https://google.com'));
```

### DOMDocument

```php
use KNPLabs\Snappy\Core\Frontend\DOMDocumentToPdf;

$frontend = new DOMDocumentToPdf($adapter);
$pdf = $frontend->generateFromDOMDocument($domDocument);
```

### Stream

```php
use KNPLabs\Snappy\Core\Frontend\StreamToPdf;

$frontend = new StreamToPdf($adapter);
$pdf = $frontend->generateFromStream($htmlStream);
```

## Frontends

Frontends are the public API of Snappy. They are responsible for normalizing the input and calling the correct method on the backend adapter.

If an adapter doesn't natively support a specific input (e.g., Dompdf doesn't support URIs), the frontend will attempt to convert the input to a format the adapter supports (e.g., fetching the URI content and passing it as HTML).

## Options

You can configure the PDF generation using the `Options` class.

### Page Orientation

```php
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;

$options = Options::create()
    ->withPageOrientation(PageOrientation::Landscape);

$adapter = $adapter->withOptions($options);
```

### Backend Specific Options

Each backend supports extra options.

#### Dompdf

```php
$options = Options::create()
    ->withExtraOptions([
        'construct' => ['isRemoteEnabled' => true],
        'output' => ['compress' => 0],
    ]);
```

#### Chrome Headless

```php
use KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption\NoSandbox;
use KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption\WindowSize;

$options = Options::create()
    ->withExtraOptions([
        new NoSandbox(),
        new WindowSize('1920,1080'),
    ]);
```

## Symfony Bundle Configuration

If you use the Symfony bundle, you can configure your backends in `config/packages/snappy.yaml`.

### Dompdf

```yaml
snappy:
    backends:
        default:
            dompdf:
                options:
                    pageOrientation: portrait
                    extraOptions:
                        construct:
                            isRemoteEnabled: true
                        output:
                            compress: 1
```

### WkHtmlToPdf

```yaml
snappy:
    backends:
        default:
            wkhtmltopdf:
                binary: /usr/local/bin/wkhtmltopdf
                timeout: 60
                options:
                    pageOrientation: landscape
```

### Chrome Headless

```yaml
snappy:
    backends:
        default:
            chrome_headless:
                binary: google-chrome
                timeout: 120
                options:
                    extraOptions:
                        - KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption\NoSandbox
```

## Creating Custom Adapters

To create a custom adapter, you must implement the `KNPLabs\Snappy\Core\Backend\Adapter` interface or one of its specialized versions:

- `KNPLabs\Snappy\Core\Backend\Adapter\HtmlToPdf`
- `KNPLabs\Snappy\Core\Backend\Adapter\HtmlFileToPdf`
- `KNPLabs\Snappy\Core\Backend\Adapter\UriToPdf`
- `KNPLabs\Snappy\Core\Backend\Adapter\DOMDocumentToPdf`
- `KNPLabs\Snappy\Core\Backend\Adapter\StreamToPdf`

You should also implement a `KNPLabs\Snappy\Core\Backend\Factory` to instantiate your adapter.

## Chrome Headless Notes

- **Orientation**: Chrome Headless ignores the orientation flag for PDF generation in some versions. It is recommended to use CSS `@page { size: landscape; }` in your HTML for reliable results.
- **Docker/CI**: When running Chrome in a container, you often need the `--no-sandbox` and `--disable-dev-shm-usage` flags.
- **Stderr**: Chrome might output DevTools listening messages to stderr. This is normal and does not necessarily indicate a failure.

## Bugs and Support

If you have any questions or problems with Snappy, please open a **detailed** issue and we will be happy to help you !

## Contributing

Any contribution is welcome, whether it is a bug report, a feature request, a pull request or simply a question.

### Contributing Guide

Read the [CONTRIBUTING.md](CONTRIBUTING.md) file to learn how to contribute to Snappy.

### Code of Conduct

This project and everyone participating in it is governed by the [Code of Conduct](CODE_OF_CONDUCT.md).

Contributing to Snappy means you agree to uphold this code.

## Contributors

![Contributors][gh-contributors-image]

[gh-contributors-image]: https://contrib.rocks/image?repo=KnpLabs/snappy


## License

Snappy is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.
