<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\Entities;

use DateTimeInterface;
use Nette\InvalidArgumentException;

/**
 * Class Order
 *
 * @property DateTimeInterface $inserted
 *
 * @author Attreid <attreid@gmail.com>
 */
class Order extends Cart
{
	/** @var DateTimeInterface */
	private $inserted;

	protected function getInserted(): DateTimeInterface
	{
		return $this->inserted;
	}

	protected function setInserted(DateTimeInterface $inserted): void
	{
		$this->inserted = $inserted;
	}

	/**
	 * @param OrderLine $line
	 */
	public function addLine(Line $line): void
	{
		if (!$line instanceof OrderLine) {
			throw new InvalidArgumentException(__METHOD__ . ' Expects instance of OrderLine, you passed ' . get_class($line));
		}
		$this->lines[] = $line;
		$this->total += (($line->price - $line->discount) * $line->quantity);
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
			'order_url' => $this->url,
			'currency_code' => $this->currency,
			'order_total' => $this->total,
			'financial_status' => 'pending',
			'lines' => $lines
		];
		if ($this->inserted !== null) {
			$data['processed_at_foreign'] = $this->inserted->format('Y-m-d H:m:s');
		}

		if ($this->campaignId) {
			$data['campaign_id'] = $this->campaignId;
		}
		return $data;
	}
}