# Doctrine2Bridge

Adds the power of Doctrine2 to Laraval 4.

Laravel's Eloquent ORM is nice for lightweight use, however there's little out there that can beat Doctrine when you need a more full-featured ORM.

This is an integration of Doctrine 2.x to Laravel 4.x as a composer package. Doctrine's EntityManager instance is accessible through a facade named `D2EM` and the cache is directly available via `D2Cache`.

Metadata is currently obtained via the [XML driver](http://docs.doctrine-project.org/en/latest/reference/xml-mapping.html). It should be easy to add additional drivers to this.

## Installation

Installation is the usual for Laravel packages.

Insert the following in the packages section of your composer.json file and run an update:

    "opensolutions/doctrine2bridge": "2.4.*",

Add the service providers to your Laravel application in `app/config/app.php`. In the `'providers'` array add:

    'Doctrine2Bridge\Doctrine2CacheBridgeServiceProvider',
    'Doctrine2Bridge\Doctrine2BridgeServiceProvider',

You'll need to public and edit the configuration file:

    ./artisan config:publish opensolutions/doctrine2bridge

This should get you a fresh copy of the configuration file in the directory `app`:

    config/packages/vendor/opensolutions/doctrine2bridge

## Usage

## License

Like the Laravel framework itself, this project is open-sourced under the [MIT license](http://opensource.org/licenses/MIT).

