# MailChimp pro Nette Framework

Nastavení v **config.neon**
```neon
extensions:
    smartEmailing: NAtrreid\MailChimp\DI\MailChimpExtension

smartEmailing:
    dc: 'us1'
    apiKey: 'apiKey'
    listId: 'fs5f4s68e' # vychozi seznam pro ukladani kontaktu
    debug: true # default false
```

Použití

```php
/** @var NAttreid\MailChimp\MailChimpClient @inject */
public $mailChimp;

```
