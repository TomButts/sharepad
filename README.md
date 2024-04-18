
This project was created as a way for me to explore personal interests in web technologies. The app could be called an Apple Notes clone at a very early stage. So far basic note functionality, very basic sharing functionality, and real-time shared updates have been implemented.

My main goal with this application is not to create a fully functioning Apple Notes clone, but to create a minimum viable product, and then continue exploring things around the application like testing frameworks, learning Vue best practices, setting up deployment pipelines (with a view to CI/CD), and architecting AWS services.

The project only has a couple of weeks' worth of dev time at the time of writing, but I have been looking into Cypress test framework and have implemented real-time updates using Symfony Mercure connector.

# Build status

[![Super-Linter](https://github.com/TomButts/sharepad/actions/workflows/super-linter.yml/badge.svg)](https://github.com/marketplace/actions/super-linter)


# Installation

I have been using Docker Compose to serve the application during development which I will share in a separate repository. The set up I have been using is to put `sharepad/` folder on the same level as `docker_dev/` but with small edits to the `docker-compose.yml` you can mount the project from wherever you have it saved.


### Symfony App Initialisation

Install the project dependencies
```
composer install
```

Initialise the db schema
```
php bin/console doctrine:schema:create
```
Run the migrations to further set up data structures
```
php bin/console doctrine:migrations:migrate
```
Load up local test user for aid in development
```
php bin/console doctrine:fixtures:load
```

### Vue App Initialisation

Install npm packages
```
npm install
```

Run the vue app
```
npm run watch
```
### Testing

To open cypress test suite
```
npx cypress open
```

See an example image [showing a working spec](https://pasteboard.co/9o48aWdV2buP.png)