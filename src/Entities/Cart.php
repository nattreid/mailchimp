<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\Entities;

use Nette\SmartObject;

/**
 * Class Cart
 *
 * @property string $id
 * @property Customer $customer
 * @property string $campaignId
 * @property string $url
 * @property string $currency
 * @property-read float $total
 *
 * @author Attreid <attreid@gmail.com>
 */
class Cart
{
	use SmartObject;

	/** @var string */
	private $id;

	/** @var string */
	private $customer;

	/** @var string */
	private $campaignId;

	/** @var string */
	private $url;

	/** @var string */
	private $currency;

	/** @var float */
	private $total = 0;

	/** @var Line[] */
	private $lines = [];

	protected function getId(): string
	{
		return $this->id;
	}

	protected function setId(string $id): void
	{
		$this->id = $id;
	}

	protected function getCustomer(): Customer
	{
		return $this->customer;
	}

	protected function setCustomer(Customer $customer): void
	{
		$this->customer = $customer;
	}

	protected function getCampaignId(): string
	{
		return $this->campaignId;
	}

	protected function setCampaignId(string $campaignId): void
	{
		$this->campaignId = $campaignId;
	}

	protected function getUrl(): string
	{
		return $this->url;
	}

	protected function setUrl(string $url): void
	{
		$this->url = $url;
	}

	protected function getCurrency(): string
	{
		return $this->currency;
	}

	protected function setCurrency(string $currency): void
	{
		$this->currency = $currency;
	}

	protected function getTotal(): float
	{
		return $this->total;
	}

	public function addLine(Line $line): void
	{
		$this->lines[] = $line;
		$this->total = ($line->price * $line->quantity);
	}

	public function getData(): array
	{
		$lines = [];
		foreach ($this->lines as $line) {
			$lines[] = $line->getData();
		}
		$data = [
			'id' => $this->id,
			'customer' => $this->customer->getData(),
			'checkout_url' => $this->url,
			'currency_code' => $this->currency,
			'order_total' => $this->total,
			'lines' => $lines
		];
		if ($this->campaignId) {
			$data['campaign_id'] = $this->campaignId;
		}
		return $data;
	}
}