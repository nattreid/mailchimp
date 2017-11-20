<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\DI;

use NAttreid\Cms\Configurator\Configurator;
use NAttreid\Cms\DI\ExtensionTranslatorTrait;
use NAttreid\MailChimp\Hooks\MailChimpHook;
use NAttreid\MailChimp\MailChimpClient;
use NAttreid\WebManager\Services\Hooks\HookService;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\DI\Statement;

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
		'listId' => null,
		'debug' => false,
		'store' => null,
		'tempDir' => '%tempDir%'
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		$store = $config['store'];
		if ($store !== null) {
			$mailChimpStore = $builder->addDefinition($this->prefix('store'))
				->setType(MailChimpStore::class)
				->addSetup(new Statement('$service->id = ?', [$store['id'] ?? null]))
				->addSetup(new Statement('$service->name = ?', [$store['name']]))
				->addSetup(new Statement('$service->domain = ?', [$store['domain']]))
				->addSetup(new Statement('$service->email = ?', [$store['email']]))
				->addSetup(new Statement('$service->currency = ?', [$store['currency']]));
		}

		$mailChimpConfig = $builder->addDefinition($this->prefix('config'))
			->setType(MailChimpConfig::class)
			->addSetup(new Statement('$service->apiKey = ?', [$config['apiKey']]))
			->addSetup(new Statement('$service->listId = ?', [$config['listId']]));

		if (isset($store['id'])) {
			$mailChimpConfig->addSetup(new Statement('$service->store = ?', [$mailChimpStore]));
		}

		$hook = $builder->getByType(HookService::class);
		if ($hook) {
			$builder->addDefinition($this->prefix('mailChimpHook'))
				->setType(MailChimpHook::class)
				->addSetup('setConfig', [$mailChimpConfig, '@' . MailChimpStore::class]);

			$this->setTranslation(__DIR__ . '/../lang/', [
				'webManager'
			]);

			$mailChimpConfig = new Statement('?->mailChimp \?: ?', ['@' . Configurator::class, '@' . MailChimpConfig::class]);
		}

		$tempDir = Helpers::expand($config['tempDir'], $builder->parameters);

		$builder->addDefinition($this->prefix('client'))
			->setType(MailChimpClient::class)
			->setArguments([$config['debug'], $mailChimpConfig, $tempDir]);
	}
}