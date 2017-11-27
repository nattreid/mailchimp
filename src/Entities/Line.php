<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\Entities;

use Nette\SmartObject;

/**
 * Class Line
 *
 * @property string $id
 * @property string $productId
 * @property string $productVariantId
 * @property int $quantity
 * @property float $price
 *
 * @author Attreid <attreid@gmail.com>
 */
class Line
{
	use SmartObject;

	/** @var string */
	protected $id;

	/** @var string */
	protected $productId;

	/** @var string */
	protected $productVariantId;

	/** @var int */
	protected $quantity;

	/** @var float */
	protected $price;

	protected function getId(): string
	{
		return $this->id;
	}

	protected function setId(string $id): void
	{
		$this->id = $id;
	}

	protected function getProductId(): string
	{
		return $this->productId;
	}

	protected function setProductId(string $productId): void
	{
		$this->productId = $productId;
	}

	protected function getProductVariantId(): string
	{
		return $this->productVariantId;
	}

	protected function setProductVariantId(string $productVariantId): void
	{
		$this->productVariantId = $productVariantId;
	}

	protected function getQuantity(): int
	{
		return $this->quantity;
	}

	protected function setQuantity(int $quantity): void
	{
		$this->quantity = $quantity;
	}

	protected function getPrice(): float
	{
		return $this->price;
	}

	protected function setPrice(float $price): void
	{
		$this->price = $price;
	}

	public function getData(): array
	{
		return [
			'id' => $this->id,
			'product_id' => $this->productId,
			'product_variant_id' => $this->productVariantId,
			'quantity' => $this->quantity,
			'price' => $this->price
		];
	}
}