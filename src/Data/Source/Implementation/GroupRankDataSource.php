<?php

namespace SineFine\RobloxApi\Data\Source\Implementation;

use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Data\Source\DataSourceProvider;
use SineFine\RobloxApi\Data\Source\DependentDataSource;
use SineFine\RobloxApi\Domain\Exceptions\RobloxAPIException;

class GroupRankDataSource extends DependentDataSource {

	/**
	 * @inheritDoc
	 */
	public function __construct( DataSourceProvider $dataSourceProvider ) {
		parent::__construct( $dataSourceProvider, 'groupRank', 'groupRoles' );
	}

	/**
	 * @inheritDoc
	 */
	public function exec(array $requiredArgs, array $optionalArgs = [] ): mixed {
		$groups = $this->dataSource->exec([ $requiredArgs[1] ] );

		if ( !$groups ) {
			$this->failNoData();
		}

		if ( !is_array( $groups ) ) {
			$this->failUnexpectedDataStructure();
		}

		foreach ( $groups as $group ) {
			if ( $group->group->id === (int)$requiredArgs[0] ) {
				return $group->role->name;
			}
		}

		throw new RobloxAPIException( 'robloxapi-error-user-group-not-found' );
	}

	/**
	 * @inheritDoc
	 */
	public function getArgumentSpecification(): ArgumentSpecification {
		return new ArgumentSpecification( [ 'GroupID', 'UserID' ] );
	}
}
