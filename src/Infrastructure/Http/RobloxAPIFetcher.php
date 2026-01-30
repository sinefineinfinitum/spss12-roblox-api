<?php

namespace SineFine\RobloxApi\Infrastructure\Http;

use Campo\UserAgent;
use Closure;
use Exception;
use SineFine\RobloxApi\Application\Port\CacheInterface;
use SineFine\RobloxApi\Application\Port\HttpClientInterface;
use SineFine\RobloxApi\Application\Port\OptionsStoreInterface;
use SineFine\RobloxApi\Infrastructure\Constant\RobloxAPIConstants;
use SineFine\RobloxApi\Domain\Exceptions\RobloxAPIException;

/**
 * This class holds all logic for fetching data from Roblox API endpoints.
 */
class RobloxAPIFetcher
{

    public const CONSTRUCTOR_OPTIONS = [
        RobloxAPIConstants::ConfCachingExpiries,
        RobloxAPIConstants::ConfRequestUserAgent,
    ];

    private const CACHE_EXPIRY_DEFAULT = 24*60*60;

    /**
     * @var string[]
     */
    private array $rateLimitedDataSources = [];

    public function __construct(
        private OptionsStoreInterface $options,
        private HttpClientInterface   $httpClient,
        private CacheInterface        $cache,
    )
    {
//		$options->assertRequiredOptions( self::CONSTRUCTOR_OPTIONS );
    }

    /**
     * Fetches data from the given endpoint.
     * @param string $dataSourceId The ID of the data source
     * @param string $endpoint The endpoint to fetch data from.
     * @param array<string> $requiredArgs
     * @param array<string, string> $optionalArgs
     * @param array<string, string> $headers Additional headers that should be added
     * @param Closure( array<string, mixed>&, array<string>, array<string, string> ): void $processRequestOptions
     * @return mixed The fetched data.
     * @throws RobloxAPIException if there are any errors during the process
     */
    public function getDataFromEndpoint(
        string  $dataSourceId,
        string  $endpoint,
        array   $requiredArgs,
        array   $optionalArgs,
        array   $headers,
        Closure $processRequestOptions
    ): mixed
    {
        $cacheKey = $this->getCacheKey($endpoint, $requiredArgs, $optionalArgs);
        $cachedResult = $this->cache->get($cacheKey);

        if ($cachedResult !== null && $cachedResult !== false) {
            return $cachedResult;
        }

        if (in_array($dataSourceId, $this->rateLimitedDataSources, true)) {
            throw new RobloxAPIException('robloxapi-error-request-cancelled-rate-limits', $dataSourceId);
        }

        $options = [
            'connectTimeout' => 5,
            'timeout' => 5,
            'user-agent' => $this->getRandomUserAgent(),
            'headers' => ['Accept' => 'application/json'],
        ];

        $processRequestOptions($options, $requiredArgs, $optionalArgs);

        foreach ($headers as $header => $value) {
            $options['headers'][$header] = $value;
        }

        if (isset($options['method']) && $options['method'] == 'POST') {
            $result = $this->httpClient->post($endpoint, $options);
        } else {
            $result = $this->httpClient->get($endpoint, $options);
        }

        $response = $result['response'];
        $json = $result['body'];

        if (is_wp_error($response)) {
            throw new RobloxAPIException($response->get_error_message());
        }

        $data = json_decode($json);

        if ($data === null) {
            throw new RobloxAPIException('robloxapi-error-decode-failure');
        }

        $this->cache->set(
            $cacheKey,
            $data,
            $this->getCachingExpiry($dataSourceId) ?? self::CACHE_EXPIRY_DEFAULT
        );

        return $data;
    }


    /**
     * @param string $endpoint
     * @param array<string, mixed> $args
     * @param array<string, mixed> $optionalArgs
     * @return string
     */
    private function getCacheKey(string $endpoint, array $args, array $optionalArgs): string
    {
        return '__roblox__' . $endpoint . '__' . md5(json_encode($args)) . '__' . md5(json_encode($optionalArgs));
    }

    /**
     * Gets the caching expiry for a data source.
     * If a specific value is not set, the default value (key '*') is used.
     * @param string $id The ID of the data source
     * @return ?int The caching expiry in seconds.
     */
    protected function getCachingExpiry(string $id): ?int
    {
        $cachingExpiry = $this->options->get(RobloxAPIConstants::ConfCachingExpiries);
        if (!isset($cachingExpiry[$id])) {
            return $cachingExpiry['*'] ?? self::CACHE_EXPIRY_DEFAULT;
        }

        return $cachingExpiry[$id];
    }

    private function getRandomUserAgent(): string
    {
        try {
            return UserAgent::random();
        } catch (Exception) {
            return 'Wordpress';
        }
    }
}
