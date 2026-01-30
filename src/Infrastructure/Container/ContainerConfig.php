<?php

namespace SineFine\RobloxApi\Infrastructure\Container;

use SineFine\RobloxApi\Application\Port\CacheInterface;
use SineFine\RobloxApi\Application\Port\HttpClientInterface;
use SineFine\RobloxApi\Application\Port\OptionsStoreInterface;
use SineFine\RobloxApi\Infrastructure\Http\RobloxAPIFetcher;
use SineFine\RobloxApi\Infrastructure\Http\WordPressHttpClient;
use SineFine\RobloxApi\Infrastructure\Cache\WordPressCache;
use SineFine\RobloxApi\Data\Source\DataSourceProvider;
use SineFine\RobloxApi\Data\Source\DependentDataSource;
use SineFine\RobloxApi\Infrastructure\Option\OptionService;
use SineFine\RobloxApi\Infrastructure\Shortcode\ShortcodeProcessor;
use SineFine\RobloxApi\Infrastructure\Shortcode\ShortcodeRegistrar;
use function DI\autowire;
use function DI\create;
use function DI\get;

class ContainerConfig
{
    /**
     * @return array<string, mixed>
     */
    public static function getConfig(): array
    {
        return [
            ShortcodeRegistrar::class => create(ShortcodeRegistrar::class),
            ShortcodeProcessor::class => create(ShortcodeProcessor::class),

            OptionsStoreInterface::class => get(OptionService::class),
            OptionService::class => create(OptionService::class),

            HttpClientInterface::class => get(WordPressHttpClient::class),
            WordPressHttpClient::class => create(WordPressHttpClient::class),

            CacheInterface::class => get(WordPressCache::class),
            WordPressCache::class => create(WordPressCache::class),

            RobloxAPIFetcher::class => autowire(RobloxAPIFetcher::class)
            ->constructor(
                get(OptionsStoreInterface::class),
                get(HttpClientInterface::class),
                get(CacheInterface::class)
            ),
            DataSourceProvider::class => autowire(DataSourceProvider::class)
            ->constructor(
                get(RobloxAPIFetcher::class),
            ),
            DependentDataSource::class => autowire(DependentDataSource::class)
            ->constructor(get(DataSourceProvider::class)),
        ];
    }
}
