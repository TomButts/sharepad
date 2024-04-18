# Sharepad

This project was created as a way for me to explore personal interests in web technologies.

So far basic note functionality, very basic sharing functionality, and real-time shared updates have been implemented.

## Build status

[![Super-Linter](https://github.com/TomButts/sharepad/actions/workflows/super-linter.yml/badge.svg)](https://github.com/marketplace/actions/super-linter)

## Installation

I have been using Docker Compose to serve the application during development which I will share in a separate repository. The set up I have been using is to put `sharepad/` folder on the same level as `docker_dev/` but with small edits to the `docker-compose.yml` you can mount the project from wherever you have it saved.

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

See an example image [showing a working spec](https://pasteboard.co/9o48aWdV2buP.png)
