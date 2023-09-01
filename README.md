# Basic Login App

This is an App stub created to satisfy the requirements of a development test. It does login through Symfony.

## Technologies Used

This App stub is coded in PHP and TypeScript/JavaScript and is based on the Symfony and React frameworks.

[Vite](https://vitejs.dev/guide/) is used for the transpilation of the React files.

Authentication is handled through the [LexikJWTAuthenticationBundle](https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/index.html)

## How To Configure It

This App allows environment variables for DB_HOST, DB_NAME, DB_USER and DB_PASS to be configured. The default db is a mysql server called mysql.local.

If running locally, public and private keys **must** be generated for the [LexikJWTAuthenticationBundle](https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/index.html#generate-the-ssl-keys)

```bash
$ php bin/console lexik:jwt:generate-keypair
```

If running locally, then for the tests a separate [key pair](https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/3-functional-testing.html#configuration) **must** be configured using the passphrase saved in the .env file

```bash
$ openssl genrsa -out config/jwt/private-test.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private-test.pem -out config/jwt/public-test.pem
```

## How to Run It

This repo comes with a Dockerfile built on top of [Nginx Unit](https://unit.nginx.org/howto/symfony/) and a docker-compose.yml file for running on Docker Compose. Ancillary containers are configured in the docker compose file for mysql and mailcatcher.

The Nginx Unit config requires the setup of a self-signed certificate. This can be generated using the ```create-cert.sh``` script. This is currently configured to create a cert for the domain https://basic-login-symfony.local. Modify the script to change the domain to something more suitable, if required. Add the domain to your hosts file for easier interactions with the site through HTTPS.

```bash
$ chmod +x ./create-cert.sh && ./create-cert.sh
```

To build and run the docker containers run 

```bash
$ docker-compose up -d basic-login-symfony-app
```

This will generate a Symfony API and React App. The Symfony API is available at http://localhost/api and the App is available at http://localhost/. For HTTPS, use the domain name configured in the create-cert.sh script above.

To install composer dependencies locally (assuming a composer executable is available), run

```bash
$ composer install
```

For local development and the [vite](https://vitejs.dev/guide/) dev server with live reloading, run

```bash 
$ cd react-app && npm install && cp -f ./environment.dev.tsx.example ./environment.dev.tsx && npm run dev
```

Edit the ```react-app/environment.dev.tsx``` file to specify a different address for the backend. This is the equivalent of an environment variable for this project.

The Vite dev server serves the React App at localhost. The port can vary.


For production building (which will perform linting and thorough error checking) of the React/TypeScript App locally, run 

```bash
$ cd react-app && npm install && npm run build
```


To run the database migrations from the project root directory

```bash
$ docker-compose exec basic-login-symfony-app bash
$ cd /www && php bin/console doctrine:migrations:migrate
```

To create the test database from the root directory

```bash
$ docker-compose exec basic-login-symfony-app bash
$ cd /www && php bin/console --env=test doctrine:database:create
$ cd /www && php bin/console --env=test doctrine:schema:create
```

The [NelmioCorsBundle](https://symfony.com/bundles/NelmioCorsBundle/current/index.html) allows requests from localhost in the dev environment. This is useful when running the Vite Dev Server.

## Structure

All controllers are coded in accordance with the latest guidance from the Symfony docs. Attributes are used heavily for the methods in these. In cases where attributes are used, docblocks are minimal.

## Post-Development Considerations

The React components could do with refactoring as there is some repetition in how forms are managed. The reducers should be reduced to one.

The components need to be tested in some manner. More time would be needed for this.

## TODO

Add CSP Headers for the frontend. These are standard but are not obviously configurable with Nginx Unit. There is an open [issue](https://github.com/nginx/unit/issues/313) for this. 

App is very basic, but should probably have a change password route and page for easier manual acceptance testing.

Separate some DOM elements out into reusable components.

Get UniqueEntity validation rule working as it seems unnecessary to have to create a UniqueEmail validator when this is a standard use-case. Error is possibly somewhere in the service config.

## How To Test It

There are application tests included with this repo. They test that the API rejects invalid fields and accepts valid ones.

```sh
$ docker-compose exec basic-login-symfony-app bash
$ cd /www && openssl genrsa -out config/jwt/private-test.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private-test.pem -out config/jwt/public-test.pem
$ cd /www && php bin/phpunit
```
