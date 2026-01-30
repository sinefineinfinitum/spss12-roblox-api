<?php

namespace SineFine\RobloxApi\Data\Source\Implementation;

use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Data\Source\DataSourceProvider;
use SineFine\RobloxApi\Data\Source\DependentDataSource;

class PlaceVisitsDataSource extends DependentDataSource {

	/**
	 * @inheritDoc
	 */
	public function __construct( DataSourceProvider $dataSourceProvider ) {
		parent::__construct( $dataSourceProvider, 'visits', 'gameData' );
	}

	/**
	 * @inheritDoc
	 */
	public function exec(array $requiredArgs, array $optionalArgs = [] ): mixed {
		$gameData = $this->dataSource->exec($requiredArgs );

		if ( !$gameData ) {
			$this->failNoData();
		}

		if ( !property_exists( $gameData, 'visits' ) ) {
			$this->failUnexpectedDataStructure();
		}

		return $gameData->visits;
	}

	/**
	 * @inheritDoc
	 */
	public function getArgumentSpecification(): ArgumentSpecification {
		return new ArgumentSpecification( [ 'UniverseID', 'GameID' ] );
	}

	/**
	 * @inheritDoc
	 */
	public function shouldRegisterLegacyParserFunction(): bool {
		return true;
	}

}
