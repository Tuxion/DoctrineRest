# DoctrineRest - 0.1.0 Beta version

[![Build Status](https://travis-ci.org/Tuxion/DoctrineRest.svg?branch=master)](https://travis-ci.org/Tuxion/DoctrineRest)
**This library is currently not stable. Do not use for production.**

Links Doctrine 2 models to Aura.Router in a simple CRUD fashion.

<!-- MarkdownTOC -->

- [Installation](#installation)
- [Configuration](#configuration)
- [Defining resources](#defining-resources)
- [Contributing](#contributing)

<!-- /MarkdownTOC -->

## Installation

Add the following sections to your project's `composer.json`.

```json
{
  "repositories": [
    {"type":"vcs", "url":"ssh://git@github.com/Tuxion/DoctrineRest.git"}
  ],
  "require": {
    "tuxion/doctrine-rest": "*"
  }
}
```

You may want to look at the [`minimum-stability`](https://getcomposer.org/doc/04-schema.md#minimum-stability)
value as well if you want something other than stable versions.
Then run `composer update` or `composer install` depending on whether you've installed your project before.

## Configuration

First add the following lines to your `Common.php`.

```php
// Use the web-kernel services for DoctrineRest.
$di->values['Tuxion/DoctrineRest:router'] = $di->get('aura/web-kernel:router');
$di->values['Tuxion/DoctrineRest:request'] = $di->get('aura/web-kernel:request');
$di->values['Tuxion/DoctrineRest:response'] = $di->get('aura/web-kernel:response');
```

In your project there are two values that need to be defined manually to use the ResourceMapper.

### `Tuxion/DoctrineRest:entityManager`

This is the Doctrine2 EntityManager instance that is properly connected to the database.
We recommend you use the DI lazy value for this.

```php
//Set the entity manager.
$di->values['Tuxion/DoctrineRest:entityManager'] =
  EntityManager::create($connection, $config);
```

### `Tuxion/DoctrineRest:routePrefix`

The route prefix is to add a prefix to all your REST resources.
For example, to create routes that look like `/rest/<my-resource>`
the `routePrefix` value should be `/rest`.

There are two ways you can set this value.

**Using the DI's lazy value**

```php
//Set the route prefix for our REST interface.
$di->values['Tuxion/DoctrineRest:routePrefix'] = '/api/v1';

/* ... */

//Create a ResourceMapper instance to use.
$mapper = $di->newInstance('Tuxion\DoctrineRest\Mapper\ResourceMapper');
```

**Using an override during instantiation**

```php
//Create a ResourceMapper instance to use.
$mapper = $di->newInstance('Tuxion\DoctrineRest\Mapper\ResourceMapper', array(
  'routePrefix' => '/api/v1'
));
```

## Defining resources

Now that we have an instance of `ResourceMapper` we can define resources with it.
For this we use the `ResourceMapper::resource` method.

It takes the following arguments:
* `string|array $actions`: The actions to enable for this resource.
* `string $resource`: The name of the resource, which will be used for your routes.
* `string $model`: The Doctrine2 model name. Defined with the full namespace.

```php
//A simple resource.
$mapper->resource('*', 'example', 'Vendor\Project\ExampleModel');

//A resource that only allows read and create.
$mapper->resource('GET|POST', 'foo', 'Vendor\Project\FooModel');
```

The method returns an instance of `Resource`.
This class has the `before` and `after` methods that you can use to add extra hooks.

Both of these methods take the following arguments:
* `string|array $actions`: The actions to enable for this resource.
* `array $callbacks`: An array of [`is_callable`](http://php.net/manual/en/function.is-callable.php) values.

```php
//A resource with before and after handlers.
$mapper->resource('*', 'bar', 'Vendor\Project\BarModel')
  
  //The before handler, checking if you are logged in to create entries.
  ->before('create', array(
    array('MyHelperClass', 'isLoggedIn')
  ))
  
  //The before handler, checking if you are an admin allowed to modify entries.
  ->before('replace|delete', array(
    array('MyHelperClass', 'isAdmin')
  ))
  
  //An after handler, creating a log entry for deleted items.
  ->after('DELETE', array(
    array('MyHelperClass', 'logDeletion')
  ));
```

If you have an overlap in these callbacks, they will be called in **definition order**.

```php
$mapper->resource('*', 'baz', 'Vendor\Project\BazModel')
  
  ->before('*', array(
    function(){ /* I will go first! */ }
  ))
  
  ->before('*', array(
    function(){ /* I will go second. */ }
  ));
```

Refer to [`Resource::normalizeActions`](src/Mapper/Resource.php#L284) for more information on the `$actions` format.

## Contributing

If you would like to contribute, please use one of the following methods.

1. [Create an issue](https://github.com/Tuxion/DoctrineRest/issues) for questions, bug reports or feature requests.
1. For fixing a bug, create a pull-request based on the `master` branch.
1. For adding new features, create a pull-request based on the `develop` branch.
