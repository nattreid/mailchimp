<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\Entities;

/**
 * Class OrderLine
 *
 * @property float $discount
 *
 * @author Attreid <attreid@gmail.com>
 */
class OrderLine extends Line
{

	/** @var float */
	private $discount;

	protected function getDiscount(): float
	{
		return $this->discount;
	}

	protected function setDiscount(float $discount): void
	{
		$this->discount = $discount;
	}

	public function getData(): array
	{
		$result = parent::getData();
		if ($this->discount) {
			$result['discount'] = $this->discount;
		}
		return $result;
	}
}