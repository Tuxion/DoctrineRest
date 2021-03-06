<?php namespace Tuxion\DoctrineRest\Action;

use \ReflectionProperty;
use Aura\Web\WebFactory;
use Tuxion\DoctrineRest\Domain\Driver\DummyDriver;
use Tuxion\DoctrineRest\Domain\Composite\CompositeCall;
use Tuxion\DoctrineRest\Responder\StatusCodes;
use Tuxion\DoctrineRest\Responder\DummyResponder;

class ActionFactoryTest extends \PHPUnit_Framework_TestCase
{
  
  protected $webFactory;
  
  public function setUp()
  {
    $this->webFactory = new WebFactory(array());
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
  
  protected function newCompositeCall()
  {
    return new CompositeCall();
  }
  
  public function testIsCallable()
  {
    
    //Class exists?
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Action\ActionFactory'));
    
    //Create an instance.
    $instance = new ActionFactory($this->newEnvironment());
    
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
    $environment = $this->newEnvironment();
    
    //Instantiate and set parameters.
    $instance = new ActionFactory($environment);
    $instance->setModel($params['model']);
    
    //Must be populated with the constructor params correctly.
    $this->assertEquals($environment, $instance->getEnvironment());
    
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
    
    $environment = $this->newEnvironment();
    $compositeCall = $this->newCompositeCall();
    
    //Instantiate and set parameters.
    $instance = new ActionFactory($environment);
    $instance->setModel($params['model']);
    
    //Invoke the factory.
    $output = $instance(
      $compositeCall,
      $params['action']
    );
    
    //Must be an Action.
    $this->assertInstanceOf('Tuxion\DoctrineRest\Action\Action', $output);
    
    //Must be populated with the environment correctly.
    $this->assertEquals($environment, $output->getEnvironment());
    
    //Use reflection to find out the same for the protected composite call.
    $raw = new ReflectionProperty($output, 'compositeCall');
    $raw->setAccessible(true);
    $this->assertEquals($compositeCall, $raw->getValue($output));
    $raw->setAccessible(false);
    
    //Must be populated with the params correctly.
    $this->assertEquals($params['model'], $output->getModel());
    $this->assertEquals($params['action'], $output->getAction());
    
  }
  
}