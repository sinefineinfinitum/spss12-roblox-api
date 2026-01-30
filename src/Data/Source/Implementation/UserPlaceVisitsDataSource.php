<?php

namespace SineFine\RobloxApi\Data\Source\Implementation;

use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Data\Source\DataSourceProvider;
use SineFine\RobloxApi\Data\Source\DependentDataSource;

/**
 * A data source for getting the total amount of visits a user's places have.
 * For performance reasons, this is restricted to the first 50 games the API returns.
 */
class UserPlaceVisitsDataSource extends DependentDataSource {

	public function __construct( DataSourceProvider $dataSourceProvider ) {
		parent::__construct( $dataSourceProvider, 'userPlaceVisits', 'userGames' );
	}

	/**
	 * @inheritDoc
	 */
	public function exec(array $requiredArgs, array $optionalArgs = [] ): mixed {
		$userGames = $this->dataSource->exec($requiredArgs, $optionalArgs );

		if ( $userGames === null ) {
			$this->failNoData();
		}

		if ( !is_array( $userGames ) ) {
			$this->failUnexpectedDataStructure();
		}

		$totalVisits = 0;
		foreach ( $userGames as $game ) {
			if ( !property_exists( $game, 'placeVisits' ) ) {
				$this->failUnexpectedDataStructure();
			}
			$totalVisits += $game->placeVisits;
		}

		return $totalVisits;
	}

	/**
	 * @inheritDoc
	 */
	public function getArgumentSpecification(): ArgumentSpecification {
		return $this->dataSource->getArgumentSpecification();
	}
}
