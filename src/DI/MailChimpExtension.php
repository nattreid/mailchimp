<?php

namespace NAttreid\SmartEmailing\DI;

use NAttreid\MailChimp\MailChimpClient;
use Nette\DI\CompilerExtension;
use Nette\InvalidStateException;
use Nette\Utils\Strings;

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
		'debug' => false
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		if ($config['apiKey'] === null) {
			throw new InvalidStateException("MailChimp: 'apiKey' does not set in config.neon");
		}

		if (Strings::match($config['dc'], '/^(us[1-9]|1[0-4])$/') === null) {
			throw new InvalidStateException("MailChimp: 'dc' is invalid in config.neon (available: us1 - us14)");
		}

		$builder->addDefinition($this->prefix('client'))
			->setClass(MailChimpClient::class)
			->setArguments([$config['debug'], $config['apiKey'], $config['dc']]);
	}
}