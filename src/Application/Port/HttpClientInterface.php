<?php

namespace SineFine\RobloxApi\Application\Port;

interface HttpClientInterface
{
    /**
     * @param string $url
     * @param array<string, mixed> $options
     * @return array<string, mixed> { body: string, response: mixed }
     */
    public function get(string $url, array $options = []): array;

    /**
     * @param string $url
     * @param array<string, mixed> $options
     * @return array<string, mixed> { body: string, response: mixed }
     */
    public function post(string $url, array $options = []): array;
}
