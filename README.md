<h1 align="center">
    <a href="http://bitbag.shop" target="_blank">
        <img src="doc/logo.png" width="55%" />
    </a>
    <br />
    <a href="https://packagist.org/packages/bitbag/upselling-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/bitbag/upselling-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/bitbag/upselling-plugin" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/bitbag/upselling-plugin.svg" />
    </a>
    <a href="http://travis-ci.org/BitBagCommerce/SyliusUpsellingPlugin" title="Build status" target="_blank">
            <img src="https://img.shields.io/travis/BitBagCommerce/SyliusUpsellingPlugin/master.svg" />
        </a>
    <a href="https://scrutinizer-ci.com/g/BitBagCommerce/SyliusUpsellingPlugin/" title="Scrutinizer" target="_blank">
        <img src="https://img.shields.io/scrutinizer/g/BitBagCommerce/SyliusUpsellingPlugin.svg" />
    </a>
    <a href="https://packagist.org/packages/bitbag/upselling-plugin" title="Total Downloads" target="_blank">
        <img src="https://poser.pugx.org/bitbag/upselling-plugin/downloads" />
    </a>
</h1>

## About us

At BitBag we do believe in open source. However, we are able to do it just beacuse of our awesome clients, who are kind enough to share some parts of our work with the community. Therefore, if you feel like there is a possibility for us working together, feel free to reach us out. You will find out more about our professional services, technologies and contact details at https://bitbag.io/.

## BitBag SyliusUpsellingPlugin

SyliusUpsellingPluging adds a new section to product page called "Related products". It displays other products bought together with the viewed one, and if there isn't enough data, it displays other products with the same taxon.

See below for more details about the algorithm.


## Requirements
| Package       | Version             |
|---------------|---------------------|
| PHP           | \>= 7.3             |
| Elasticsearch | \^5.3.x &#124; ^6.x |

## Installation

*Note*: This Plugin currently supports ElasticSearch 5.3.x up to 6.8.x.  ElasticSearch ^7.x is not currently supported.

```bash
$ composer require bitbag/upselling-plugin
```

Add plugin dependencies to your `config/bundles.php` file:
```php
return [
    ...
    
    FOS\ElasticaBundle\FOSElasticaBundle::class => ['all' => true],
    BitBag\SyliusUpsellingPlugin\BitBagSyliusUpsellingPlugin::class => ['all' => true],
];
```

Import required config in your `config/packages/_sylius.yaml` file:
```yaml
# config/packages/_sylius.yaml

imports:
    ...

    - { resource: "@BitBagSyliusUpsellingPlugin/Resources/config/config.yaml" }
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

## Usage

### Rendering the shop products list

Now, when you go to the product page at `/{_locale}/products/{slug}`, you will see a new section at the bottom called "Related products".

### Algorithm
Related products are calculated using the following priorities:

#### 1. Order history

- Check product order history. Count the number of orders which contained both the original product and the recommended product. The product with the highest number of common orders is recommended first, then the second one, and so on.
    * Each order counts as 1. The number of items in the order doesn't matter.

#### 2. Common taxons 
- If there hadn't been enough common orders to display the requested number of related products, the original product's taxons are checked. Other products that share a taxon are then displayed.
    * Latest products are displayed first.
    * Original product's main taxon is checked first.
    * If there aren't enough other products that use original product's main taxon, original product's other taxons are checked, one after another.
    
#### 3. Remaining slots
- After above options are exhausted, no more related products are displayed, even if the list is shorter than requested.

## Customization

### Twig template
The related products section is rendered by `Resources/views/Shop/Product/_relatedProducts.html.twig`. You can override this template to modify it to your needs.

### Rendering related products list
`bitbag_upselling_render_related_products()` Twig function renders the individual products. You can call it with optional parameters:
```php
// displays 8 most related products using @customTemplate.html.twig
{{ bitbag_upselling_render_related_products(product.slug, 8, '@customTemplate.html.twig') }}
```

### Parameters you can override in your parameters.yml(.dist) file
```bash
$ bin/console debug:container --parameters | grep bitbag
```

## Testing

Setup test environment:
```bash
$ composer install
$ cd tests/Application
$ yarn install
$ yarn build
$ bin/console assets:install public -e test
$ bin/console doctrine:database:create -e test
$ bin/console doctrine:schema:create -e test
$ bin/console server:run 127.0.0.1:8080 -d public -e test
$ sudo service elasticsearch start
```

Run the tests (from project root)
```bash
$ vendor/bin/phpspec run
$ vendor/bin/phpunit
$ vendor/bin/behat --strict
```

## Contribution

Learn more about our contribution workflow on http://docs.sylius.org/en/latest/contributing/.
