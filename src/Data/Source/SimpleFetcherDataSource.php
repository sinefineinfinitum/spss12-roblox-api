<?php

namespace SineFine\RobloxApi\Data\Source;

use Closure;
use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Infrastructure\Http\RobloxAPIFetcher;

/**
 * A simple data source that does not process the data.
 */
class SimpleFetcherDataSource extends FetcherDataSource {

	/**
	 * @inheritDoc
	 * @param Closure( array<string>, array<string, string> ): string $createEndpoint The function to create the
	 * endpoint.
	 * @param Closure( mixed, array<string>, array<string, string> ): mixed|null $processDataFn The function to process
	 * the data.
	 * @param bool $registerParserFunction Whether to register a legacy parser function.
	 */
	public function __construct(
		string $id,
		RobloxAPIFetcher $fetcher,
		protected ArgumentSpecification $argumentSpecification,
		protected Closure $createEndpoint,
		protected ?Closure $processDataFn = null,
		protected bool $registerParserFunction = false
	) {
		parent::__construct( $id, $fetcher );
	}

	/**
	 * @inheritDoc
	 */
	public function getEndpoint( array $requiredArgs, array $optionalArgs ): string {
		return call_user_func( $this->createEndpoint, $requiredArgs, $optionalArgs );
	}

	/**
	 * @inheritDoc
	 */
	public function processData( mixed $data, array $requiredArgs, array $optionalArgs ): mixed {
		if ( $this->processDataFn ) {
			return call_user_func( $this->processDataFn, $data, $requiredArgs, $optionalArgs );
		}

		return $data;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldRegisterLegacyParserFunction(): bool {
		return $this->registerParserFunction;
	}

	public function getArgumentSpecification(): ArgumentSpecification {
		return $this->argumentSpecification;
	}

}
