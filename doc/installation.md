## Installation

*Note*: This Plugin currently supports ElasticSearch ^7.x.

```bash
$ composer require bitbag/crossselling-plugin --no-scripts
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

Remove the default ElasticSearch index (`app`) defined by `FOSElasticaBundle` in `config/packages/fos_elastica.yaml` (if the file doesn't exist, please create it with already updated info):
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

Before populating indexes, please clear the cache with the command:

```
$ bin/console cache:clear
```

Finally, with an elasticsearch server running, execute following command:
```
$ bin/console fos:elastica:populate
```

**Note:** If you are running it on production, add the `-e prod` flag to this command. Elasticsearch indexes are created with environment suffix, e.g. `related_products_dev`.


Configure webpack

- [Import webpack config](./01.1-webpack-config.md)

