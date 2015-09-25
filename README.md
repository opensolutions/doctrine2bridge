# Doctrine2Bridge

Adds the power of Doctrine2 to Laraval 4 (including authentication and SQL query logging support).

Laravel's Eloquent ORM is nice for lightweight use, however there's little out there that can beat Doctrine when you need a more full-featured ORM.

This is an integration of Doctrine 2.x to Laravel 4.x as a composer package. Doctrine's EntityManager instance is accessible through a facade named `D2EM` and the cache is directly available via `D2Cache`.

Metadata is currently obtained via the [XML driver](http://docs.doctrine-project.org/en/latest/reference/xml-mapping.html). It should be easy to add additional drivers to this.

Authentication support is also included via a `Auth/Doctrine2UserProvider` class. Documentation on integrating this with Laravel's own authentication system [can be found here](https://github.com/opensolutions/doctrine2bridge/wiki/Auth).

## Installation

Installation is the usual for Laravel packages. You can find a detailed worked version of [how to install and test in the wiki](https://github.com/opensolutions/doctrine2bridge/wiki/Install-from-Scratch).

Insert the following in the packages (`require`) section of your composer.json file and run an update (`composer update`):

    "opensolutions/doctrine2bridge": "2.4.*",

Generally speaking, we'll try and match our minor versions (2.4.x) with Doctrine's but you should always use the latest `x` version of this.

Note that your minimum stability must be `dev` for Doctrine migrations. If the above command complains, ensure you have the following set in your `composer.json` file:

    "minimum-stability": "dev"

Add the service providers to your Laravel application in `app/config/app.php`. In the `'providers'` array add:

    'Doctrine2Bridge\Doctrine2CacheBridgeServiceProvider',
    'Doctrine2Bridge\Doctrine2BridgeServiceProvider',

You'll need to public and edit the configuration file:

    ./artisan config:publish opensolutions/doctrine2bridge

This should get you a fresh copy of the configuration file in the directory `app`:

    config/packages/vendor/opensolutions/doctrine2bridge

Documentation on integrating this with Laravel's own authentication system [can be found here](https://github.com/opensolutions/doctrine2bridge/wiki/Auth).

## Usage

Two facades are provided - one for the Doctrine2 cache and the other for the entity manager. These can be used as follows:

    D2Cache::save( $key, $value );
    D2Cache::fetch( $key );
    
    D2EM::persist( $object );
    D2EM::flush();
    $users = D2EM::getRepository( 'Entities\User' )->findAll();

## More Detailed Usage

The configuration file by default expects to find XML schema definitions under `doctrine/schema`. Let's say for example we have a single schema file called `doctrine/schema/Entities.SampleEntity.dcm.xml` containing:

    <?xml version="1.0"?>
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping" xsi="http://www.w3.org/2001/XMLSchema-instance" schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
        <entity name="Entities\SampleEntity" repository-class="Repositories\Sample">
            <id name="id" type="integer">
                <generator strategy="AUTO"/>
            </id>
            <field name="name" type="string" length="255" nullable="true"/>
        </entity>
    </doctrine-mapping>

Assuming you've configured your database connection parameters in the config file and you're positioning in the base directory of your project, we can create the entities, proxies and repositories with:

    ./vendor/bin/doctrine2 orm:generate-entities app/models/
    ./vendor/bin/doctrine2 orm:generate-proxies
    ./vendor/bin/doctrine2 orm:generate-repositories app/models/

You can also (drop) and create the database with:

    ./vendor/bin/doctrine2 orm:schema-tool:drop --force
    ./vendor/bin/doctrine2 orm:schema-tool:create

Now you can add some data to the database:

    $se = new Entities\SampleEntity;
    $se->setName( rand( 0, 100 ) );
    D2EM::persist( $se );
    D2EM::flush();

And query it:

    echo count( D2EM::getRepository( 'Entities\SampleEntity' )->findAll() );

I use the excellent [ORM Designer](http://www.orm-designer.com/) to create and manage my XML schema files.

## Convenience Function for Repositories

If, like me, you spend a lot of time typing `D2EM::getRepository( 'Entities\XXX' )`, then add the following to the end of `bootstrap/start.php`:

    include $app['path.base'] . '/vendor/opensolutions/doctrine2bridge/src/bootstrap/d2r.php';

and then you can replace the above with: `D2R( 'XXX' )`. I use *Entities* as my namespace generally so this function is just as follows (which you can easily change to suit yourself):

    function D2R( $entity, $namespace = 'Entities' )
    {
        return D2EM::getRepository( $namespace . '\\' . $entity );
    }

## SQL Query Logging

This package includes an implementation of `Doctrine\DBAL\Logging\SQLLlogger` which times the queries and calls the Laravel [Log](http://laravel.com/docs/errors#logging) facade to log the query execution times and the SQL queries.

This logger can be enabled in the configuration file.

## License

Like the Laravel framework itself, this project is open-sourced under the [MIT license](http://opensource.org/licenses/MIT).

