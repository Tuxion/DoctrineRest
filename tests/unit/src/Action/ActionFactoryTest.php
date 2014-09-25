<?php namespace Tuxion\DoctrineRest\Action;

use Tuxion\DoctrineRest\Driver\DummyDriver;
use Tuxion\DoctrineRest\Responder\DummyResponder;
use Aura\Web\WebFactory;

class ActionFactoryTest extends \PHPUnit_Framework_TestCase
{
  
  protected function newDriver()
  {
    return new DummyDriver();
  }
  
  protected function newResponder()
  {
    return new DummyResponder();
  }
  
  protected function newRequest()
  {
    $factory = new WebFactory(array());
    return $factory->newRequest();
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
  
  public function testReturnValue()
  {
    
    //Dependencies
    $params = array(
      'action' => 'create',
      'model' => 'DummyModel',
      'resource' => 'dummy-resource'
    );
    
    $driver = $this->newDriver();
    $request = $this->newRequest();
    $responder = $this->newResponder();
    
    //Instantiate and set parameters.
    $instance = new ActionFactory($request, $responder, $driver);
    $instance->setModel($params['model']);
    $instance->setResource($params['resource']);
    
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
    $this->assertEquals($params['resource'], $output->getResource());
    
  }
  
}