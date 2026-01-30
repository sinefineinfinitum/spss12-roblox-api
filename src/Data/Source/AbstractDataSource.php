<?php

namespace SineFine\RobloxApi\Data\Source;

abstract class AbstractDataSource implements IDataSource
{
    /**
     * @var bool Whether this data source is enabled.
     */
    private bool $enabled;

    /**
     * @param string $id The ID of this data source.
     */
    public function __construct(
        public string $id,
    )
    {
        $this->enabled = true;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @inheritDoc
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id;
    }
}
