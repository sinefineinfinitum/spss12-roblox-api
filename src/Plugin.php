<?php

namespace SineFine\RobloxApi;

use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use SineFine\RobloxApi\Data\Source\DataSourceProvider;
use SineFine\RobloxApi\Infrastructure\Container\ContainerConfig;
use SineFine\RobloxApi\Infrastructure\Shortcode\ShortcodeProcessor;
use SineFine\RobloxApi\Infrastructure\Shortcode\ShortcodeRegistrar;

final class Plugin
{
    /**
     * @throws DependencyException | NotFoundException | Exception
     */
    public function boot(): void
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions( ContainerConfig::getConfig() );
        $container = $builder->build();

        $registrar  = $container->get( ShortcodeRegistrar::class );
        $processor  = $container->get( ShortcodeProcessor::class );
        $dataSourceProvider = $container->get( DataSourceProvider::class );

        // add shortcodes
        foreach ($dataSourceProvider->dataSources as $dataSource) {
            $id = $dataSource->getId();
            $shortcodeName = 'roblox_' . $id;

            $registrar->addShortcode(
                $shortcodeName,
                function ($attrs) use ($processor, $dataSource) {
                    return $processor->process($dataSource, (array)$attrs);
                }
            );
        }
    }
}
