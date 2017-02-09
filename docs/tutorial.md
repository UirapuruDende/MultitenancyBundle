Tutorial
========

Let's create a simple application that has 2 connections to databases.
One is static, the other one is dynamic. Application holds all tenant
credentials in first database and uses them to switch second connection
according to domain that is used to access it. So if user
enters in browser `http://pluto.base_url.dev` the part
`pluto` will be used as a kind of identifier to find Tenant entity in
first database, then second connection will be setted up and connected
to the proper database.

Installation
------------

We start with [Symfony 2.8 installation](http://symfony.com/doc/current/setup.html).
Next step is to configure properly application after
[installing the Dende/MultitenantBundle](installation.md).

We need steps 1 and 2 done in the same way as in instruction:

1. install via composer

    ```bash
    composer require dende/multitenancybundle
    ```
    
2. enable bundle in AppKernel

    ```php
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            return array(
               new Dende\MultitenancyBundle\DendeMultitenancyBundle(),
            );
        }
    }
    ```
    
3. let's `CD` to app directory and `composer init` or just paste and edit `composer.json` file that suits your needs:

    ```json
    {
        "name": "uirapuru/multitenancy-tutorial",
        "type": "project",
        "authors": [
            {
                "name": "Grzegorz Kaszuba",
                "email": "uirapuruadg@gmail.com"
            }
        ],
        "minimum-stability": "dev",
        "require": {}
    }
    ```
    
4. `composer require dende/multitenancybundle` should result with:

    ```bash
    > composer require dende/multitenancybundle
    Using version dev-master for dende/multitenancybundle
    
    ./composer.json has been updated
    Loading composer repositories with package information
    Updating dependencies (including require-dev)
        ...
      - Installing dende/multitenancybundle (dev-master fca345e)
        Cloning fca345ed44e9cd987b7821e074d687310812534a from cache

    Writing lock file
    Generating autoload files
    ```
    
5. let's create a databases that we will going to use in this tutorial:

    ```mysql
   mysql -u root -p -e "CREATE DATABASE tutorial; CREATE DATABASE tutorial_spiderman; CREATE DATABASE tutorial_batman; CREATE DATABASE tutorial_superman;"
    ```
    
6. We edit `app/config/config.yml` file. We need to find this block:

    ```yaml
    # Doctrine Configuration
    doctrine:
        dbal:
            driver:   pdo_mysql
            host:     "%database_host%"
            port:     "%database_port%"
            dbname:   "%database_name%"
            user:     "%database_user%"
            password: "%database_password%"
            charset:  UTF8
            # if using pdo_sqlite as your database driver:
            #   1. add the path in parameters.yml
            #     e.g. database_path: "%kernel.root_dir%/data/data.db3"
            #   2. Uncomment database_path in parameters.yml.dist
            #   3. Uncomment next line:
            #path:     "%database_path%"
    ```
    
    and let's change it into something like:
    
    ```yaml
     doctrine:
         dbal:
           default_connection: default
           connections:
             default:
               driver:   pdo_mysql
               host:     localhost
               dbname:   tutorial
               user:     root
               password: ~
               charset:  UTF8
             dynamic:
               driver:   pdo_mysql
               host:     ~
               dbname:   ~
               user:     ~
               password: ~
               charset:  UTF8
               wrapper_class: Dende\MultitenancyBundle\Connection\Wrapper
    ```
    
7. Next step is to creating an Entity and map it to db. We can use existing `Tenant` class and just inherit from it.

    ```php
    <?php
    namespace AppBundle\Entity;
    
    use Dende\MultitenancyBundle\DTO\Tenant as BaseTenant;
    
    class Tenant extends BaseTenant
    {
        /** @var string */
        protected $id;
    
        /** @var string */
        protected $slug;
    
        /**
         * Tenant constructor.
         * @param $id
         * @param $slug
         * @param $username
         * @param $password
         * @param $dbname
         * @param $host
         */
        public function __construct($id, $slug, $username, $password, $dbname, $host, $path = null)
        {
            parent::__construct($username, $password, $dbname, $host, $path);
    
            $this->id = $id;
            $this->slug = $slug;
        }
    }
    ```

    mapping file `src/AppBundle/Resources/config/doctrine/Tenant.orm.yml`:
    
    ```yaml
    AppBundle\Entity\Tenant:
      type: entity
      table: tenants
      repositoryClass: AppBundle\Entity\TenantRepository
    
      id:
        id:
          type: guid
          generator: { strategy: UUID }
    
      fields:
        slug:
          type: string
          nullable: false
          length: 255
        dbname:
          type: string
          nullable: false
          length: 255
        username:
          type: string
          nullable: false
          length: 255
        password:
          type: string
          nullable: false
          length: 255
        host:
          type: string
          nullable: false
          length: 255
    ```
    
    and a repository class
    
    ```php
    <?php
    namespace AppBundle\Entity;
    
    use Doctrine\ORM\EntityRepository;
    
    class TenantRepository extends EntityRepository
    {
    
    }
    ```
    
    