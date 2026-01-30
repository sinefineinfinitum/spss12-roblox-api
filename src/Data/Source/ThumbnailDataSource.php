<?php

namespace SineFine\RobloxApi\Data\Source;

use SineFine\RobloxApi\Infrastructure\Http\RobloxAPIFetcher;
use SineFine\RobloxApi\Domain\Exceptions\RobloxAPIException;

abstract class ThumbnailDataSource extends FetcherDataSource {

	/**
	 * @inheritDoc
	 * @param string $apiPath The path of the thumbnail API to use
	 * @param string $thumbnailIdParamName The parameter name to use for submitting the thumbnail ID
	 */
	public function __construct(
		string $id,
		RobloxAPIFetcher $fetcher,
		private string $apiPath,
		protected string $thumbnailIdParamName
	) {
		parent::__construct( $id, $fetcher );
	}

    /**
     * @inheritDoc
     * @throws RobloxAPIException
     */
	public function processData( mixed $data, array $requiredArgs, array $optionalArgs ): mixed {
		if ( !is_object( $data ) || !property_exists( $data, 'data' ) ) {

			throw new RobloxAPIException( 'robloxapi-error-unexpected-data-structure' );
		}

		return $data->data;
	}

	/**
	 * @inheritDoc
	 */
	public function getEndpoint( array $requiredArgs, array $optionalArgs ): string {
		$thumbnailId = $requiredArgs[0];
		$size = $requiredArgs[1];
		$isCircular = strtolower( (string)( $optionalArgs['is_circular'] ?? '' ) ) === 'true';
		$format = $optionalArgs['format'] ?? 'Png';

		$query = http_build_query( [
			$this->thumbnailIdParamName => $thumbnailId,
			'size' => $size,
			'format' => $format,
			'isCircular' => $isCircular ? 'true' : 'false',
		] );

		return "https://thumbnails.roblox.com/v1/$this->apiPath?" . $query;
	}
}
