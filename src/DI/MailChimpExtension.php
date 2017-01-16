<?php

namespace NAttreid\SmartEmailing\DI;

use NAttreid\MailChimp\MailChimpClient;
use Nette\DI\CompilerExtension;
use Nette\InvalidStateException;

/**
 * Class MailChimpExtension
 *
 * @author Attreid <attreid@gmail.com>
 */
class MailChimpExtension extends CompilerExtension
{
	private $defaults = [
		'apiKey' => null,
		'dc' => null,
		'listId' => null,
		'debug' => false
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		if ($config['apiKey'] === null) {
			throw new InvalidStateException("MailChimp: 'apiKey' does not set in config.neon");
		}

		if ($config['dc'] === null) {
			throw new InvalidStateException("MailChimp: 'dc' does not set in config.neon");
		}

		if ($config['listId'] === null) {
			throw new InvalidStateException("MailChimp: 'listId' does not set in config.neon");
		}

		$builder->addDefinition($this->prefix('client'))
			->setClass(MailChimpClient::class)
			->setArguments([$config['debug'], $config['apiKey'], $config['dc']])
			->addSetup('setListId', [$config['listId']]);
	}
}