<?php

namespace SineFine\RobloxApi\Data\Source\Implementation;

use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Infrastructure\Http\RobloxAPIFetcher;
use SineFine\RobloxApi\Data\Source\ThumbnailDataSource;

class UserAvatarThumbnailDataSource extends ThumbnailDataSource
{
    /**
     * @inheritDoc
     */
    public function __construct(RobloxAPIFetcher $fetcher)
    {
        parent::__construct('userAvatarThumbnail', $fetcher, 'users/avatar', 'userIds');
    }

    /**
     * @inheritDoc
     */
    public function getArgumentSpecification(): ArgumentSpecification
    {
        return (new ArgumentSpecification([
            'UserID',
            'ThumbnailSize',
        ], [
            'is_circular' => 'Boolean',
            'format' => 'ThumbnailFormat',
        ],))->withJsonArgs();
    }
}
