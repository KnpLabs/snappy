<?php

declare(strict_types=1);

namespace Knp\Snappy\Chrome;

/**
 * Build the commands used by the backend to run chrome.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 */
class CommandBuilder
{
    /**
     * @param string $binary   Binary file used
     * @param string $inputUri URI of the input document used (
     * @param array  $options  Options and arguments to pass to chrome (empty/false/null options are ignored)
     *
     * @return string
     */
    public function buildCommand(string $binary, string $inputUri, array $options): string
    {
        $command = $binary;
        $escapedBinary = escapeshellarg($binary);

        if (is_executable($escapedBinary)) {
            $command = $escapedBinary;
        }

        foreach ($options as $option => $value) {
            if ($value === false || $value === null || empty($value)) {
                continue;
            }

            $command .= ' --' . $option;

            if ($value === true) {
                continue;
            }

            if ($option === 'window-size') {
                $value = implode(',', $value);
            }

            $command .= '=' . escapeshellarg($value);
        }

        return $command . ' ' . escapeshellarg($inputUri);
    }
}
