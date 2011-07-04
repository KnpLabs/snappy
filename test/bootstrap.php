<?php

spl_autoload_register(
    function($class) {
      static $classes = null;
      if ($classes === null) {
         $classes = array(
            'knp\\snappy\\image' => '/Image.php',
            'knp\\snappy\\media' => '/Media.php',
            'knp\\snappy\\pdf' => '/Pdf.php'
          );
      }
      $cn = strtolower($class);
      if (isset($classes[$cn])) {
         require __DIR__ . '/../src/Knp/Snappy' . $classes[$cn];
      }
    }
);
