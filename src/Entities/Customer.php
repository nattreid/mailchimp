<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\Entities;

use Nette\SmartObject;

/**
 * Class Customer
 *
 * @property string $id
 * @property string $email
 * @property string $firstName
 * @property string $surname
 * @property int $countOrders
 * @property float $totalSpent
 * @property bool $optStatus
 *
 * @author Attreid <attreid@gmail.com>
 */
class Customer
{
	use SmartObject;

	/** @var string */
	private $id;

	/** @var string */
	private $email;

	/** @var string */
	private $firstName;

	/** @var string */
	private $surname;

	/** @var bool */
	private $optStatus = true;

	protected function getId(): string
	{
		return $this->id;
	}

	protected function setId(string $id): void
	{
		$this->id = $id;
	}

	protected function getEmail(): string
	{
		return $this->email;
	}

	protected function setEmail(string $email): void
	{
		$this->email = $email;
	}

	protected function getFirstName(): string
	{
		return $this->firstName;
	}

	protected function setFirstName(string $firstName): void
	{
		$this->firstName = $firstName;
	}

	protected function getSurname(): string
	{
		return $this->surname;
	}

	protected function setSurname(string $surname): void
	{
		$this->surname = $surname;
	}

	protected function getCountOrders(): int
	{
		return $this->countOrders;
	}

	protected function setCountOrders(int $countOrders): void
	{
		$this->countOrders = $countOrders;
	}

	protected function getTotalSpent(): float
	{
		return $this->totalSpent;
	}

	protected function setTotalSpent(float $totalSpent): void
	{
		$this->totalSpent = $totalSpent;
	}

	protected function getOptStatus(): bool
	{
		return $this->optStatus;
	}

	protected function setOptStatus(bool $optStatus): void
	{
		$this->optStatus = $optStatus;
	}

	public function getData(): array
	{
		return [
			'id' => (string) $this->id,
			'email_address' => $this->email,
			'first_name' => $this->firstName,
			'last_name' => $this->surname,
			'orders_count' => $this->countOrders,
			'total_spent' => $this->totalSpent,
			'opt_in_status' => $this->optStatus,
		];
	}
}