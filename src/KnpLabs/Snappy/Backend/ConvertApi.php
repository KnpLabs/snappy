<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Backend;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use KnpLabs\Snappy\FileStream;
use SplFileInfo;

// TODO: This class should not be maintained by us, but by the community
// It stands for test purpose only. Reminder remove the symfony/http-client dependency
class ConvertApi implements UriToPdfBackend
{
    public function __construct(
        private readonly string $apiUrl,
        private readonly string $secret,
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public function generateFromUri(string $uri, iterable $options): FileStream
    {
        $response = $this->httpClient->request(
            'POST',
            sprintf('%s?Secret=%s', $this->apiUrl, $this->secret),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'Parameters' => [
                        [
                            'Name' => 'File',
                            'FileValue' => [
                                'Url' => $uri,
                            ],
                        ],
                        [
                            'Name' => 'FileName',
                            'Value' => 'toto',
                        ],
                        ...$options
                    ],
                ],
            ]
        );

        $content = $response->toArray();

        file_put_contents('/tmp/toto.pdf', base64_decode($content['Files'][0]['FileData']));

        return new FileStream(new SplFileInfo('/tmp/toto.pdf'));
    }

    public function validateOptions(array $options): void
    {
        foreach ($options as $option) {
            if (
                !is_array($option)
                || !isset($option['Name'])
            ) {
                throw new \InvalidArgumentException(
                    'Options must be an array like [\'Name\' => \'FileName\', \'Value\' => \'toto\']'
                );
            }
        }
    }
}
