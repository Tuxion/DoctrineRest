<?php namespace Tuxion\DoctrineRest\Action;

use Tuxion\DoctrineRest\Domain\Driver\DummyDriver;
use Tuxion\DoctrineRest\Responder\StatusCodes;
use Tuxion\DoctrineRest\Responder\DummyResponder;
use Aura\Web\WebFactory;

class ActionFactoryTest extends \PHPUnit_Framework_TestCase
{
  
  protected $webFactory;
  
  public function setUp()
  {
    $this->webFactory = new WebFactory(array());
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
  
  public function testIsCallable()
  {
    
    //Class exists?
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Action\ActionFactory'));
    
    //Create an instance.
    $instance = new ActionFactory(
      $this->newRequest(),
      $this->newResponder(),
      $this->newDriver()
    );
    
    //Is callable.
    $this->assertTrue(is_callable($instance));
    
  }
  
  public function testProperties()
  {
    
    //Properties.
    $params = array(
      'model' => 'DummyModel'
    );
    
    //Constructor dependencies.
    $driver = $this->newDriver();
    $request = $this->newRequest();
    $responder = $this->newResponder();
    
    //Instantiate and set parameters.
    $instance = new ActionFactory($request, $responder, $driver);
    $instance->setModel($params['model']);
    
    //Must be populated with the constructor params correctly.
    $this->assertEquals($driver, $instance->getDriver());
    $this->assertEquals($request, $instance->getRequest());
    $this->assertEquals($responder, $instance->getResponder());
    
    //Test getters for set properties.
    $this->assertEquals($params['model'], $instance->getModel());
    
  }
  
  public function testReturnValue()
  {
    
    //Dependencies
    $params = array(
      'action' => 'create',
      'model' => 'DummyModel'
    );
    
    $driver = $this->newDriver();
    $request = $this->newRequest();
    $responder = $this->newResponder();
    
    //Instantiate and set parameters.
    $instance = new ActionFactory($request, $responder, $driver);
    $instance->setModel($params['model']);
    
    //Invoke the factory.
    $output = $instance($params['action']);
    
    //Must be an Action.
    $this->assertInstanceOf('Tuxion\DoctrineRest\Action\Action', $output);
    
    //Must be populated with the driver and request correctly.
    $this->assertEquals($driver, $output->getDriver());
    $this->assertEquals($request, $output->getRequest());
    $this->assertEquals($responder, $output->getResponder());
    
    //Must be populated with the params correctly.
    $this->assertEquals($params['model'], $output->getModel());
    $this->assertEquals($params['action'], $output->getAction());
    
  }
  
}