<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\Entities;

use DateTime;
use Nette\SmartObject;

/**
 * Class Product
 *
 * @property string $id
 * @property DateTime $inserted
 * @property string $title
 * @property string $image
 * @property string $url
 * @property string $vendor
 *
 * @author Attreid <attreid@gmail.com>
 */
class Product
{
	use SmartObject;

	/** @var string */
	private $id;

	/** @var DateTime */
	private $inserted;

	/** @var string */
	private $title;

	/** @var string */
	private $image;

	/** @var string */
	private $url;

	/** @var string */
	private $vendor;

	/** @var array */
	private $variants = [];

	protected function getId(): string
	{
		return $this->id;
	}

	protected function setId(string $id): void
	{
		$this->id = $id;
	}

	protected function getInserted(): DateTime
	{
		return $this->inserted;
	}

	protected function setInserted(DateTime $inserted): void
	{
		$this->inserted = $inserted;
	}

	protected function getTitle(): string
	{
		return $this->title;
	}

	protected function setTitle(string $title): void
	{
		$this->title = $title;
	}

	protected function getImage(): string
	{
		return $this->image;
	}

	protected function setImage(string $image): void
	{
		$this->image = $image;
	}

	protected function getUrl(): string
	{
		return $this->url;
	}

	protected function setUrl(string $url): void
	{
		$this->url = $url;
	}

	protected function getVendor(): string
	{
		return $this->vendor;
	}

	protected function setVendor(string $vendor): void
	{
		$this->vendor = $vendor;
	}

	public function addVariant(string $id, string $title, float $price, bool $recommended = false)
	{
		$this->variants[] = [
			'id' => $id,
			'title' => $title,
			'price' => $price,
			'inventory_quantity' => $recommended ? 1 : 0
		];
	}

	public function getData(): array
	{
		$data = [
			'id' => $this->id,
			'title' => $this->title,
			'variants' => $this->variants,
		];
		if ($this->vendor) {
			$data['vendor'] = $this->vendor;
		}
		if ($this->inserted !== null) {
			$data['published_at_foreign'] = $this->inserted->format('Y-m-d H:m:s');
		}
		if ($this->url) {
			$data['url'] = $this->url;
		}
		if ($this->image) {
			$data['image_url'] = $this->image;
		}
		return $data;
	}
}