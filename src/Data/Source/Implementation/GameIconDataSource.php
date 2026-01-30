<?php

namespace SineFine\RobloxApi\Data\Source\Implementation;

use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Infrastructure\Http\RobloxAPIFetcher;
use SineFine\RobloxApi\Data\Source\ThumbnailDataSource;

class GameIconDataSource extends ThumbnailDataSource {

	/**
	 * @inheritDoc
	 */
	public function __construct( RobloxAPIFetcher $fetcher ) {
		parent::__construct( 'gameIcon', $fetcher, 'places/gameicons', 'placeIds' );
	}

	public function getEndpoint( array $requiredArgs, array $optionalArgs ): string {
		$returnPolicy = $optionalArgs['return_policy'] ?? 'PlaceHolder';

		return parent::getEndpoint( $requiredArgs, $optionalArgs ) . "&returnPolicy=$returnPolicy";
	}

	/**
	 * @inheritDoc
	 */
	public function getArgumentSpecification(): ArgumentSpecification {
		// jpeg is also supported in theory, not by the other thumbnail APIs though
		return ( new ArgumentSpecification( [
			'PlaceID',
			'ThumbnailSize',
		], [
			'is_circular' => 'Boolean',
			'format' => 'ThumbnailFormat',
			'return_policy' => 'ReturnPolicy',
		], ) )->withJsonArgs();
	}

}
