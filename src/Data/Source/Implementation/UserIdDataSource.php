<?php

namespace SineFine\RobloxApi\Data\Source\Implementation;

use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Infrastructure\Http\RobloxAPIFetcher;
use SineFine\RobloxApi\Data\Source\FetcherDataSource;
use SineFine\RobloxApi\Domain\Exceptions\RobloxAPIException;

/**
 * A data source for getting a user's ID from their username.
 */
class UserIdDataSource extends FetcherDataSource
{

    /**
     * @inheritDoc
     */
    public function __construct(RobloxAPIFetcher $fetcher)
    {
        parent::__construct('userId', $fetcher);
    }

    /**
     * @inheritDoc
     */
    public function getEndpoint(array $requiredArgs, array $optionalArgs): string
    {
        return "https://users.roblox.com/v1/usernames/users";
    }

    /**
     * @inheritDoc
     * @throws RobloxAPIException
     */
    public function processData(mixed $data, array $requiredArgs, array $optionalArgs): mixed
    {

        $entries = $data->data;
        if ($entries === null || count($entries) === 0) {
            throw new RobloxAPIException('robloxapi-error-invalid-data');
        }

        return $entries[0];
    }

    /**
     * @inheritDoc
     */
    public function processRequestOptions(array &$options, array $requiredArgs, array $optionalArgs): void
    {
        $options['method'] = 'POST';
        $options['body'] = json_encode(['usernames' => [$requiredArgs[0]]]);
    }

    /**
     * @inheritDoc
     */
    protected function getAdditionalHeaders(array $requiredArgs, array $optionalArgs): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @inheritDoc
     */
    public function exec(array $requiredArgs, array $optionalArgs = []): mixed
    {
        $data = $this->fetch($requiredArgs, $optionalArgs);

        if (!$data) {
            throw new RobloxAPIException('robloxapi-error-datasource-returned-no-data');
        }

        if (!property_exists($data, 'id')) {
            throw new RobloxAPIException('robloxapi-error-unexpected-data-structure');
        }

        return $data->id;
    }

    /**
     * @inheritDoc
     */
    public function getArgumentSpecification(): ArgumentSpecification
    {
        return new ArgumentSpecification(['Username']);
    }

    // special case:
    // for legacy reasons, this data source does not return the full json.
    // instead, it returns the id directly.
    // this is because the id is the same as the one of the legacy parser function.

    /**
     * @inheritDoc
     */
    public function shouldRegisterLegacyParserFunction(): bool
    {
        return true;
    }

}
