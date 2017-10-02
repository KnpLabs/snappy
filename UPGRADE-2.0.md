# Upgrade from 1.x to 2.x

* `Pdf` and `Image` generators has been moved to `Knp\Snappy\Wkhtmltox` namespace

Before:
```php
<?php

use Knp\Snappy\Pdf;

$snappy = new Pdf('/usr/local/bin/wkhtmltopdf');
$snappy->generateFromHtml('<h1>Bill</h1><p>You owe me money, dude.</p>', '/tmp/bill-123.pdf');
```

After:
```php
<?php

use Knp\Snappy\Wkhtmltox\Pdf;

$snappy = new Pdf('/usr/local/bin/wkhtmltopdf');
$snappy->generateFromHtml('<h1>Bill</h1><p>You owe me money, dude.</p>', '/tmp/bill-123.pdf');
```
