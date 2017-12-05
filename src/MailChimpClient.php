<?php

declare(strict_types=1);

namespace NAttreid\MailChimp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use NAttreid\MailChimp\DI\MailChimpConfig;
use NAttreid\MailChimp\Entities\Cart;
use NAttreid\MailChimp\Entities\Customer;
use NAttreid\MailChimp\Entities\Line;
use NAttreid\MailChimp\Entities\Order;
use NAttreid\MailChimp\Entities\Product;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\InvalidStateException;
use Nette\SmartObject;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Utils\Strings;
use Psr\Http\Message\ResponseInterface;
use stdClass;

/**
 * Class Client
 *
 * @property string $currency
 *
 * @author Attreid <attreid@gmail.com>
 */
class MailChimpClient
{
	use SmartObject;

	private const COOKIE_TIME = '30 days';

	/** @var Client */
	private $client;

	/** @var string */
	private $uri;

	/** @var MailChimpConfig */
	private $config;

	/** @var bool */
	private $debug;

	/** @var string */
	private $tempDir;

	/** @var string */
	private $campaignId;

	/** @var string */
	private $emailId;

	/** @var string */
	private $currency;

	/** @var Request */
	private $request;

	/** @var Response */
	private $response;

	public function __construct(bool $debug, MailChimpConfig $config, string $tempDir = null, Request $request = null, Response $response = null)
	{
		$this->config = $config;
		$this->uri = "https://{$config->dc}.api.mailchimp.com/3.0/";
		$this->debug = $debug;
		$this->tempDir = $tempDir;
		$this->request = $request;
		$this->response = $response;

		$this->emailId = $this->initCookie('mc_eid');
		$this->campaignId = $this->initCookie('mc_cid');
	}

	private function initCookie(string $variable): ?string
	{
		$value = null;
		if ($this->request && $this->response) {
			$value = $this->request->getQuery($variable);
			if (!empty($value)) {
				$this->response->setCookie($variable, $value, self::COOKIE_TIME);
			} else {
				$value = $this->request->getCookie('mc_cid');
			}
		}
		return $value;
	}

	protected function getCurrency(): ?string
	{
		return $this->currency;
	}

	protected function setCurrency(string $currency): void
	{
		$this->currency = $currency;
	}

	/**
	 * @param ResponseInterface $response
	 * @return stdClass|null
	 * @throws JsonException
	 */
	private function getResponse(ResponseInterface $response): ?stdClass
	{
		$json = $response->getBody()->getContents();
		if (!empty($json)) {
			return Json::decode($json);
		}
		return null;
	}

	private function getClient(): Client
	{
		if ($this->client === null) {
			if (Strings::match($this->config->dc, '/^us([1-9]{1,2})$/') === null) {
				throw new InvalidStateException('Invalid dc (available: us1 - us99)');
			}
			$this->client = new Client(['base_uri' => $this->uri]);
		}
		return $this->client;
	}

	/**
	 * @param string $method
	 * @param string $url
	 * @param array $args
	 * @return stdClass|null
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	private function request(string $method, string $url, array $args = []): ?stdClass
	{
		if (empty($this->config->apiKey)) {
			throw new CredentialsNotSetException('ApiKey must be set');
		}

		try {
			$options = [
				RequestOptions::AUTH => [
					'user',
					$this->config->apiKey
				]
			];

			if (count($args) >= 1) {
				$options[RequestOptions::JSON] = $args;
			}

			$response = $this->getClient()->request($method, $url, $options);

			switch ($response->getStatusCode()) {
				case 200:
				case 201:
					return $this->getResponse($response);
				case 204:
					return new stdClass();
			}
		} catch (ClientException $ex) {
			switch ($ex->getCode()) {
				default:
					throw new MailChimpClientException($ex);
					break;
				case 400:
				case 404:
				case 422:
					if ($this->debug) {
						throw new MailChimpClientException($ex);
					} else {
						return null;
					}
			}
		} catch (\Exception $ex) {
			throw new MailChimpClientException($ex);
		}
		return null;
	}

	/**
	 * @throws CredentialsNotSetException
	 */
	private function checkList(): void
	{
		if (empty($this->config->listId)) {
			throw new CredentialsNotSetException('ListId must be set');
		}
	}

	/**
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	private function checkStore(): void
	{
		if ($this->config->store === null) {
			throw new CredentialsNotSetException('Store is not set');
		}
		$file = $this->tempDir . '/store_' . $this->config->store->id;
		if (!file_exists($file)) {
			$this->createStore();
			file_put_contents($file, '');
		}
	}

	/**
	 * @param string $url
	 * @return stdClass|null
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	private function get(string $url): ?stdClass
	{
		return $this->request('GET', $url);
	}

	/**
	 * @param string $url
	 * @param string[] $args
	 * @return stdClass|null
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	private function post(string $url, array $args = []): ?stdClass
	{
		return $this->request('POST', $url, $args);
	}

	/**
	 * @param string $url
	 * @return bool
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	private function delete(string $url): bool
	{
		return $this->request('DELETE', $url) !== null;
	}

	/**
	 * @param string $url
	 * @param string[] $args
	 * @return stdClass|null
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	private function patch(string $url, array $args = []): ?stdClass
	{
		return $this->request('PATCH', $url, $args);
	}

	/**
	 * @param string $url
	 * @param string[] $args
	 * @return stdClass|null
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	private function put(string $url, array $args = []): ?stdClass
	{
		return $this->request('PUT', $url, $args);
	}

	/**
	 * Aliveness test
	 * @return stdClass
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function ping(): stdClass
	{
		return $this->get('');
	}

	/**
	 * Get information about all lists
	 * @param string $email
	 * @param int $limit
	 * @param int $offset
	 * @return stdClass|null
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function findLists(string $email = null, int $limit = 10, int $offset = 0): ?stdClass
	{
		$args = [
			'limit=' . $limit,
			'offset=' . $offset
		];
		if ($email !== null) {
			$args[] = 'email=' . $email;
		}
		return $this->get('lists' . '?' . implode('&', $args));
	}

	/**
	 * Get information about a specific list
	 * @param string $id
	 * @return stdClass|null
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function getList(string $id): ?stdClass
	{
		return $this->get('lists/' . $id);
	}

	/**
	 * Get information about members in a list
	 * @return stdClass|null
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function findMembers(): ?stdClass
	{
		$this->checkList();
		return $this->get("lists/{$this->config->listId}/members");
	}

	/**
	 * Get information about a specific list member
	 * @param string $email
	 * @return stdClass|null
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function getMember(string $email): ?stdClass
	{
		$this->checkList();
		return $this->get("lists/{$this->config->listId}/members/" . md5($email));
	}

	/**
	 * Add or update a list member
	 * @param string $email
	 * @param string $name
	 * @param string $surname
	 * @return stdClass|null
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function createMember(string $email, string $name = null, string $surname = null): ?stdClass
	{
		$this->checkList();
		$data = [
			'email_address' => $email,
			'status_if_new' => 'subscribed',
			'status' => 'subscribed'
		];
		if ($name !== null) {
			$data['merge_fields']['FNAME'] = $name;
		}
		if ($surname !== null) {
			$data['merge_fields']['LNAME'] = $surname;
		}
		return $this->put("lists/{$this->config->listId}/members/" . md5($email), $data);
	}

	/**
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	private function createStore(): void
	{
		if ($this->config->store !== null) {
			try {
				$response = $this->get('ecommerce/stores/' . $this->config->store->id);
				if ($response) {
					return;
				}
			} catch (MailChimpClientException $ex) {
			}
			$this->post('ecommerce/stores', [
				'id' => $this->config->store->id,
				'list_id' => $this->config->listId,
				'name' => $this->config->store->name,
				'domain' => $this->config->store->domain,
				'email_address' => $this->config->store->email,
				'currency_code' => $this->config->store->currency,
			]);
		}
	}

	/**
	 * @param Customer $customer
	 * @return null|stdClass
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function saveCustomer(Customer $customer): ?stdClass
	{
		$this->checkStore();
		return $this->put("ecommerce/stores/{$this->config->store->id}/customers/{$customer->id}", $customer->getData());
	}

	/**
	 * @param Product $product
	 * @return null|stdClass
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function saveProduct(Product $product)
	{
		$this->checkStore();
		$data = $product->getData();
		try {
			$response = $this->patch("ecommerce/stores/{$this->config->store->id}/products/{$product->id}", $data);
			if ($response) {
				return $response;
			}
		} catch (MailChimpClientException $ex) {
		}
		return $this->post("ecommerce/stores/{$this->config->store->id}/products", $data);
	}

	/**
	 * @param Cart $cart
	 * @return null|stdClass
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function saveCart(Cart $cart): ?stdClass
	{
		$this->checkStore();

		$cart->currency = $this->config->store->currency;
		if ($this->campaignId) {
			$cart->campaignId = $this->campaignId;
		}

		$data = $cart->getData();

		try {
			$response = $this->patch("ecommerce/stores/{$this->config->store->id}/carts/{$cart->id}", $data);
			if ($response) {
				return $response;
			}
		} catch (MailChimpClientException $ex) {
		}
		return $this->post("ecommerce/stores/{$this->config->store->id}/carts", $data);
	}

	/**
	 * @param string $cartId
	 * @return bool
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function deleteCart(string $cartId): bool
	{
		$this->checkStore();
		return $this->delete("ecommerce/stores/{$this->config->store->id}/carts/{$cartId}");
	}

	/**
	 * @param string $cartId
	 * @param Line $line
	 * @return null|stdClass
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function addCartLine(string $cartId, Line $line): ?stdClass
	{
		$this->checkStore();
		return $this->post("ecommerce/stores/{$this->config->store->id}/carts/{$cartId}/lines", $line->getData());
	}

	/**
	 * @param string $cartId
	 * @param string $cartLineId
	 * @param int $quantity
	 * @return null|stdClass
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function changeQuantityLine(string $cartId, string $cartLineId, int $quantity): ?stdClass
	{
		$this->checkStore();
		return $this->patch("ecommerce/stores/{$this->config->store->id}/carts/{$cartId}/lines/{$cartLineId}", [
			'quantity' => $quantity
		]);
	}

	/**
	 * @param string $cartId
	 * @param string $cartLineId
	 * @return bool
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function deleteCartLine(string $cartId, string $cartLineId): bool
	{
		$this->checkStore();
		return $this->delete("ecommerce/stores/{$this->config->store->id}/carts/{$cartId}/lines/$cartLineId");
	}

	/**
	 * @param Order $order
	 * @return null|stdClass
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function createOrder(Order $order): ?stdClass
	{
		$this->checkStore();

		$order->currency = $this->config->store->currency;
		if ($this->campaignId) {
			$order->campaignId = $this->campaignId;
		}

		$data = $order->getData();

		return $this->post("ecommerce/stores/{$this->config->store->id}/orders", $data);
	}

	/**
	 * @param string $id
	 * @return null|stdClass
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function setOrderPayed(string $id): ?stdClass
	{
		$this->checkStore();
		return $this->patch("ecommerce/stores/{$this->config->store->id}/orders/{$id}", [
			'financial_status' => 'paid'
		]);
	}

	/**
	 * @param string $id
	 * @return null|stdClass
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function setOrderRefunded(string $id): ?stdClass
	{
		$this->checkStore();
		return $this->patch("ecommerce/stores/{$this->config->store->id}/orders/{$id}", [
			'financial_status' => 'refunded'
		]);
	}

	/**
	 * @param string $id
	 * @return null|stdClass
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function setOrderCancelled(string $id): ?stdClass
	{
		$this->checkStore();
		return $this->patch("ecommerce/stores/{$this->config->store->id}/orders/{$id}", [
			'financial_status' => 'cancelled'
		]);
	}

	/**
	 * @param string $id
	 * @return null|stdClass
	 * @throws CredentialsNotSetException
	 * @throws MailChimpClientException
	 */
	public function setOrderShipped(string $id): ?stdClass
	{
		$this->checkStore();
		return $this->patch("ecommerce/stores/{$this->config->store->id}/orders/{$id}", [
			'fulfillment_status' => 'shipped'
		]);
	}
}
