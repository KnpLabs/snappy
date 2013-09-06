# Snappy

Snappy is a PHP5 library allowing thumbnail, snapshot or PDF generation from a url or a html page.
It uses the excellent webkit-based [wkhtmltopdf and wkhtmltoimage](http://code.google.com/p/wkhtmltopdf/)
available on OSX, linux, windows.

You will have to download wkhtmltopdf `0.11.0 >= rc1` in order to use Snappy.

[![Build Status](https://secure.travis-ci.org/KnpLabs/snappy.png?branch=master)](http://travis-ci.org/KnpLabs/snappy)

## Installation using [Composer](http://getcomposer.org/)

Add to your `composer.json`:

```json
{
    "require" :  {
        "knplabs/knp-snappy": "*"
    }
}
```

## Usage

```php
<?php

require_once '/path/to/snappy/src/autoload.php';

use Knp\Snappy\Pdf;

$snappy = new Pdf('/usr/local/bin/wkhtmltopdf');

// or you can do it in two steps
$snappy = new Pdf();
$snappy->setBinary('/usr/local/bin/wkhtmltopdf');

// Display the resulting pdf in the browser
// by setting the Content-type header to pdf
$snappy = new Pdf('/usr/local/bin/wkhtmltopdf');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="file.pdf"');
echo $snappy->getOutput('http://www.github.com');

// .. or simply save the PDF to a file
$snappy = new Pdf('/usr/local/bin/wkhtmltopdf');
$snappy->generateFromHtml('<h1>Bill</h1><p>You owe me money, dude.</p>', '/tmp/bill-123.pdf');

// Pass options to snappy
// Type wkhtmltopdf -H to see the list of options
$snappy = new Pdf('/usr/local/bin/wkhtmltopdf');
$snappy->setOption('disable-javascript', true);
$snappy->setOption('no-background', true);
$snappy->setOption('allow', array('/path1', '/path2'));
$snappy->setOption('cookie', array('key' => 'value', 'key2' => 'value2'));
```

## wkhtmltopdf binary as composer dependencies

If you want to download wkhtmltopdf and wkhtmltoimage with composer you add to `composer.json`:

```json
{
    "require": {
        "h4cc/wkhtmltopdf-i386": "0.11.0-RC1",
        "h4cc/wkhtmltoimage-i386": "0.11.0-RC1"
    }
}
```

or this if you are in 64 bit based system:

```json
{
    "require": {
        "h4cc/wkhtmltopdf-amd64": "0.11.0-RC1",
        "h4cc/wkhtmltoimage-amd64": "0.11.0-RC1"
    }
}
```

And then you can use it

```php
<?php

use Knp\Snappy\Pdf;

$myProjetDirectory = '/path/to/my/project';

$snappy = new Pdf($myProjetDirectory . '/vendor/h4cc/wkhtmltopdf-i386/bin/wkhtmltopdf-i386');

// or

$snappy = new Pdf($myProjetDirectory . '/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
```


## Credits

Snappy has been originally developed by the [KnpLabs](http://knplabs.com) team.
