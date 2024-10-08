# Installation

## Overview:
GENERAL
- [Requirements](#requirements)
- [Composer](#composer)
- [Basic configuration](#basic-configuration)
---
FRONTEND
- [Webpack](#webpack)
---
ADDITIONAL
- [Known Issues](#known-issues)
---

## Requirements:
We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.

| Package       | Version  |
|---------------|----------|
| PHP           | \>=8.2   |
| sylius/sylius | \>=2.0.0 |
| MySQL         | \>= 5.7  |
| NodeJS        | \>= 20.x |
| ElasticSearch | \>= 7.x  |

## Composer:
```bash
composer require bitbag/crossselling-plugin --no-scripts
```

## Basic configuration:
Add plugin dependencies to your `config/bundles.php` file:

```php
# config/bundles.php

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

Remove the default ElasticSearch index (`app`) defined by `FOSElasticaBundle` in `config/packages/fos_elastica.yaml`
(if the file doesn't exist, please create it with already updated info):

```yaml
# config/packages/fos_elastica.yaml

fos_elastica:
    clients:
        default: { host: localhost, port: 9200 }
    indexes:
        app: ~
```

...should become:

```yaml
# config/packages/fos_elastica.yaml

fos_elastica:
    clients:
        default: { host: localhost, port: 9200 }
```

### Clear application cache by using command:
```bash
bin/console cache:clear
```

### Finally, with an elasticsearch server running, execute following command:
```bash
bin/console fos:elastica:populate
```

**Note:** If you are running it on production, add the `-e prod` flag to these commands.
Elasticsearch indexes are created with environment suffix, e.g. `related_products_dev`.

## Webpack
### Webpack.config.js

Please setup your `webpack.config.js` file to require the plugin's webpack configuration. To do so, please put the line below somewhere on top of your webpack.config.js file:
```js
const [bitbagCsShop, bitbagCsAdmin] = require('./vendor/bitbag/crossselling-plugin/webpack.config.js');
```
As next step, please add the imported consts into final module exports:
```js
module.exports = [..., bitbagCsShop, bitbagCsAdmin];
```

### Assets
Add the asset configuration into `config/packages/assets.yaml`:
```yaml
framework:
    assets:
        packages:
            ...
            cs_shop:
                json_manifest_path: '%kernel.project_dir%/public/build/bitbag/cs/shop/manifest.json'
            cs_admin:
                json_manifest_path: '%kernel.project_dir%/public/build/bitbag/cs/admin/manifest.json'
```

### Webpack Encore
Add the webpack configuration into `config/packages/webpack_encore.yaml`:

```yaml
webpack_encore:
    output_path: '%kernel.project_dir%/public/build/default'
    builds:
        ...
        cs_shop: '%kernel.project_dir%/public/build/bitbag/cs/shop'
        cs_admin: '%kernel.project_dir%/public/build/bitbag/cs/admin'
```

### Encore functions
Add encore functions to your templates:

SyliusAdminBundle:
```php
{# @SyliusAdminBundle/_scripts.html.twig #}
{{ encore_entry_script_tags('bitbag-cs-admin', null, 'cs_admin') }}

{# @SyliusAdminBundle/_styles.html.twig #}
{{ encore_entry_link_tags('bitbag-cs-admin', null, 'cs_admin') }}
```
SyliusShopBundle:
```php
{# @SyliusShopBundle/_scripts.html.twig #}
{{ encore_entry_script_tags('bitbag-cs-shop', null, 'cs_shop') }}

{# @SyliusShopBundle/_styles.html.twig #}
{{ encore_entry_link_tags('bitbag-cs-shop', null, 'cs_shop') }}
```

### Run commands
```bash
yarn install
yarn encore dev # or prod, depends on your environment
```

## Known issues
### Translations not displaying correctly
For incorrectly displayed translations, execute the command:
```bash
bin/console cache:clear
```
