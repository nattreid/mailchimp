# MailChimp API pro Nette Framework

Nastavení v **config.neon**
```neon
extensions:
    smartEmailing: NAtrreid\MailChimp\DI\MailChimpExtension

smartEmailing:
    apiKey: 'apiKey'
    listId: 'fs5f4s68e' # vychozi seznam pro ukladani kontaktu
    debug: true # default false
    store:
        id: 'storeId'
        name: 'storeName'
        domain: 'storeDomain'
        email: 'storeEmail'
        currency: 'storeCurrency'
```

Použití

```php
/** @var NAttreid\MailChimp\MailChimpClient @inject */
public $mailChimp;

```
