<?php

declare(strict_types=1);

namespace NAttreid\MailChimp;

use Exception;
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
		parent::__construct('', 0, $previous);
	}
}