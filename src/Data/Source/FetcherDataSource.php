<?php

namespace SineFine\RobloxApi\Data\Source;

use Closure;
use SineFine\RobloxApi\Infrastructure\Http\RobloxAPIFetcher;
use SineFine\RobloxApi\Domain\Exceptions\RobloxAPIException;

/**
 * Represents an endpoint of the roblox api.
 */
abstract class FetcherDataSource extends AbstractDataSource {

	/**
	 * Constructs a new data source.
	 * @param string $id The ID of this data source.
	 * @param RobloxAPIFetcher $fetcher An instance of the fetcher service.
	 */
	public function __construct(
		string $id,
		private RobloxAPIFetcher $fetcher
	) {
		parent::__construct( $id );
	}

	/**
	 * Fetches data
	 * @param array<string> $requiredArgs
	 * @param array<string, string> $optionalArgs
	 * @throws RobloxAPIException if there are any errors during the process
	 */
	public function fetch( array $requiredArgs, array $optionalArgs = [] ): mixed {
		$endpoint = $this->getEndpoint( $requiredArgs, $optionalArgs );
		$headers = $this->getAdditionalHeaders( $requiredArgs, $optionalArgs );

		$data = $this->fetcher->getDataFromEndpoint(
			$this->id,
			$endpoint,
			$requiredArgs,
			$optionalArgs,
			$headers,
            Closure::fromCallable([$this, 'processRequestOptions'])
		);

		$processedData = $this->processData( $data, $requiredArgs, $optionalArgs );

		if ( $processedData === null ) {
			throw new RobloxAPIException( 'robloxapi-error-invalid-data' );
		}

		return $processedData;
	}

	/**
	 * Returns the endpoint of this data source for the given arguments.
	 * @param array<string> $requiredArgs
	 * @param array<string, string> $optionalArgs
	 * @return string The endpoint of this data source.
	 */
	abstract public function getEndpoint( array $requiredArgs, array $optionalArgs ): string;

	/**
	 * Processes the data before returning it.
	 * @param mixed $data The data to process.
	 * @param array<string> $requiredArgs
	 * @param array<string, string> $optionalArgs
	 * @return mixed The processed data.
     */
	public function processData( mixed $data, array $requiredArgs, array $optionalArgs ): mixed {
		return $data;
	}

	/**
	 * Processes the request options before making the request. This allows modifying the request options.
	 * @param array<string, mixed> &$options The options to process.
	 * @param string[] $requiredArgs
	 * @param array<string, string> $optionalArgs
	 */
	public function processRequestOptions( array &$options, array $requiredArgs, array $optionalArgs ): void {
		// do nothing by default
	}

	/**
	 * Allows specifying additional headers for the request.
	 * @param array<string> $requiredArgs
	 * @param array<string, string> $optionalArgs
	 * @return array<string, string> The additional headers.
	 */
	protected function getAdditionalHeaders( array $requiredArgs, array $optionalArgs ): array {
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return $this->id;
	}

	/**
	 * @inheritDoc
	 */
	public function exec(array $requiredArgs, array $optionalArgs = [] ): mixed {
		return $this->fetch( $requiredArgs, $optionalArgs );
	}
}
