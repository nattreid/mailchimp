# MailChimp pro Nette Framework

Nastavení v **config.neon**
```neon
extensions:
    smartEmailing: NAtrreid\MailChimp\DI\MailChimpExtension

smartEmailing:
    dc: 'us1'
    key: 'apiKey'
    debug: true # default false
```

Použití

```php
/** @var NAttreid\MailChimp\MailChimpClient @inject */
public $mailChimp;

```
