<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\Hooks;

use Nette\SmartObject;

/**
 * Class MailChimpConfig
 *
 * @property-read string $dc
 * @property string $apiKey
 * @property string $listId
 *
 * @author Attreid <attreid@gmail.com>
 */
class MailChimpConfig
{
	use SmartObject;

	/** @var string */
	private $dc;

	/** @var string */
	private $apiKey;

	/** @var string */
	private $listId;

	protected function getDc(): ?string
	{
		return $this->dc;
	}

	protected function getApiKey(): ?string
	{
		return $this->apiKey;
	}

	protected function setApiKey(?string $apiKey)
	{
		$this->apiKey = $apiKey;
		@list(, $this->dc) = explode('-', $apiKey);
	}

	protected function getListId(): ?string
	{
		return $this->listId;
	}

	protected function setListId(?string $listId)
	{
		$this->listId = $listId;
	}
}