<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\DI;

use Nette\SmartObject;

/**
 * Class MailChimpConfig
 *
 * @property-read string $dc
 * @property string $apiKey
 * @property string $listId
 * @property MailChimpStore|null $store
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

	/** @var MailChimpStore|null */
	private $store;

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
		if ($apiKey !== null) {
			@list(, $this->dc) = explode('-', $apiKey);
		} else {
			$this->dc = null;
		}
	}

	protected function getListId(): ?string
	{
		return $this->listId;
	}

	protected function setListId(?string $listId)
	{
		$this->listId = $listId;
	}

	public function getStore(): ?MailChimpStore
	{
		return $this->store;
	}

	public function setStore(?MailChimpStore $store)
	{
		$this->store = $store;
	}


}