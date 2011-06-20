# Snappy

Snappy is a PHP5 library allowing thumbnail, snapshot or PDF generation from a url or a html page. This is a simple PHP wrapper for the wkhtmltopdf/wkhtmltoimage executable.

# Example use
```php
<?php

namespace Knplabs\Snappy;
 
require_once('Knplabs/Snappy/Media.php');
require_once('Knplabs/Snappy/Image.php');
 
// location of the wkhtmltoimage binary, freebsd used in this example
$snappy = new Image('/usr/local/bin/wkhtmltoimage');
 
// Display the resulting image in the browser by setting the Content-type header to jpg
header("Content-Type: image/jpeg");
$snappy->output('http://www.github.com');
```

## Credits

All credits go to the original authors of [wkhtmltopdf](http://github.com/antialize/wkhtmltopdf).