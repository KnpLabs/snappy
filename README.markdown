# Snappy

Snappy is a PHP5 library allowing thumbnail, snapshot or PDF generation from a url or a html page.  
This is a simple PHP wrapper for the [wkhtmltopdf and wkhtmltoimage](http://code.google.com/p/wkhtmltopdf/) executable available on OSX, linux, windows.

# Example use
```php
<?php

require_once 'Knp/Snappy/autoload.php';

use Knp\Snappy\Image;

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

require_once 'Knp/Snappy/autoload.php';

use Knp\Snappy\Image;

$snappy = new Image();
$snappy->setExecutable('/usr/local/bin/wkhtmltoimage');

header("Content-Type: image/jpeg");
$snappy->output('http://www.github.com');
```

### Alternative 2 (define location of binaries in constant):

```php
<?php

require_once 'Knp/Snappy/autoload.php';

use Knp\Snappy\Image;

// can be defined in a configuration file or anywhere else 
define("SNAPPY_IMAGE_BINARY", "/usr/local/bin/wkhtmltoimage");

$snappy = new Image();
header("Content-Type: image/jpeg");
$snappy->output('http://www.github.com');
```
