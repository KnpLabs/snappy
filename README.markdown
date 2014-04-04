# Snappy

Snappy is a PHP5 library allowing thumbnail, snapshot or PDF generation from a url or a html page.
It uses the excellent webkit-based [wkhtmltopdf and wkhtmltoimage](http://wkhtmltopdf.org/)
available on OSX, linux, windows.

You will have to download wkhtmltopdf `0.12.0` in order to use Snappy.

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

// Merge multiple urls into one pdf
// by sending an array of urls to getOutput()
$snappy = new Pdf('/usr/local/bin/wkhtmltopdf');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="file.pdf"');
echo $snappy->getOutput(array('http://www.github.com','http://www.knplabs.com','http://www.php.net'));

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
        "h4cc/wkhtmltopdf-i386": "0.12.x",
        "h4cc/wkhtmltoimage-i386": "0.12.x"
    }
}
```

or this if you are in 64 bit based system:

```json
{
    "require": {
        "h4cc/wkhtmltopdf-amd64": "0.12.x",
        "h4cc/wkhtmltoimage-amd64": "0.12.x"
    }
}
```

And then you can use it

```php
<?php

use Knp\Snappy\Pdf;

$myProjectDirectory = '/path/to/my/project';

$snappy = new Pdf($myProjectDirectory . '/vendor/h4cc/wkhtmltopdf-i386/bin/wkhtmltopdf-i386');

// or

$snappy = new Pdf($myProjectDirectory . '/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
```

## Error handling

From: https://github.com/wkhtmltopdf/wkhtmltopdf/issues/1502, here is a table that explain exit codes returned by the
wkhtmltoimage binary:

ExitCode | Explanation
-------- | ---------------
0        | All OK
1        | PDF generated OK, but some request(s) did not return HTTP 200
2        | Could not something something
X        | Could not write PDF: File in use
Y        | Could not write PDF: No write permission
Z        | PDF generated OK, but some JavaScript requests(s) timeouted
A        | Invalid arguments provided
B        | Could not find input file(s)
C        | Process timeout

When you generate for example an Image, if the exit code of wkhtmltoimage is not 0 (All OK), Snappy raises a RuntimeException with the wkhtmltoimage exit code as code property of the exception.

As explain here https://github.com/KnpLabs/KnpSnappyBundle/issues/33, in some case, you are ok with the exit code 1, 
because the generated image result is acceptable even if some assets return a 404 http status.

Example

```php

try {
    $snappy = new Image('/usr/local/bin/wkhtmltoimage');
    $snappy->generateFromHtml($someHtml, '/tmp/preview.jpg');
} catch (\RuntimeException $e) {
    //Generation terminated, but it should be in degraded mode
    if (1 == $e->getCode()) {
        $this->logger->warning();
    } else {
    // Generation failed
        $this->logger->error();
        throw $e;
    }
}
```

## Credits

Snappy has been originally developed by the [KnpLabs](http://knplabs.com) team.
