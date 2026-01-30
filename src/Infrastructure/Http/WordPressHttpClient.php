<?php

namespace SineFine\RobloxApi\Infrastructure\Http;

use SineFine\RobloxApi\Application\Port\HttpClientInterface;

class WordPressHttpClient implements HttpClientInterface
{
    public function get(string $url, array $options = []): array
    {
        $response = wp_remote_get($url, $options);
        return [
            'body' => wp_remote_retrieve_body($response),
            'response' => $response
        ];
    }

    public function post(string $url, array $options = []): array
    {
        $response = wp_remote_post($url, $options);
        return [
            'body' => wp_remote_retrieve_body($response),
            'response' => $response
        ];
    }
}
