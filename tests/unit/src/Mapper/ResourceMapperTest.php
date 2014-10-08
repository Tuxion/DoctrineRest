<?php namespace Tuxion\DoctrineRest\Mapper;

use Tuxion\DoctrineRest\Mapper\ResourceMapper;
use Tuxion\DoctrineRest\Domain\Driver\DummyDriver;
use Tuxion\DoctrineRest\Domain\Composite\CompositeCallFactory;
use Tuxion\DoctrineRest\Responder\StatusCodes;
use Tuxion\DoctrineRest\Responder\DummyResponder;
use Tuxion\DoctrineRest\Action\ActionFactory;
use Tuxion\DoctrineRest\Action\Environment;
use Aura\Router\RouterFactory;
use Aura\Web\WebFactory;

class ResourceMapperTest extends \PHPUnit_Framework_TestCase
{
  
  protected $routerFactory;
  protected $webFactory;
  protected $dummyEntity;
  
  protected function setUp()
  {
    $this->webFactory = new WebFactory(array());
    $this->routerFactory = new RouterFactory();
    $this->dummyEntity = 'Tuxion\DoctrineRest\Domain\Dummy\DummyEntity';
  }
  
  public function testConstructor()
  {
    
    $prefix = '/api/v1';
    $router = $this->newRouter();
    $factory = new ResourceFactory($this->newActionFactory(), $this->newCompositeCallFactory());
    
    $instance = new ResourceMapper($factory, $router, $prefix);
    
    $this->assertEquals($router, $instance->getRouter());
    $this->assertEquals($prefix, $instance->getRoutePrefix());
    $this->assertEquals($factory, $instance->getResourceFactory());
    
  }
  
  public function testResource()
  {
    
    $name = 'test-resource';
    $instance = $this->newInstance();
    
    $resource = $instance->resource('*', $name, $this->dummyEntity);
    
    $this->assertInstanceOf('Tuxion\DoctrineRest\Mapper\Resource', $resource);
    $this->assertEquals($this->dummyEntity, $resource->getModel());
    $this->assertInstanceOf('Tuxion\DoctrineRest\Action\ActionFactory', $resource->getActionFactory());
    
  }
  
  protected function newInstance()
  {
    $factory = new ResourceFactory($this->newActionFactory(), $this->newCompositeCallFactory());
    $router = $this->newRouter();
    $prefix = '/api/v1';
    return new ResourceMapper($factory, $router, $prefix);
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
  
  protected function newEnvironment()
  {
    return new Environment(
      $this->newRequest(),
      $this->newResponder(),
      $this->newDriver()
    );
  }
  
  protected function newDriver()
  {
    return new DummyDriver();
  }
  
  protected function newResponder()
  {
    return new DummyResponder($this->newResponse(), new StatusCodes());
  }
  
  protected function newRequest()
  {
    return $this->webFactory->newRequest();
  }
  
  protected function newResponse()
  {
    return $this->webFactory->newResponse();
  }
  
  protected function newRouter()
  {
    return $this->routerFactory->newInstance();
  }
  
}