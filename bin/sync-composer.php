<?php

$global = \json_decode(
    (string) \file_get_contents(__DIR__ . '/../composer.json'),
    true,
    flags: \JSON_THROW_ON_ERROR,
);

$dependencies = [
    ...$global['require'],
    ...$global['require-dev'],
];

$replace = $global['replace'];

foreach ($global['autoload']['psr-4'] as $path) {
    $content = \file_get_contents($path . '/composer.json');

    if (false === $content) {
        throw new Exception("File {$path}/composer.json not found.");
    }

    $json = \json_decode(
        $content,
        true,
        flags: \JSON_THROW_ON_ERROR,
    );

    foreach (['require', 'require-dev'] as $part) {
        foreach ($json[$part] ?? [] as $name => $constraint) {
            if (isset($replace[$name])) {
                continue;
            }

            if (false === isset($dependencies[$name])) {
                throw new Exception(\sprintf('Dependency "%s" not found in %s/composer.json.', $name, __DIR__, ));
            }

            $json[$part][$name] = $dependencies[$name];
        }
    }

    $content = \file_put_contents(
        $path . '/composer.json',
        \json_encode(
            $json,
            flags: \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES,
        ),
    );
}
