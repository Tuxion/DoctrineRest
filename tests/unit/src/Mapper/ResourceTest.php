<?php namespace Tuxion\DoctrineRest\Mapper;

use Tuxion\DoctrineRest\Mapper\Resource;
use Tuxion\DoctrineRest\Domain\Driver\DummyDriver;
use Tuxion\DoctrineRest\Domain\Composite\CompositeCallFactory;
use Tuxion\DoctrineRest\Action\Environment;
use Tuxion\DoctrineRest\Action\Action;
use Tuxion\DoctrineRest\Action\ActionFactory;
use Tuxion\DoctrineRest\Responder\StatusCodes;
use Tuxion\DoctrineRest\Responder\DummyResponder;
use Aura\Web\WebFactory;
use Aura\Router\RouterFactory;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
  
  protected $routerFactory;
  protected $webFactory;
  
  protected function setUp()
  {
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
  
  protected function newEnvironment()
  {
    return new Environment(
      $this->newRequest(),
      $this->newResponder(),
      new DummyDriver()
    );
  }
  
  protected function newActionFactory()
  {
    return new ActionFactory(
      $this->newEnvironment()
    );
  }
  
  protected function newCompositeCallFactory()
  {
    return new CompositeCallFactory();
  }
  
  public function testIsCallable()
  {
    
    //Class exists?
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Mapper\Resource'));
    
    //Create the route attacher.
    $instance = new Resource(
      $this->newActionFactory(),
      $this->newCompositeCallFactory(),
      '*',
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
      'actions' => array(
        'create' => true,
        'read' => false,
        'replace' => true,
        'delete' => false
      ),
      'actionFactory' => $this->newActionFactory(),
      'compositeCallFactory' => $this->newCompositeCallFactory()
    );
    
    //Create the route attacher.
    $instance = new Resource($args['actionFactory'], $args['compositeCallFactory'], $args['actions'], $args['model']);
    
    //See if the properties are set correctly.
    $this->assertSame($args['model'], $instance->getModel());
    $this->assertSame($args['actions'], $instance->getActions());
    $this->assertSame($args['actionFactory'], $instance->getActionFactory());
    $this->assertSame($args['compositeCallFactory'], $instance->getCompositeCallFactory());
    
    //Test the befores and afters have an empty array.
    $expect = array(
      'create' => array(),
      'read' => array(),
      'replace' => array(),
      'delete' => array()
    );
    $this->assertSame($expect, $instance->getBefores());
    $this->assertSame($expect, $instance->getAfters());
    
  }
  
  public function testAttach()
  {
    
    //Create the route attacher.
    $instance = new Resource(
      $this->newActionFactory(),
      $this->newCompositeCallFactory(),
      '*',
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
    $instance = new Resource(
      $this->newActionFactory(),
      $this->newCompositeCallFactory(),
      '*',
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
  
  
  public function testWildcard()
  {
    
    $name = 'test-resource';
    $resource = new Resource($this->newActionFactory(), $this->newCompositeCallFactory(), '*', $name, 'TestModel');
    
    $expect = array(
      'create' => true,
      'read' => true,
      'replace' => true,
      'delete' => true
    );
    
    $this->assertSame($expect, $resource->getActions());
    
  }
  
  public function testHTTPMethods()
  {
    
    $name = 'test-resource';
    $resource = new Resource($this->newActionFactory(), $this->newCompositeCallFactory(), 'GET|PUT', $name, 'TestModel');
    
    $expect = array(
      'create' => false,
      'read' => true,
      'replace' => true,
      'delete' => false
    );
    
    $this->assertSame($expect, $resource->getActions());
    
  }
  
  public function testActionArray()
  {
    
    $name = 'test-resource';
    $resource = new Resource($this->newActionFactory(), $this->newCompositeCallFactory(), array('create', 'delete'), $name, 'TestModel');
    
    $expect = array(
      'create' => true,
      'read' => false,
      'replace' => false,
      'delete' => true
    );
    
    $this->assertSame($expect, $resource->getActions());
    
  }
  
  public function testActionString()
  {
    
    $name = 'test-resource';
    $resource = new Resource($this->newActionFactory(), $this->newCompositeCallFactory(), 'create|read', $name, 'TestModel');
    
    $expect = array(
      'create' => true,
      'read' => true,
      'replace' => false,
      'delete' => false
    );
    
    $this->assertSame($expect, $resource->getActions());
    
  }
  
  public function testSemiNormalizedActions()
  {
    
    $name = 'test-resource';
    
    $input = array(
      'create' => true,
      'delete' => false
    );
    $resource = new Resource($this->newActionFactory(), $this->newCompositeCallFactory(), $input, $name, 'TestModel');
    
    $expect = array(
      'create' => true,
      'read' => false,
      'replace' => false,
      'delete' => false
    );
    
    $this->assertSame($expect, $resource->getActions());
    
  }
  
  public function testNormalizedActions()
  {
    
    $name = 'test-resource';
    
    $input = array(
      'create' => true,
      'delete' => false,
      'read' => true,
      'replace' => false
    );
    $resource = new Resource($this->newActionFactory(), $this->newCompositeCallFactory(), $input, $name, 'TestModel');
    
    $expect = array(
      'create' => true,
      'read' => true,
      'replace' => false,
      'delete' => false
    );
    
    $this->assertSame($expect, $resource->getActions());
    
  }
  
  public function testBefore()
  {
    
    $name = 'test-resource';
    $resource = new Resource($this->newActionFactory(), $this->newCompositeCallFactory(), '*', $name, 'TestModel');
    
    $method1 = function(){};
    $method2 = function(){};
    $method3 = function(){};
    $method4 = function(){};
    
    $resource
      ->before('GET|PUT', $method1)
      ->before('create', $method2)
      ->before('*', $method3)
      ->before(array('delete','GET'), $method4);
    
    $expect = array(
      'create' => array($method2, $method3),
      'read' => array($method1, $method3, $method4),
      'replace' => array($method1, $method3),
      'delete' => array($method3, $method4)
    );
    
    $this->assertSame($expect, $resource->getBefores());
    
  }
  
  public function testAfter()
  {
    
    $name = 'test-resource';
    $resource = new Resource($this->newActionFactory(), $this->newCompositeCallFactory(), '*', $name, 'TestModel');
    
    $method1 = function(){};
    $method2 = function(){};
    $method3 = function(){};
    $method4 = function(){};
    
    $resource
      ->after('GET|PUT', $method1)
      ->after('create', $method2)
      ->after('*', $method3)
      ->after(array('delete','GET'), $method4);
    
    $expect = array(
      'create' => array($method2, $method3),
      'read' => array($method1, $method3, $method4),
      'replace' => array($method1, $method3),
      'delete' => array($method3, $method4)
    );
    
    $this->assertSame($expect, $resource->getAfters());
    
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