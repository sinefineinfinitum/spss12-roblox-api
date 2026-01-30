<?php

namespace SineFine\RobloxApi\Data\Source\Implementation;

use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Data\Source\DataSourceProvider;
use SineFine\RobloxApi\Data\Source\DependentDataSource;

class GroupMembersDataSource extends DependentDataSource
{

    /**
     * @inheritDoc
     */
    public function __construct(DataSourceProvider $dataSourceProvider)
    {
        parent::__construct($dataSourceProvider, 'groupMembers', 'groupData');
    }

    /**
     * @inheritDoc
     */
    public function exec(array $requiredArgs, array $optionalArgs = []): mixed
    {
        $groupData = $this->dataSource->exec($requiredArgs);

        if (!$groupData) {
            $this->failNoData();
        }

        if (!property_exists($groupData, 'memberCount')) {
            $this->failUnexpectedDataStructure();
        }

        return $groupData->memberCount;
    }

    /**
     * @inheritDoc
     */
    public function getArgumentSpecification(): ArgumentSpecification
    {
        return new ArgumentSpecification(['GroupID']);
    }

    /**
     * @inheritDoc
     */
    public function shouldRegisterLegacyParserFunction(): bool
    {
        return true;
    }
}
