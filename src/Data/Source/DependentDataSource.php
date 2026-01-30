<?php

namespace SineFine\RobloxApi\Data\Source;

use LogicException;
use SineFine\RobloxApi\Domain\Exceptions\RobloxAPIException;

abstract class DependentDataSource extends AbstractDataSource {

	/**
	 * @var IDataSource The data source that this data source depends on.
	 */
	protected IDataSource $dataSource;

	/**
	 * @param DataSourceProvider $dataSourceProvider
	 * @param string $id The id of this data source.
	 * @param string $dependencyId
	 */
	public function __construct(
		DataSourceProvider $dataSourceProvider,
		string $id,
		string $dependencyId
	) {
		parent::__construct( $id );
		$nullableDataSource = $dataSourceProvider->getDataSource( $dependencyId );
		if ( $nullableDataSource === null ) {
			throw new LogicException( "Tried constructing dependent data source $this->id" .
				", but dependency $dependencyId was not found!" );
		}
		$this->dataSource = $nullableDataSource;
	}

	/**
	 * Throws an exception stating that the data source returned no data.
	 * @throws RobloxAPIException
	 */
	protected function failNoData(): void
    {
		throw new RobloxAPIException( 'robloxapi-error-datasource-returned-no-data' );
	}

	/**
	 * Throws an exception stating that the data source returned an unexpected data structure.
	 * @throws RobloxAPIException
	 */
	protected function failUnexpectedDataStructure(): void
    {
		throw new RobloxAPIException( 'robloxapi-error-unexpected-data-structure' );
	}

	/**
	 * Throws an exception stating that the data source returned invalid data.
	 * @throws RobloxAPIException
	 */
	protected function failInvalidData(): void
    {
		throw new RobloxAPIException( 'robloxapi-error-invalid-data' );
	}

	/**
	 * @inheritDoc
	 */
	public function isEnabled(): bool
    {
		return $this->dataSource->isEnabled() && parent::isEnabled();
	}
}
