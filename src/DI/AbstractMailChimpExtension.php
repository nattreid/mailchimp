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
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;

/**
 * Class AbstractMailChimpExtension
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class AbstractMailChimpExtension extends CompilerExtension
{
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

		$mailChimpConfig = $builder->addDefinition($this->prefix('config'))
			->setType(MailChimpConfig::class)
			->addSetup(new Statement('$service->apiKey = ?', [$config['apiKey']]))
			->addSetup(new Statement('$service->listId = ?', [$config['listId']]));

		$store = $config['store'];
		if ($store !== null) {
			$mailChimpStore = $builder->addDefinition($this->prefix('store'))
				->setType(MailChimpStore::class)
				->addSetup(new Statement('$service->id = ?', [$store['id'] ?? null]))
				->addSetup(new Statement('$service->name = ?', [$store['name']]))
				->addSetup(new Statement('$service->domain = ?', [$store['domain']]))
				->addSetup(new Statement('$service->email = ?', [$store['email']]))
				->addSetup(new Statement('$service->currency = ?', [$store['currency']]));

			if (isset($store['id'])) {
				$mailChimpConfig->addSetup(new Statement('$service->store = ?', [$mailChimpStore]));
			}
		}

		$mailChimpConfig = $this->prepareConfig($mailChimpConfig);

		$tempDir = Helpers::expand($config['tempDir'], $builder->parameters);

		$builder->addDefinition($this->prefix('client'))
			->setType(MailChimpClient::class)
			->setArguments([$config['debug'], $mailChimpConfig, $tempDir]);
	}

	protected function prepareConfig(ServiceDefinition $mailChimpConfig)
	{
		return $mailChimpConfig;
	}
}