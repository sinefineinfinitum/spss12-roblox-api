<?php

namespace SineFine\RobloxApi\Data\Source\Implementation;

use SineFine\RobloxApi\Data\Source\DataSourceProvider;
use SineFine\RobloxApi\Data\Source\ThumbnailUrlDataSource;

class AssetThumbnailUrlDataSource extends ThumbnailUrlDataSource {

	/**
	 * @inheritDoc
	 */
	public function __construct( DataSourceProvider $dataSourceProvider) {
		parent::__construct( $dataSourceProvider, 'assetThumbnailUrl', 'assetThumbnail' );
	}

}
