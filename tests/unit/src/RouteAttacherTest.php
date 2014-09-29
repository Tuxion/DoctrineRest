<?php namespace Tuxion\DoctrineRest;

use Tuxion\DoctrineRest\RouteAttacher;
use Tuxion\DoctrineRest\Domain\Driver\DummyDriver;
use Tuxion\DoctrineRest\Action\Action;
use Tuxion\DoctrineRest\Action\ActionFactory;
use Tuxion\DoctrineRest\Responder\StatusCodes;
use Tuxion\DoctrineRest\Responder\DummyResponder;
use Aura\Web\WebFactory;
use Aura\Router\RouterFactory;

class RouteAttacherTest extends \PHPUnit_Framework_TestCase
{
  
  protected $routerFactory;
  protected $webFactory;
  
  protected function setUp()
  {
    parent::setUp();
    $this->webFactory = new WebFactory(array());
    $this->routerFactory = new RouterFactory();
  }
  
  protected function newRequest()
  {
    return $this->webFactory->newRequest();
  }
  
  protected function newResponse()
  {
    return $this->webFactory->newResponse();
  }
  
  protected function newResponder()
  {
    return new DummyResponder($this->newResponse(), new StatusCodes());
  }
  
  protected function newRouter()
  {
    return $this->routerFactory->newInstance();
  }
  
  protected function newFactory()
  {
    return new ActionFactory(
      $this->newRequest(),
      $this->newResponder(),
      new DummyDriver()
    );
  }
  
  public function testIsCallable()
  {
    
    //Class exists?
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\RouteAttacher'));
    
    //Create the route attacher.
    $instance = new RouteAttacher(
      $this->newFactory(),
      'TestModel',
      'test-resource'
    );
    
    //Is callable.
    $this->assertTrue(is_callable($instance));
    
  }
  
  public function testConstruct()
  {
    
    $args = array(
      'model' => 'TestModel',
      'resource' => 'test-resource',
      'factory' => $this->newFactory()
    );
    
    //Create the route attacher.
    $instance = new RouteAttacher($args['factory'], $args['model'], $args['resource']);
    
    //See if the properties are set correctly.
    $this->assertSame($args['model'], $instance->getModel());
    $this->assertSame($args['resource'], $instance->getResource());
    $this->assertSame($args['factory'], $instance->getActionFactory());
    
  }
  
  public function testAttach()
  {
    
    //Create the route attacher.
    $instance = new RouteAttacher(
      $this->newFactory(),
      'TestModel',
      'test-resource'
    );
    
    //Able to attach.
    $router = $this->newRouter();
    $router->attach('attached', '/attached', $instance);
    
    //Verify routes generated.
    $routes = $router->getRoutes();
    $this->assertRestRoutes('attached', '/attached', $routes);
    
  }
  
  public function testResource()
  {
    
    //Create the route attacher.
    $instance = new RouteAttacher(
      $this->newFactory(),
      'TestModel',
      'test-resource'
    );
    
    //Able to attach as callable resource.
    $router = $this->newRouter();
    $router->setResourceCallable($instance);
    $router->attachResource('resource', '/resource');
    
    //Verify routes generated.
    $routes = $router->getRoutes();
    $this->assertRestRoutes('resource', '/resource', $routes);
    
  }
  
  protected function assertRestRoutes($namePrefix, $pathPrefix, $actual)
  {
    
    $expect = array(
      'name' => "$namePrefix.create",
      'path' => "$pathPrefix",
      'method' => array('POST'),
      'tokens' => array('id' => '\d+')
    );
    $this->assertRoute($expect, $actual["$namePrefix.create"]);
    $this->assertInstanceOf(
      'Tuxion\DoctrineRest\Action\Action',
      $actual["$namePrefix.create"]->values['action']
    );
    
    $expect = array(
      'name' => "$namePrefix.read",
      'path' => "$pathPrefix/{id}",
      'method' => array('GET'),
      'tokens' => array('id' => '\d+')
    );
    $this->assertRoute($expect, $actual["$namePrefix.read"]);
    $this->assertInstanceOf(
      'Tuxion\DoctrineRest\Action\Action',
      $actual["$namePrefix.read"]->values['action']
    );
    
    $expect = array(
      'name' => "$namePrefix.replace",
      'path' => "$pathPrefix/{id}",
      'method' => array('PUT'),
      'tokens' => array('id' => '\d+')
    );
    $this->assertRoute($expect, $actual["$namePrefix.replace"]);
    $this->assertInstanceOf(
      'Tuxion\DoctrineRest\Action\Action',
      $actual["$namePrefix.replace"]->values['action']
    );
    
    $expect = array(
      'name' => "$namePrefix.delete",
      'path' => "$pathPrefix/{id}",
      'method' => array('DELETE'),
      'tokens' => array('id' => '\d+')
    );
    $this->assertRoute($expect, $actual["$namePrefix.delete"]);
    $this->assertInstanceOf(
      'Tuxion\DoctrineRest\Action\Action',
      $actual["$namePrefix.delete"]->values['action']
    );
    
  }
  
  protected function assertRoute($expect, $actual)
  {
    $this->assertInstanceOf('Aura\Router\Route', $actual);
    foreach ($expect as $key => $val) {
      $this->assertSame($val, $actual->$key);
    }
  }
  
}