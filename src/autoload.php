<?php

/**
 * Simple autoloader that follow the PHP Standards Recommendation #0 (PSR-0)
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md for more informations.
 *
 * Code inspired from the SplClassLoader RFC
 * @see https://wiki.php.net/rfc/splclassloader#example_implementation
 */
spl_autoload_register(function($className) {
    $package = 'Knp\\Snappy';
    $className = ltrim($className, '\\');
    if (0 === strpos($className, $package)) {
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
        if (is_file($fileName)) {
            require $fileName;
            return true;
        }
    }
    return false;
});
