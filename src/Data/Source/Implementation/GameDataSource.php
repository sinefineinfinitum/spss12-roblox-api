<?php

namespace SineFine\RobloxApi\Data\Source\Implementation;

use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Infrastructure\Http\RobloxAPIFetcher;
use SineFine\RobloxApi\Data\Source\FetcherDataSource;
use SineFine\RobloxApi\Domain\Exceptions\RobloxAPIException;

/**
 * A data source for the roblox games API.
 */
class GameDataSource extends FetcherDataSource
{
    public function __construct(RobloxAPIFetcher $fetcher)
    {
        parent::__construct('gameData', $fetcher);
    }

    /**
     * @inheritDoc
     */
    public function getEndpoint(array $requiredArgs, array $optionalArgs): string
    {
        return "https://games.roblox.com/v1/games?universeIds=$requiredArgs[0]";
    }

    /**
     * @inheritDoc
     * @throws RobloxAPIException
     */
    public function processData(mixed $data, array $requiredArgs, array $optionalArgs): mixed
    {
        $entries = $data->data;

        if (!$entries) {
            throw new RobloxAPIException('robloxapi-error-invalid-data');
        }

        foreach ($entries as $entry) {
            if (!property_exists($entry, 'rootPlaceId')) {
                continue;
            }

            if ($entry->rootPlaceId !== (int)$requiredArgs[1]) {
                continue;
            }

            if (array_key_exists('json_key', $optionalArgs)) {
                //TODO multidimensional properties
                $key = $optionalArgs['json_key'];
               return $entry->{$key} ?? $entry;
            }

            return $entry;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getArgumentSpecification(): ArgumentSpecification
    {
        return (new ArgumentSpecification([
            'UniverseID',
            'PlaceID',
        ]))->withJsonArgs();
    }

}
