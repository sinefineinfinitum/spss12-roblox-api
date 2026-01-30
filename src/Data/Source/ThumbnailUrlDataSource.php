<?php

namespace SineFine\RobloxApi\Data\Source;

use SineFine\RobloxApi\Data\Args\ArgumentSpecification;

abstract class ThumbnailUrlDataSource extends DependentDataSource {
	/**
	 * @inheritDoc
	 */
	public function __construct(
		DataSourceProvider $dataSourceProvider,
		string $id,
		string $dependencyId,
	) {
		parent::__construct( $dataSourceProvider, $id, $dependencyId );
	}

	/**
	 * @inheritDoc
	 * @return string URL of the thumbnail
	 */
	public function exec(array $requiredArgs, array $optionalArgs = [] ): string {
		$data = $this->dataSource->exec($requiredArgs, $optionalArgs );

		if ( !$data ) {
			$this->failNoData();
		}

		if ( count( $data ) === 0 ) {
			$this->failInvalidData();
		}

		$url = $data[0]->imageUrl;

		if ( !$url ) {
			$this->failInvalidData();
		}

		$format = $optionalArgs['format'] ?? 'Png';
		$lowerFormat = strtolower( $format );

		$url = "$url.$lowerFormat";

		if ( !$this->verifyIsRobloxCdnUrl( $url ) ) {
			$this->failInvalidData();
		}

		return $url;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldEscapeResult( mixed $result ): bool {
		// The url should not be escaped here in order to be embedded correctly using $wgEnableImageWhitelist.
		// If the URL was escaped here, it would be URL-encoded and not recognized by MediaWiki as an image URL.
		return !$this->verifyIsRobloxCdnUrl( $result );
	}

	/**
	 * @inheritDoc
	 */
	public function getArgumentSpecification(): ArgumentSpecification {
		return $this->dataSource->getArgumentSpecification();
	}

    /**
     * Verifies that a URL is a Roblox CDN URL
     * @param string $url The URL to verify
     */
    public function verifyIsRobloxCdnUrl(string $url): bool
    {
        $urlParts = parse_url($url);

        return $urlParts !== []
            && !isset($urlParts['port'])
            && !isset($urlParts['query'])
            && !isset($urlParts['fragment'])
            && isset($urlParts['scheme'])
            && $urlParts['scheme'] === 'https'
            && preg_match("/^[a-zA-Z0-9]{2}\.rbxcdn\.com$/", $urlParts['host'])
            && preg_match("/[0-9A-Za-z\-\/]*\.(png|webp)?$/", $urlParts['path']);
    }

}
