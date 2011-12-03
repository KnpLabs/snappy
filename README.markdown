# Snappy

Snappy is a PHP5 library allowing thumbnail, snapshot or PDF generation from a url or a html page.  
It uses the excellent webkit-based [wkhtmltopdf and wkhtmltoimage](http://code.google.com/p/wkhtmltopdf/)
available on OSX, linux, windows.

You will have to download wkhtmltopdf 0.10.0 >= rc2 in order to use Snappy.

## Usage

```php
<?php

require_once '/path/to/snappy/src/autoload.php';

use Knp\Snappy\Pdf;

$snappy = new Pdf('/usr/local/bin/wkhtmltopdf');

// or you can do it in two steps
$snappy = new Pdf();
$snappy->setBinary('/usr/local/bin/wkhtmltopdf');

// Display the resulting image in the browser 
// by setting the Content-type header to jpg
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

## Credits

Snappy has been originally developed by the [KnpLabs](http://knplabs.com) team.
