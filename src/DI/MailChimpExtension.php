<?php

namespace NAttreid\MailChimp\DI;

use NAttreid\Cms\Configurator\Configurator;
use NAttreid\Cms\ExtensionTranslatorTrait;
use NAttreid\MailChimp\Hooks\MailChimpHook;
use NAttreid\MailChimp\MailChimpClient;
use NAttreid\WebManager\Services\Hooks\HookService;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\InvalidStateException;

/**
 * Class MailChimpExtension
 *
 * @author Attreid <attreid@gmail.com>
 */
class MailChimpExtension extends CompilerExtension
{
	use ExtensionTranslatorTrait;

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

		$hook = $builder->getByType(HookService::class);
		if ($hook) {
			$builder->addDefinition($this->prefix('mailChimpHook'))
				->setClass(MailChimpHook::class);

			$this->setTranslation(__DIR__ . '/../lang/', [
				'webManager'
			]);

			$config['dc'] = new Statement('?->mailchimpDC', ['@' . Configurator::class]);
			$config['apiKey'] = new Statement('?->mailchimpApiKey', ['@' . Configurator::class]);
			$config['listId'] = new Statement('?->mailchimpListId', ['@' . Configurator::class]);
		}

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