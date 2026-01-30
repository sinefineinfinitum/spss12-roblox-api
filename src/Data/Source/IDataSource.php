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
	 * Determines whether a legacy parser function should be registered.
	 */
	public function shouldRegisterLegacyParserFunction(): bool;

	/**
	 * Gets the argument specification for this data source.
	 */
	public function getArgumentSpecification(): ArgumentSpecification;

	/**
	 * Determines whether the result of the parser function should be escaped.
	 * Note that this is ignored and the result is always escaped if the data source returns JSON data.
	 * @param mixed $result The result of the parser function.
	 * @return bool Whether the result should be escaped and url-encoded.
	 */
	public function shouldEscapeResult( mixed $result ): bool;

	/**
	 * Gets the ID of the data source.
	 */
	public function getId(): string;

	/**
	 * @return string The ID of the data source that fetches the data for this one.
	 */
	public function getFetcherSourceId(): string;

	/**
	 * @return bool Whether the data source is enabled.
	 */
	public function isEnabled(): bool;

	/**
	 * Disables this data source.
	 */
	public function disable(): void;

}
