<?php

namespace NAttreid\MailChimp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use stdClass;

/**
 * Class Client
 *
 * @author Attreid <attreid@gmail.com>
 */
class MailChimpClient
{
	/** @var Client */
	private $client;

	/** @var string */
	private $uri;

	/** @var string */
	private $apiKey;

	/** @var bool */
	private $debug;

	/**
	 * Client constructor.
	 * @param bool $debug
	 * @param string $apiKey
	 */
	public function __construct($debug, $apiKey, $dc)
	{
		$this->uri = "https://$dc.api.mailchimp.com/3.0";
		$this->apiKey = $apiKey;
		$this->debug = (bool)$debug;
	}

	/**
	 * @param ResponseInterface $response
	 * @return mixed
	 */
	private function getResponse(ResponseInterface $response)
	{
		$json = $response->getBody()->getContents();
		if (!empty($json)) {
			return Json::decode($json);
		}
		return null;
	}

	private function getClient()
	{
		if ($this->client === null) {
			$this->client = new Client(['base_uri' => $this->uri]);
		}
		return $this->client;
	}

	/**
	 * @param string $method
	 * @param string $url
	 * @param array $args
	 * @return bool|stdClass
	 * @throws CredentialsNotSetException
	 */
	private function request($method, $url, array $args = [])
	{
		if (empty($this->apiKey)) {
			throw new CredentialsNotSetException('ApiKey must be set');
		}

		try {
			$options = [
				RequestOptions::AUTH => [
					'user',
					$this->apiKey
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
					return true;
			}
		} catch (ClientException $ex) {
			switch ($ex->getCode()) {
				case 404:
				case 422:
					if ($this->debug) {
						throw $ex;
					} else {
						return false;
					}
				case 401:
					throw $ex;
			}
		}
		return false;
	}

	/**
	 * @param string $url
	 * @return bool|stdClass
	 */
	private function get($url)
	{
		return $this->request('GET', $url);
	}

	/**
	 * @param string $url
	 * @param string[] $args
	 * @return bool|stdClass
	 */
	private function post($url, array $args = [])
	{
		return $this->request('POST', $url, $args);
	}

	/**
	 * @param string $url
	 * @return bool
	 */
	private function delete($url)
	{
		return $this->request('DELETE', $url);
	}

	/**
	 * @param string $url
	 * @param string[] $args
	 * @return bool|stdClass
	 */
	private function patch($url, array $args = [])
	{
		return $this->request('PATCH', $url, $args);
	}

	/**
	 * Aliveness test
	 * @return stdClass
	 */
	public function ping()
	{
		return $this->get('');
	}
}