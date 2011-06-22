<?php

spl_autoload_register(
    function($class) {
      static $classes = null;
      if ($classes === null) {
         $classes = array(
            'knplabs\\snappy\\image' => '/Image.php',
            'knplabs\\snappy\\media' => '/Media.php',
            'knplabs\\snappy\\pdf' => '/Pdf.php'
          );
      }
      $cn = strtolower($class);
      if (isset($classes[$cn])) {
         require __DIR__ . '/../src/Knplabs/Snappy' . $classes[$cn];
      }
    }
);
