<?php namespace Tuxion\DoctrineRest\Mapper;

use \Exception;
use Tuxion\DoctrineRest\Domain\Result\DummyResult;
use Tuxion\DoctrineRest\Mapper\ResourceMapper;
use Tuxion\DoctrineRest\Domain\Driver\DummyDriver;
use Tuxion\DoctrineRest\Domain\Composite\CompositeCallFactory;
use Tuxion\DoctrineRest\Responder\StatusCodes;
use Tuxion\DoctrineRest\Responder\DummyResponder;
use Tuxion\DoctrineRest\Action\ActionFactory;
use Tuxion\DoctrineRest\Action\Environment;
use Aura\Router\RouterFactory;
use Aura\Web\WebFactory;

class ResourceFactoryTest extends \PHPUnit_Framework_TestCase
{
  
  protected $routerFactory;
  protected $webFactory;
  
  protected function setUp()
  {
    $this->webFactory = new WebFactory(array());
    $this->routerFactory = new RouterFactory();
  }
  
  public function testInvoke()
  {
    
    $actionFactory = $this->newActionFactory();
    $compositeCallFactory = $this->newCompositeCallFactory();
    $actions = array(
      'create' => true,
      'read' => false,
      'replace' => true,
      'delete' => false
    );
    $model = 'TestingModel';
    
    $factory = new ResourceFactory($this->newActionFactory(), $this->newCompositeCallFactory());
    $instance = $factory($actions, $model);
    
    $this->assertInstanceOf('Tuxion\DoctrineRest\Mapper\Resource', $instance);
    $this->assertEquals($actionFactory, $instance->getActionFactory());
    $this->assertEquals($compositeCallFactory, $instance->getCompositeCallFactory());
    $this->assertEquals($actions, $instance->getActions());
    $this->assertEquals($model, $instance->getModel());
    
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