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
    Using version dev-master for dende/multitenancybundle
    ./composer.json has been updated
    Loading composer repositories with package information
    Updating dependencies (including require-dev)
    ```