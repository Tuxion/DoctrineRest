DoctrineRest
============

[![Build Status](https://travis-ci.org/Tuxion/DoctrineRest.svg?branch=master)](https://travis-ci.org/Tuxion/DoctrineRest)
**This library is currently not stable. Do not use for production.**

Links Doctrine models to Aura.Router in a simple CRUD fashion.

### TODO

* Actually use the CompositeCall features in the action.
* Consider a PHP 5.3 compatible way of exposing JSON body rather than `\JsonSerialize`.
* Implement custom exceptions
* Factory die een route attacher instance maakt.
  - neemt een resource name
  - neemt een model class name
  - neemt een verwijzing naar de actions

### Missing tests

* Integration tests for before and after calls.
* Test for empty bodies at action.
* Test for null body in deleted results.
* Explicit test for ResultFactory.
* Explicit test for Mapper\ResourceFactory.
* Explicit test for Action\Environment.

```php
<?php

$mapper = new Mapper\Mapper($router, '/api/v1'); # Nieuwe class

# Mapper\Resource = was de RouteAttacher
$mapper->resource('GET|POST', 'snarl', 'Foo\Bar\Nyerk\Snarl');
  
  # Geef aan de GET + POST actions een ->addBefore() call.
  ->before('*', array(
    array('Helpers', 'authenticate')
  ))
  
  # De action bewaart alle befores en afters, tot de __invoke.
  # Op dat moment maakt deze een nieuwe composite call aan.
  
  
  ->before('DELETE', array(
    array('Helpers', 'ensureThatItExists')
  ))
  
  ->before('POST|DELETE', array(
    array('Helpers', 'ensureOwnership')
  ))
  
  ->before('DELETE', array(
    array('Helpers', 'ensureThatItExistsAgain')
  ));
  
?>
```