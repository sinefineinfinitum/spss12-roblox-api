<?php

namespace SineFine\RobloxApi\Data\Args;

/**
 * Represents the specification for the arguments that a data source requires.
 */
class ArgumentSpecification {

	/**
	 * @param string[] $requiredArgs The required argument types.
	 * @param array<string, string> $optionalArgs The optional argument's names and types.
	 * @param bool $withJsonArgs Whether to add the default optional arguments for JSON data.
	 */
	public function __construct(
		public array $requiredArgs,
		public array $optionalArgs = [],
		bool $withJsonArgs = false
	) {
		if ( $withJsonArgs ) {
			$this->withJsonArgs();
		}
	}

	/**
	 * Adds the default optional arguments for JSON data and returns the instance.
	 */
	public function withJsonArgs(): ArgumentSpecification {
		$this->optionalArgs['pretty'] = 'Boolean';
		$this->optionalArgs['json_key'] = 'String';

		return $this;
	}

	/**
	 * Adds a required argument to the specification and returns the instance.
	 * @param string $arg The argument type.
	 */
	public function withRequiredArg( string $arg ): ArgumentSpecification {
		$this->requiredArgs[] = $arg;

		return $this;
	}

	/**
	 * Adds an optional argument to the specification and returns the instance.
	 * @param string $arg The argument name.
	 * @param string $type The argument type.
	 */
	public function withOptionalArg( string $arg, string $type ): ArgumentSpecification {
		$this->optionalArgs[$arg] = $type;

		return $this;
	}

}
