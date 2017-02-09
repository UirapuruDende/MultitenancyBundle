# DendeMultitenancyBundle

A [Symfony 2](http://symfony.com) bundle providing an easy to use database switching 'on-the-fly' infrastructure

## installation:

1. install via composer

    ```bash
    composer require dende/multitenancy-bundle
    ```
    
2. enable bundle in AppKernel

    ```php
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            return array(
                new Dende\MultitenancyBundle\DendeMultitenancyBundle(),
                ...
            );
        }
    }
    ```

3. configure

    ```yaml
    dende_multitenancy:
      patched_commands:
        - "doctrine:*"
    
      connections:
        connection_name:
          command_parameter_name:         tenant
          command_parameter_description:  Provide id for 'connection_name' connection tenant
          fixtures_dir: ~
    ```

4. FAQ

     * What is a 'Tenant'?
      
       Tenant is a structure/object that holds credentials to database and is used to switch connection to other database
       
     * How to obtain Tenant?  
     
       Create a `TenantProviderInterface` implementing service, register it with a tag and return a Tenant object created
       with data taken from wherever you want.
       
       ```yaml
       services:
         provider.subdomain:
           class: Acme\Provider\TenantFromSubdomain
           tags:
             - {name: tenant_provider, connection_name: first}
         
         provider.api:
           class: Acme\Provider\TenantFromApi
           tags:
             - {name: tenant_provider, connection_name: second}
       ```
       
     * How to update connection?
     
       Just update information in Tenant Provider service that corresponds with required connection, and use method
       `switchConnection` on TenantManager service:
       
       ```php
       $container->get('provider.your_provider')->yourMethod($yourData); // this updates whatever you want in provider
       $container->get('dende_multitenanacy.tenant_manager')->switchConnection($connectionName); // connection_name from service definition's tag
       ```
       
       you can also directly pass tenant id (subdomain, username, something unique that TenantProvider will use to find proper Tenant and will pass it to connection):
       
       ```php
       $container->get('dende_multitenanacy.tenant_manager')->switchConnection('my_connection', 'spiderman'); // connection_name from service definition's tag
       ```