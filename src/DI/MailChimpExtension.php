<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\DI;

use NAttreid\Cms\Configurator\Configurator;
use NAttreid\Cms\DI\ExtensionTranslatorTrait;
use NAttreid\MailChimp\Hooks\MailChimpConfig;
use NAttreid\MailChimp\Hooks\MailChimpHook;
use NAttreid\MailChimp\MailChimpClient;
use NAttreid\WebManager\Services\Hooks\HookService;
use Nette\DI\CompilerExtension;
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
		'debug' => false
	];

	public function loadConfiguration(): void
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

			$mailChimp = new Statement('?->mailChimp \?: new ' . MailChimpConfig::class, ['@' . Configurator::class]);
		} else {
			$mailChimp = new MailChimpConfig;
			$mailChimp->apiKey = $config['apiKey'];
			$mailChimp->listId = $config['listId'];
		}

		$builder->addDefinition($this->prefix('client'))
			->setClass(MailChimpClient::class)
			->setArguments([$config['debug'], $mailChimp]);
	}
}