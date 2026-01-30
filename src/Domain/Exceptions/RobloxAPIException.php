<?php

namespace SineFine\RobloxApi\Domain\Exceptions;

use Exception;

/**
 * Exception thrown if there are any errors happening when calling the roblox API or parsing the
 * data it returns.
 */
class RobloxAPIException extends Exception {

	/**
	 * @var string[] The parameters to be used in the message.
	 */
	public array $messageParams = [];

	/**
	 * Creates a new RobloxAPIException.
	 * @param string $message
	 * @param string ...$messageParams
	 */
	public function __construct( string $message = '', string ...$messageParams ) {
		parent::__construct( $message );

		$this->messageParams = $messageParams;
	}

}
