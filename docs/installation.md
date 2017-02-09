Installation:
=============

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

3. configure

    ```yaml
    dende_multitenancy:
      patched_commands:
        - "doctrine:*"
    
      connections:
        connection_name:
          command_parameter_name:         tenant
          command_parameter_description:  Provide id for 'connection_name' connection tenant
    ```
    
4. add a wrapper class to connection in doctrine configuration that should be dynamic

    ```yaml
    doctrine:
        dbal:
          connections:
            default:
              host:     ~
              dbname:   ~
              user:     ~
              password: ~
              wrapper_class: Dende\MultitenancyBundle\Connection\Wrapper
    ```
    
5. create and register a `TenantProviderInterface` implementing service for dynamic connection:

    ```yaml
    services:
      provider.tenant_by_subdomain:
        class: Acme\Provider\TenantFromSubdomain
        tags:
          - {name: tenant_provider, connection_name: default}
    ```
    
Voila!