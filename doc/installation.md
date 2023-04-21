## Installation

*Note*: This Plugin currently supports ElasticSearch 5.3.x up to 6.8.x.  ElasticSearch ^7.x is not currently supported.

```bash
$ composer require bitbag/crossselling-plugin
```

Add plugin dependencies to your `config/bundles.php` file:

```php
return [
    ...
    
    FOS\ElasticaBundle\FOSElasticaBundle::class => ['all' => true],
    BitBag\SyliusCrossSellingPlugin\BitBagSyliusCrossSellingPlugin::class => ['all' => true],
];
```

Import required config in your `config/packages/_sylius.yaml` file:
```yaml
# config/packages/_sylius.yaml

imports:
    ...

    - { resource: "@BitBagSyliusCrossSellingPlugin/Resources/config/config.yaml" }
```

Remove the default ElasticSearch index (`app`) defined by `FOSElasticaBundle` in `config/packages/fos_elastica.yaml`:
```

fos_elastica:
    clients:
        default: { host: localhost, port: 9200 }
    indexes:
        app: ~
```
should become:
```

fos_elastica:
    clients:
        default: { host: localhost, port: 9200 }
```

Finally, with an elasticsearch server running, execute following command:
```
$ bin/console fos:elastica:populate
```

**Note:** If you are running it on production, add the `-e prod` flag to this command. Elasticsearch indexes are created with environment suffix, e.g. `related_products_dev`.


Configure webpack

- [Import webpack config](./01.1-webpack-config.md)

