<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\DI;

use NAttreid\Cms\Configurator\Configurator;
use NAttreid\Cms\DI\ExtensionTranslatorTrait;
use NAttreid\MailChimp\Hooks\MailChimpHook;
use NAttreid\WebManager\Services\Hooks\HookService;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;

if (trait_exists('NAttreid\Cms\DI\ExtensionTranslatorTrait')) {
	class MailChimpExtension extends AbstractMailChimpExtension
	{
		use ExtensionTranslatorTrait;

		protected function prepareConfig(ServiceDefinition $mailChimpConfig)
		{
			$builder = $this->getContainerBuilder();
			$hook = $builder->getByType(HookService::class);
			if ($hook) {
				$builder->addDefinition($this->prefix('mailChimpHook'))
					->setType(MailChimpHook::class)
					->addSetup('setConfig', [$mailChimpConfig, '@' . MailChimpStore::class]);

				$this->setTranslation(__DIR__ . '/../lang/', [
					'webManager'
				]);

				return new Statement('?->mailChimp \?: ?', ['@' . Configurator::class, '@' . MailChimpConfig::class]);
			} else {
				return parent::prepareConfig($mailChimpConfig);
			}
		}
	}
} else {
	class MailChimpExtension extends AbstractMailChimpExtension
	{
	}
}