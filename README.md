# Sharepad

This project was created as a way for me to explore personal interests in web technologies.

So far basic note functionality, very basic sharing functionality, and real-time shared updates have been implemented.

## Build status

[![Super-Linter](https://github.com/TomButts/sharepad/actions/workflows/super-linter.yml/badge.svg)](https://github.com/marketplace/actions/super-linter)

### Symfony App Initialisation

Install the project dependencies

```bash
composer install
```

Initialise the db schema

```bash
php bin/console doctrine:schema:create
```

Run the migrations to further set up data structures

```bash
php bin/console doctrine:migrations:migrate
```

Load up local test user for aid in development

```bash
php bin/console doctrine:fixtures:load
```

### Vue App Initialisation

Install npm packages

```bash
npm install
```

Run the vue app

```bash
npm run watch
```

### Testing

To open cypress test suite

```bash
npx cypress open
```
