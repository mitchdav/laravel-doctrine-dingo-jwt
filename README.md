# Tempest Tools Skeleton

This is a Skeleton that I am crafting that will contain all the commonly required features of web applications, and a set of tools for building APIs faster, more securely, and more extensively than any other project I have encountered.

Over the nearly 15 years I have spent making web apps, I have faced many poorly written projects. I spent this time collecting notes on what I think an ideal project would look like and thinking about the best way to create a tool set to write API's that meet the following requirements: 
1. Are as fast as possible to set up and configure
2. Secure
3. Flexible and extensible enough to allow the core code of the API to be easily adapted to any scenario
4. Consistent to use and understand.

I also have always wondered why it is that there’s a set of features which 99% of web apps require, and yet there is no way to start a project with all those features already authored and working together. 

My goal is to have Tempest Tools, and this Skeleton, be my answer to those needs; an ideal starting place for new Laravel projects. The Skeleton I am crafting will contain all the commonly required features of web applications, and a set of tools for building APIs faster, more securely, and more extensively than any other project.

As new tools are added to the Skeleton, I will wire them together. Therefore, the Skeleton will serve as a working example of all the tools included, and as the perfect starting place for any new Laravel project.

This Skeleton also houses the test cases for other Tempest Tools packages.

Tempest Tools Skeleton is a work in progress, so if you encounter a problem please report it, and/or fire off a pull request to fix the issue.

Also note that the packages folder holds the 1st party components.

Tempest Tools Skeleton is a fork of Laravel versioned API. Please see: https://github.com/mitchdav/laravel-versioned-api

## What’s currently included in v1.0.0 (3rd Party)

1. Everything that laravel-versioned-api ships with
2. https://packagist.org/packages/laravel-doctrine/extensions
3. https://packagist.org/packages/gedmo/doctrine-extensions
4. https://packagist.org/packages/beberlei/DoctrineExtensions
5. https://packagist.org/packages/laravel-doctrine/migrations
6. https://packagist.org/packages/laravel-doctrine/acl

## What’s currently included in v1.0.0 (1st Party)
1. https://github.com/tempestwf/tempest-tools-common
  a. Contains common libraries used by other Tempest Tools packages.
2. https://github.com/tempestwf/tempest-tools-moat
  a. Contains easily configurable middleware that can be applied to any route restrict access to it based on a templating system that allows developers to easily define which permissions (stored in the database VIA;  https://packagist.org/packages/laravel-doctrine/acl) are required to access it.

## What’s currently in development
1. Tempest Tools CRUD
  a. The fastest, most secure, and extendable way to write RESTful APIs
2. Tempest Tools Cache Tagging System
  a. Tag cache items based on the templating system from Tempest Tools Common ArrayHelper. Apply tags to cache in distributed cache environments that don’t commonly support it.
3. User management system
  a. A user management system based on Tempest Tools CRUD

## What’s planned:
1. Improved error handling and logging.
2. Support for code modularization.
3. Better implementation of Gedmo extensions (applying the extensions to provided entities).
4. Full Text Search.
5. Result Caching.
6. Sign Up / User email verification.
7. Oauth sign in from 3rd party services (google, facebook, twitter).
8. Database sharding for large datasets.
9. Improved localization features.

## Requirements

* PHP >= 7.1.0

* A database (MySQL, or any others supported by Laravel and Doctrine)
* [Composer](https://getcomposer.org/).

## Installation

You may fork the repo first and then update the git clone command below. Or you may optionally save the cloned repo into your own repository.

Note: Make sure to turn off xdebug before running composer install as it can cause it to hang indefinitely (setting xdebug: "xdebug.remote_autostart= 0" should fix the problem).
Clone the repo manually by running the following commands in the terminal. We do a manual clone so we can run the sub module commands:

    git clone https://github.com/tempestwf/tempest-tools-skeleton {name of directory to clone into}
    cd {name of directory to clone into}
    git submodule init
    git submodule update
    composer install
    php -r "copy('.env.example', '.env');"
    php artisan key:generate
    php artisan jwt:generate

If you want a vanilla implementation that does not include the albums and artists and tests related to them you may switch to the vanilla branch.

    git checkout 2.0.2-vanilla

This will setup the project's dependencies, however you will still need to setup the database. You must first create a MySQL database, and then store its details in the .env file like so:

    DB_DATABASE={database}
    DB_USERNAME={username}
    DB_PASSWORD={password}

Also in the .env file you must set up a base user. This user will be added to the DB, and the test cases that require a user will run using this users

    BASE_USER_NAME="{name}"
    BASE_USER_EMAIL={email}
    BASE_USER_PASSWORD={password}

Give your API a name in the .env

    API_NAME="{API Name}"

Set your additional information in the .env file

    APP_URL=http://{site url}
    API_DOMAIN=P{domain}

If at this point you would like to save your modified repo into it's own repository you may do so with the following command.

    git push https://{username}:{password}@github.com/{your github user}/{your repo} +{branch of the skeleton you want to copy}:master

Set the origin of your repo to the new location you pushed it too

     git remote set-url origin https://{username}:{password}@github.com/{your github user}/{your repo}

To then setup the database we use Doctrine's helper command to build our schema and proxies.

    php artisan doctrine:migrations:migrate
    php artisan doctrine:generate:proxies

Remember to also set up the host on your system before testing as well.

## Logging In
Make a POST request to ```/auth/authenticate``` with ```Content-Type``` set to ```application/json```. The JSON structure should look like the following:
	
	{
	    "email": "{email address from env}",
	    "password": "{email address from env}"
	}

If the response is successful, you will receive a token which you can use to make subsequent requests to the server, while remaining authenticated as the given user. To send through the token value send it in the ```Authorization``` header as follows:

	Authorization: Bearer {token}
	
To retrieve the user details make a GET request to ```/auth/me``` and you will receive a request similar to the following:

    {
        "id": 1,
        "email": "theodora39@example.net",
        "name": "Dillon Effertz",
        "job": "Roofer"
    }

For further information about making requests to the server check the [Dingo API Wiki](https://github.com/dingo/api/wiki).
	
## Developing The Server
As you make changes to the entities, you need to generate proxies for your entities, so that the system can load quickly for each request. To do this, run the following command in the terminal:

	php artisan doctrine:generate:proxies

This will not be necessary unless you modify the entities as their proxies are already generated and committed.

If your changes to an entity modify its database structure, you can create a migration based on change by running the following command:

	php artisan doctrine:migrations:diff
	php artisan doctrine:migrations:migrate

## Routes
Routes for your versions are available by looking at the [routes file].

See: tempest-tools-moat (https://github.com/tempestwf/tempest-tools-moat) for information on adding acl middleware to your routes.

## Adding New Versions
The important files are all contained in the ```app/API``` folder. The project has 3 different versions of the same API, which is kept very simple to be as extendable as possible.

When you would like to add a new version, you will need to follow this process:

1. Copy the whole previous version's folder into the ```app/API``` folder and give it a suitable name (for example, copy the ```app/API/V3``` folder to ```app/API/V4```)
2. Do a search and replace operation to update the folder's references from the previous version to the new version (for example, update all instances of ```V3``` to ```V4``` within the ```V4``` folder)
3. Copy any existing routes from the previous version to the new version in the routes file
4. Update the ```config/api.php``` file to include the new version in the ```auth``` section
5. Update the ```.env``` file's ```API_VERSION``` variable to the new version
6. Make your changes to the API's new version (for example, add a new entity, or a field to an existing entity)
7. Run the following commands to update the database schema and proxies

    php artisan doctrine:generate:proxies
    php artisan doctrine:migrations:diff
    php artisan doctrine:migrations:migrate

8. Add routes to any new endpoints in the routes file

## Removing Versions
To remove a version follow this process:

9. Remove the version's folder inside the ```app/API``` folder
10. Remove the routes for the removed version from the routes file
11. Remove the ```auth``` entry in the ```config/api.php``` file
12. Check that the ```.env``` file's ```API_VERSION``` variable is not set to the removed version
