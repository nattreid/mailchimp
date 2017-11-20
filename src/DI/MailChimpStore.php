<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\DI;

use Nette\SmartObject;

/**
 * Class MailChimpStore
 *
 * @property string|null $id
 * @property string|null $name
 * @property string|null $domain
 * @property string|null $email
 * @property string|null $currency
 *
 * @author Attreid <attreid@gmail.com>
 */
class MailChimpStore
{
	use SmartObject;

	/** @var string */
	private $id;

	/** @var string */
	private $name;

	/** @var string */
	private $domain;

	/** @var string */
	private $email;

	/** @var string */
	private $currency;

	protected function getId(): ?string
	{
		return $this->id;
	}

	protected function setId(?string $id): void
	{
		$this->id = $id;
	}

	protected function getName(): string
	{
		return $this->name;
	}

	protected function setName(string $name): void
	{
		$this->name = $name;
	}

	protected function getDomain(): string
	{
		return $this->domain;
	}

	protected function setDomain(string $domain): void
	{
		$this->domain = $domain;
	}

	protected function getEmail(): string
	{
		return $this->email;
	}

	protected function setEmail(string $email): void
	{
		$this->email = $email;
	}

	protected function getCurrency(): string
	{
		return $this->currency;
	}

	protected function setCurrency(string $currency): void
	{
		$this->currency = $currency;
	}
}