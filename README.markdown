# Snappy

Snappy is a PHP5 library allowing thumbnail, snapshot or PDF generation from a url or a html page. This is a simple PHP wrapper for the wkhtmltopdf/wkhtmltoimage executable.

# Example use
```php
<?php
use Knplabs\Snappy\Image;

require_once 'Knplabs/Snappy/autoload.php';

 
// location of the wkhtmltoimage binary, freebsd used in this example
$snappy = new Image('/usr/local/bin/wkhtmltoimage');
 
// Display the resulting image in the browser by setting the Content-type header to jpg
header("Content-Type: image/jpeg");
$snappy->output('http://www.github.com');
```
## Alternative ways of creating a snappy object.

### Alternative 1 (Set location of binary with class method)
```php
<?php

use Knplabs\Snappy\Image;

require_once 'Knplabs/Snappy/autoload.php';

$snappy = new Image();
$snappy->setExecutable('/usr/local/bin/wkhtmltoimage');

header("Content-Type: image/jpeg");
$snappy->output('http://www.github.com');
```

### Alternative 2 (define location of binaries in constant):
```php
<?php

use Knplabs\Snappy\Image;

// can be defined in a configuration file or anywhere else 
define("SNAPPY_IMAGE_BINARY", "/usr/local/bin/wkhtmltoimage");

require_once 'Knplabs/Snappy/autoload.php';

$snappy = new Image();
header("Content-Type: image/jpeg");
$snappy->output('http://www.github.com');
```



## Credits

All credits go to the original authors of [wkhtmltopdf](http://github.com/antialize/wkhtmltopdf).