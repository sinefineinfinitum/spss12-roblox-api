<?php

namespace SineFine\RobloxApi\Data\Source\Implementation;

use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Data\Source\DataSourceProvider;
use SineFine\RobloxApi\Data\Source\DependentDataSource;

class PlaceActivePlayersDataSource extends DependentDataSource
{
    /**
     * @inheritDoc
     */
    public function __construct(DataSourceProvider $dataSourceProvider)
    {
        parent::__construct($dataSourceProvider, 'activePlayers', 'gameData');
    }

    /**
     * @inheritDoc
     */
    public function exec(array $requiredArgs, array $optionalArgs = []): mixed
    {
        $gameData = $this->dataSource->exec($requiredArgs);

        if (!$gameData) {
            $this->failNoData();
        }

        return $gameData->playing;
    }

    /**
     * @inheritDoc
     */
    public function getArgumentSpecification(): ArgumentSpecification
    {
        return new ArgumentSpecification(['UniverseID', 'GameID']);
    }
}
