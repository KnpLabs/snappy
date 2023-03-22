# Snappy

![Build Status](https://github.com/KnpLabs/snappy/actions/workflows/build.yaml/badge.svg)
[![AppVeyor CI Build Status](https://ci.appveyor.com/api/projects/status/github/KnpLabs/snappy?branch=master&svg=true)](https://ci.appveyor.com/project/NiR-/snappy)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KnpLabs/snappy/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/KnpLabs/snappy/?branch=master)

Snappy is a PHP library allowing thumbnail, snapshot or PDF generation from a url or a html page.
It uses the excellent webkit-based [wkhtmltopdf and wkhtmltoimage](http://wkhtmltopdf.org/)
available on OSX, linux, windows.

You will have to download wkhtmltopdf `0.12.x` in order to use Snappy.

Please, check [FAQ](doc/faq.md) before opening a new issue. Snappy is a tiny wrapper around wkhtmltox, so lots of issues are already answered, resolved or wkhtmltox ones.

Following integrations are available:

* [`knplabs/knp-snappy-bundle`](https://github.com/KnpLabs/KnpSnappyBundle), for Symfony
* [`barryvdh/laravel-snappy`](https://github.com/barryvdh/laravel-snappy), for Laravel
* [`mvlabs/mvlabs-snappy`](https://github.com/mvlabs/MvlabsSnappy), for Zend Framework

## Installation using [Composer](http://getcomposer.org/)

```bash
composer require knplabs/knp-snappy
```

## Usage

### Initialization

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use KnpLabs\Snappy\Backend\WkHtmlToPdf;
use KnpLabs\Snappy\Frontend\UriToPdf;
use KnpLabs\Snappy\Process\ProcessRunner;

$processRunner = new ProcessRunner();
$backend = new WkHtmlToPdf('/usr/bin/wkhtmltopdf', $processRunner);
$frontend = new UriToPdf($backend);

$stream = $frontend->generateFromUri('https://github.com/KnpLabs/snappy');

$stream->copyTo('var/snappy-readme.pdf');
```

## Design

```mermaid
classDiagram
    Backend <|-- FileToPdfBackend
    Backend <|-- HtmlToPdfBackend
    Backend <|-- UriToPdfBackend
    FileToPdfBackend <|-- WkHtmlToPdf
    UriToPdfBackend <|-- WkHtmlToPdf
    UriToPdfBackend <|-- ConvertApi
    UriToPdfBackend *-- UriToPdf
    HtmlToPdfBackend *-- HtmlToPdf
    FileToPdfBackend *-- FileToPdf

    class Backend:::backend{
        <<Interface>>
        +validateOptions(array options): void
    }
    class FileToPdfBackend{
        <<Interface>>
        +generateFromFile(SplFileInfo file, iterable options = []): FileStream
    }
    class HtmlToPdfBackend{
        <<Interface>>
        +generateFromHtml(string html, iterable options): FileStream
    }
    class UriToPdfBackend{
        <<Interface>>
        +generateFromUri(string uri, iterable options): FileStream
    }
    class UriToPdf{
        - UriToPdfBackend backend
        +generateFromUri(string file, array options = []): FileStream
    }
    class HtmlToPdf{
        - HtmlToPdfBackend backend
        +generateFromHtml(string html, array options = []): FileStream
    }
    class FileToPdf{
        - FileToPdfBackend backend
        +generateFromFile(SplFileInfo file, array options = []): FileStream
    }
    class WkHtmlToPdf{
        -ProcessRunner processRunner
    }
    class ConvertApi{
        -HttpClientInterface httpClient
    }

```

## Bugs & Support

If you found a bug please fill a detailed issue with all the following points.
If you need some help, please at least provide a complete reproducer so we could help you based on facts rather than assumptions.

* OS and its version
* Wkhtmltopdf, its version and how you installed it
* A complete reproducer with relevant php and html/css/js code

If your reproducer is big, please try to shrink it. It will help everyone to narrow the bug.****

## Credits

Snappy has been originally developed by the [KnpLabs](http://knplabs.com) team.
