<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\Entities;

use Nette\SmartObject;

/**
 * Class Product
 *
 * @property string $id
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

	public function addVariant(string $id, string $title, float $price)
	{
		$this->variants[] = [
			'id' => $id,
			'title' => $title,
			'price' => $price
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
		if ($this->url) {
			$data['url'] = $this->url;
		}
		if ($this->image) {
			$data['image_url'] = $this->image;
		}
		return $data;
	}
}