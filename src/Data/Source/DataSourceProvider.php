<?php

namespace SineFine\RobloxApi\Data\Source;

use Closure;
use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Infrastructure\Http\RobloxAPIFetcher;
use SineFine\RobloxApi\Data\Source\Implementation\AssetThumbnailDataSource;
use SineFine\RobloxApi\Data\Source\Implementation\AssetThumbnailUrlDataSource;
use SineFine\RobloxApi\Data\Source\Implementation\GameDataSource;
use SineFine\RobloxApi\Data\Source\Implementation\GameIconDataSource;
use SineFine\RobloxApi\Data\Source\Implementation\GameIconUrlDataSource;
use SineFine\RobloxApi\Data\Source\Implementation\GroupMembersDataSource;
use SineFine\RobloxApi\Data\Source\Implementation\GroupRankDataSource;
use SineFine\RobloxApi\Data\Source\Implementation\PlaceActivePlayersDataSource;
use SineFine\RobloxApi\Data\Source\Implementation\PlaceVisitsDataSource;
use SineFine\RobloxApi\Data\Source\Implementation\UserAvatarThumbnailDataSource;
use SineFine\RobloxApi\Data\Source\Implementation\UserAvatarThumbnailUrlDataSource;
use SineFine\RobloxApi\Data\Source\Implementation\UserIdDataSource;
use SineFine\RobloxApi\Data\Source\Implementation\UserPlaceVisitsDataSource;

/**
 * Handles the registration of data sources and stores them.
 */
class DataSourceProvider
{
    /**
     * @var array<string, IDataSource> The currently enabled data sources.
     */
    public array $dataSources = [];

    public function __construct(
        private RobloxAPIFetcher $fetcher,
    )
    {
        $this->registerMainDataSources();
        $this->registerSimpleDataSources();
        $this->registerDependentDataSources();
    }

    private function registerMainDataSources(): void
    {
        $this->registerDataSources(
            new GameDataSource($this->fetcher),
            new UserIdDataSource($this->fetcher),
            new UserAvatarThumbnailDataSource($this->fetcher),
            new AssetThumbnailDataSource($this->fetcher),
            new GameIconDataSource($this->fetcher),
        );
    }

    private function registerSimpleDataSources(): void
    {
        $this->registerSimpleFetcherDataSource(
            'groupRoles',
            new ArgumentSpecification(['UserID'], [], true),
            static function (array $args): string {
                return "https://groups.roblox.com/v1/users/$args[0]/groups/roles";
            },
            static function (mixed $data): mixed {
                return $data->data;
            },
            true
        );
        $this->registerSimpleFetcherDataSource(
            'groupData',
            new ArgumentSpecification(['GroupID'], [], true),
            static function (array $args): string {
                return "https://groups.roblox.com/v1/groups/$args[0]";
            },
            null,
            true
        );
        $this->registerSimpleFetcherDataSource(
            'groupRolesList',
            new ArgumentSpecification(['GroupID'], [], true),
            static function (array $args): string {
                return "https://groups.roblox.com/v1/groups/$args[0]/roles";
            }
        );
        $this->registerSimpleFetcherDataSource(
            'badgeInfo',
            new ArgumentSpecification(['BadgeID'], [], true),
            static function (array $args): string {
                return "https://badges.roblox.com/v1/badges/$args[0]";
            },
            null,
            true
        );
        $this->registerSimpleFetcherDataSource(
            'userInfo',
            new ArgumentSpecification(['UserID'], [], true),
            static function (array $args): string {
                return "https://users.roblox.com/v1/users/$args[0]";
            },
            null,
            true
        );
        $this->registerSimpleFetcherDataSource(
            'assetDetails',
            new ArgumentSpecification(['AssetID'], [], true),
            static function (array $args): string {
                return "https://economy.roblox.com/v2/assets/$args[0]/details";
            },
            null,
            true
        );
        $this->registerSimpleFetcherDataSource(
            'gameNameDescription',
            new ArgumentSpecification(['UniverseID'], [], true),
            static function (array $args): string {
                return "https://gameinternationalization.roblox.com/v1/name-description/games/$args[0]";
            }
        );
        $this->registerSimpleFetcherDataSource(
            'universeInfo',
            new ArgumentSpecification(['UniverseID'], [], true),
            static function (array $args): string {
                return "https://develop.roblox.com/v1/universes/$args[0]";
            }
        );
        $this->registerSimpleFetcherDataSource(
            'userGames',
            new ArgumentSpecification(
                ['UserID'],
                ['limit' => 'UserGamesLimit', 'sort_order' => 'SortOrder'],
                true
            ),
            static function (array $args, array $optionalArgs): string {
                $limit = $optionalArgs['limit'] ?? 50;
                $sortOrder = $optionalArgs['sort_order'] ?? 'Asc';

                return "https://games.roblox.com/v2/users/$args[0]/games?limit=$limit&sortOrder=$sortOrder";
            },
            static function (mixed $data): mixed {
                return $data->data;
            }
        );
        $this->registerSimpleFetcherDataSource(
            'gameEvents',
            new ArgumentSpecification(
                ['UniverseID'],
                [],
                true
            ),
            static function (array $args): string {
                return "https://apis.roblox.com/virtual-events/v1/universes/$args[0]/virtual-events";
            },
            static function (mixed $data): mixed {
                return $data->data;
            }
        );
        $this->registerSimpleFetcherDataSource(
            'groupRoleMembers',
            new ArgumentSpecification(
                ['GroupID', 'RoleID'],
                ['limit' => 'GroupRoleMembersLimit', 'sort_order' => 'SortOrder'],
            ),
            static function (array $args, array $optionalArgs): string {
                $limit = $optionalArgs['limit'] ?? 50;
                $sortOrder = $optionalArgs['sort_order'] ?? 'Asc';

                return "https://groups.roblox.com/v1/groups/$args[0]/roles/$args[1]/users" .
                    "?limit=$limit&sortOrder=$sortOrder";
            },
            static function (mixed $data): mixed {
                // TODO this discards cursor data, which should be implemented at some point (maybe via lua)
                return $data->data;
            }
        );
    }

    private function registerDependentDataSources(): void
    {
        $this->registerDataSources(
            new GroupRankDataSource($this),
            new PlaceActivePlayersDataSource($this),
            new PlaceVisitsDataSource($this),
            new GroupMembersDataSource($this),
            new UserAvatarThumbnailUrlDataSource($this),
            new AssetThumbnailUrlDataSource($this),
            new GameIconUrlDataSource($this),
            new UserPlaceVisitsDataSource($this),
        );
    }

    /**
     * Registers a data source if it is enabled.
     */
    public function registerDataSource(IDataSource $dataSource): void
    {
//		$enabledDataSources = $this->options->get( RobloxAPIConstants::ConfEnabledDataSources );

        $id = $dataSource->getId();
//		if ( !in_array( $id, $enabledDataSources, true ) ) {
//			$dataSource->disable();
//		}
        $this->dataSources[$id] = $dataSource;
    }

    /**
     * Registers data sources if they're enabled.
     */
    public function registerDataSources(IDataSource ...$dataSources): void
    {
        foreach ($dataSources as $dataSource) {
            $this->registerDataSource($dataSource);
        }
    }

    /**
     * Registers a new simple fetcher data source if it's enabled.
     * @see SimpleFetcherDataSource::__construct
     */
    public function registerSimpleFetcherDataSource(
        string                $id,
        ArgumentSpecification $argumentSpecification,
        Closure               $createEndpoint,
        ?Closure              $processData = null,
        bool                  $registerParserFunction = false
    ): void
    {
        $this->registerDataSource(new SimpleFetcherDataSource(
            $id,
            $this->fetcher,
            $argumentSpecification,
            $createEndpoint,
            $processData,
            $registerParserFunction
        ));
    }

    /**
     * Gets a data source by its ID.
     */
    public function getDataSource(string $id, bool $ignoreCase = false): ?IDataSource
    {
        if (array_key_exists($id, $this->dataSources)) {
            return $this->dataSources[$id];
        }

        if ($ignoreCase) {
            foreach ($this->dataSources as $dataSource) {
                if (strcasecmp($dataSource->getId(), $id) === 0) {
                    return $dataSource;
                }
            }
        }

        return null;
    }
}
