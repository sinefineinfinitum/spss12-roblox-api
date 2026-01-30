<?php

namespace SineFine\RobloxApi\Data\Source\Implementation;

use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Infrastructure\Http\RobloxAPIFetcher;
use SineFine\RobloxApi\Data\Source\ThumbnailDataSource;

class AssetThumbnailDataSource extends ThumbnailDataSource
{
    /**
     * @inheritDoc
     */
    public function __construct(RobloxAPIFetcher $fetcher)
    {
        parent::__construct('assetThumbnail', $fetcher, 'assets', 'assetIds');
    }

    /**
     * @inheritDoc
     */
    public function getArgumentSpecification(): ArgumentSpecification
    {
        return (new ArgumentSpecification([
            'AssetID',
            'ThumbnailSize',
        ], [
            'is_circular' => 'Boolean',
            'format' => 'ThumbnailFormat',
        ],))->withJsonArgs();
    }

}
