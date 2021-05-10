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
