<?php

declare(strict_types=1);

namespace NAttreid\MailChimp;

use Exception;
use GuzzleHttp\Exception\ClientException;
use Throwable;

/**
 * Class MailChimpClientException
 *
 * @author Attreid <attreid@gmail.com>
 */
class MailChimpClientException extends Exception
{
	public function __construct(Throwable $previous = null)
	{
		$message = '';
		if ($previous instanceof ClientException) {
			$message = (string) $previous->getResponse()->getBody();
		}
		parent::__construct($message, 0, $previous);
	}
}