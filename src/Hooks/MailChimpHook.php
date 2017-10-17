<?php

declare(strict_types=1);

namespace NAttreid\MailChimp\Hooks;

use NAttreid\Form\Form;
use NAttreid\MailChimp\CredentialsNotSetException;
use NAttreid\MailChimp\MailChimpClient;
use NAttreid\MailChimp\MailChimpClientException;
use NAttreid\WebManager\Services\Hooks\HookFactory;
use Nette\ComponentModel\Component;
use Nette\InvalidArgumentException;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

/**
 * Class MailChimpHook
 *
 * @author Attreid <attreid@gmail.com>
 */
class MailChimpHook extends HookFactory
{
	/** @var IConfigurator */
	protected $configurator;

	public function init(): void
	{
		$this->latte = __DIR__ . '/mailChimpHook.latte';

		if (!$this->configurator->mailChimp) {
			$this->configurator->mailChimp = new MailChimpConfig;
		}
	}

	/** @return Component */
	public function create(): Component
	{
		$form = $this->formFactory->create();

		$form->addText('apiKey', 'webManager.web.hooks.mailChimp.apiKey')
			->setDefaultValue($this->configurator->mailChimp->apiKey);

		try {
			$mailChimpClient = new MailChimpClient(false, $this->configurator->mailChimp);
			$lists = $mailChimpClient->findLists()->lists;
			$items = [];
			foreach ($lists as $row) {
				$items[$row->id] = $row->name;
			}
			$select = $form->addSelectUntranslated('list', 'webManager.web.hooks.mailChimp.list', $items, 'form.none');
			$select->addConditionOn($form['apiKey'], $form::FILLED)
				->addRule($form::FILLED);

			try {
				$select->setDefaultValue($this->configurator->mailChimp->listId);
			} catch (InvalidArgumentException $ex) {

			}
		} catch (CredentialsNotSetException $ex) {

		} catch (MailChimpClientException $ex) {
			Debugger::log($ex, Debugger::EXCEPTION);
		}

		$form->addSubmit('save', 'form.save');

		$form->onSuccess[] = [$this, 'mailchimpFormSucceeded'];

		return $form;
	}

	public function mailchimpFormSucceeded(Form $form, ArrayHash $values): void
	{
		$config = $this->configurator->mailChimp;

		$config->apiKey = $values->apiKey;
		$config->listId = empty($values->list) ? null : $values->list;

		$this->configurator->mailChimp = $config;

		$this->flashNotifier->success('default.dataSaved');

		$this->onDataChange();
	}
}