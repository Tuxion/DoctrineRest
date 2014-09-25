<?php namespace Tuxion\DoctrineRest;

use Tuxion\DoctrineRest\RouteAttacher;
use Tuxion\DoctrineRest\Driver\DummyDriver;
use Tuxion\DoctrineRest\Action\ActionFactory;
use Tuxion\DoctrineRest\Responder\DummyResponder;
use Aura\Web\WebFactory;
use Aura\Router\RouterFactory;

class RouteAttacherTest extends \PHPUnit_Framework_TestCase
{
  
  protected $factory;
  
  protected function setUp()
  {
    parent::setUp();
    $this->factory = new RouterFactory();
  }
  
  protected function newRequest()
  {
    $factory = new WebFactory(array());
    return $factory->newRequest();
  }
  
  protected function newResponder()
  {
    return new DummyResponder();
  }
  
  protected function newRouter()
  {
    return $this->factory->newInstance();
  }
  
  protected function newFactory()
  {
    return new ActionFactory(
      $this->newRequest(),
      $this->newResponder(),
      new DummyDriver()
    );
  }
  
  protected function assertIsRoute($actual)
  {
    $this->assertInstanceOf('Aura\Router\Route', $actual);
  }
  
  protected function assertRoute($expect, $actual)
  {
    $this->assertIsRoute($actual);
    foreach ($expect as $key => $val) {
      $this->assertSame($val, $actual->$key);
    }
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
    
    $expect = array(
      'name' => "$namePrefix.read",
      'path' => "$pathPrefix/{id}",
      'method' => array('GET'),
      'tokens' => array('id' => '\d+')
    );
    $this->assertRoute($expect, $actual["$namePrefix.read"]);
    
    $expect = array(
      'name' => "$namePrefix.replace",
      'path' => "$pathPrefix/{id}",
      'method' => array('PUT'),
      'tokens' => array('id' => '\d+')
    );
    $this->assertRoute($expect, $actual["$namePrefix.replace"]);
    
    $expect = array(
      'name' => "$namePrefix.delete",
      'path' => "$pathPrefix/{id}",
      'method' => array('DELETE'),
      'tokens' => array('id' => '\d+')
    );
    $this->assertRoute($expect, $actual["$namePrefix.delete"]);
    
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
  
}