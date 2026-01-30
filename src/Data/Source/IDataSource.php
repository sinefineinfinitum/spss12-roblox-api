<?php

namespace SineFine\RobloxApi\Data\Source;

use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Domain\Exceptions\RobloxAPIException;

/**
 * Represents a data source.
 */
interface IDataSource {

	/**
	 * Executes the data source. This is called when the #robloxAPI parser function is used.
	 * @param string[] $requiredArgs
	 * @param array<string, string> $optionalArgs
	 * @throws RobloxAPIException If the data source fails to execute
	 */
	public function exec(array $requiredArgs, array $optionalArgs = [] ): mixed;

	/**
	 * Gets the argument specification for this data source.
	 */
	public function getArgumentSpecification(): ArgumentSpecification;

	/**
	 * Gets the ID of the data source.
	 */
	public function getId(): string;

	/**
	 * @return bool Whether the data source is enabled.
	 */
	public function isEnabled(): bool;

	/**
	 * Disables this data source.
	 */
	public function disable(): void;

}
