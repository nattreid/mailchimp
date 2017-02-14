<?php

namespace NAttreid\MailChimp\Hooks;

use GuzzleHttp\Exception\ClientException;
use IPub\FlashMessages\FlashNotifier;
use NAttreid\Cms\Configurator\Configurator;
use NAttreid\Cms\Factories\FormFactory;
use NAttreid\Form\Form;
use NAttreid\MailChimp\MailChimpClient;
use NAttreid\WebManager\Services\Hooks\HookFactory;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;

/**
 * Class MailChimpHook
 *
 * @author Attreid <attreid@gmail.com>
 */
class MailChimpHook extends HookFactory
{
	/** @var IConfigurator */
	protected $configurator;

	/** @var MailChimpClient */
	private $mailChimpClient;

	public function __construct(FormFactory $formFactory, Configurator $configurator, FlashNotifier $flashNotifier, MailChimpClient $mailChimpClient)
	{
		parent::__construct($formFactory, $configurator, $flashNotifier);
		$this->mailChimpClient = $mailChimpClient;
	}

	/** @return Form */
	public function create()
	{
		$form = $this->formFactory->create();

		$form->addText('apiKey', 'webManager.web.hooks.mailChimp.apiKey')
			->setDefaultValue($this->configurator->mailchimpApiKey);

		try {
			$lists = $this->mailChimpClient->findLists()->lists;
			$items = [];
			foreach ($lists as $row) {
				$items[$row->id] = $row->name;
			}
			$select = $form->addSelectUntranslated('list', 'webManager.web.hooks.mailChimp.list', $items, 'form.none');

			$select->setDefaultValue($this->configurator->mailchimpListId);

		} catch (ClientException $ex) {
		} catch (\NAttreid\MailChimp\CredentialsNotSetException $ex) {
		} catch (InvalidArgumentException $ex) {
		} catch (InvalidStateException $ex) {
		}

		$form->addSubmit('save', 'form.save');

		$form->onSuccess[] = [$this, 'mailchimpFormSucceeded'];

		return $form;
	}

	public function mailchimpFormSucceeded(Form $form, $values)
	{
		@list(, $dc) = explode('-', $values->apiKey);
		$this->configurator->mailchimpDC = $dc;
		$this->configurator->mailchimpApiKey = $values->apiKey;
		$this->configurator->mailchimpListId = isset($values->list) ? $values->list : null;

		$this->flashNotifier->success('default.dataSaved');
	}
}