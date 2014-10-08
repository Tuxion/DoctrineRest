<?php namespace Tuxion\DoctrineRest\Action;

use Aura\Web\Request;
use Aura\Web\WebFactory;
use Tuxion\DoctrineRest\Domain\Driver\DummyDriver;
use Tuxion\DoctrineRest\Domain\Composite\CompositeCall;
use Tuxion\DoctrineRest\Responder\StatusCodes;
use Tuxion\DoctrineRest\Responder\DummyResponder;

class ActionTest extends \PHPUnit_Framework_TestCase
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
  
  protected function forgeBody(Request $request, $body)
  {
    $this->forgeRawBody($request, json_encode($body), 'application/json');
  }
  
  protected function forgeRawBody(Request $request, $body, $typeValue)
  {
    
    //Alias the content class.
    $content = $request->content;
    
    //Insert the content body manually.
    $raw = new \ReflectionProperty(get_class($content), 'raw');
    $raw->setAccessible(true);
    $raw->setValue($content, $body);
    $raw->setAccessible(false);
    
    //Insert the content type manually.
    $type = new \ReflectionProperty(get_class($content), 'type');
    $type->setAccessible(true);
    $type->setValue($content, $typeValue);
    $type->setAccessible(false);
    
  }
  
  protected function newInstance(array &$params=array())
  {
    
    //Add any default values.
    $params = array_merge(
      array(
        'environment' => $this->newEnvironment(),
        'compositeCall' => $this->newCompositeCall(),
        'action' => 'read',
        'model' => 'TestModel'
      ),
      $params
    );
    
    //Create the action with these parameters.
    return new Action(
      $params['environment'],
      $params['compositeCall'],
      $params['action'],
      $params['model']
    );
    
  }
  
  public function testIsCallable()
  {
    
    //Class exists?
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Action\Action'));
    
    //Create an instance.
    $instance = $this->newInstance();
    
    //Is callable.
    $this->assertTrue(is_callable($instance));
    
  }
  
  public function testConstruct()
  {
    
    //Create an instance.
    $params = array();
    $instance = $this->newInstance($params);
    
    //Check all variables are present.
    $this->assertEquals($params['model'], $instance->getModel());
    $this->assertEquals($params['action'], $instance->getAction());
    $this->assertEquals($params['environment'], $instance->getEnvironment());
    
  }
  
  public function testUnknownMethod()
  {
    
    $params = array('action' => 'this-action-does-not-exist');
    
    //Define the exception we're expecting.
    $this->setExpectedException(
      'Exception', "Unknown action '{$params['action']}'"
    );
    
    //Create an instance.
    $instance = $this->newInstance($params);
    
  }
  
  public function testIncorrectContentType()
  {
    
    //Create an instance.
    $params = array('action' => 'create');
    $instance = $this->newInstance($params);
    
    //Set the request body.
    $body = array(
      'title' => 'Testing 123...'
    );
    $this->forgeRawBody($params['environment']->getRequest(), json_encode($body), 'application/not-json-at-all');
    
    //Should return an error result.
    $response = $instance();
    
    //Assert the return value.
    $this->assertSame($params['environment']->getResponder(), $response);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\ErrorResult', $response->getResult());
    
    //Define the exception we're expecting.
    $this->setExpectedException(
      'Exception', "Invalid Content-Type, must be 'application/json'"
    );
    throw $response->getResult()->getException();
    
  }
  
  public function testCreateAction()
  {
    
    //Create an instance.
    $params = array('action' => 'create');
    $instance = $this->newInstance($params);
    
    //Set the request body.
    $body = array(
      'title' => 'Testing 123...'
    );
    $this->forgeBody($params['environment']->getRequest(), $body);
    
    //Run the action.
    $response = $instance();
    
    //Assert the proper call has been made to the driver.
    $call = $params['environment']->getDriver()->history[0];
    $expect = array(
      'method' => $params['action'],
      'model' => $params['model'],
      'data' => $body
    );
    $this->assertSame($expect, $call);
    
    //Assert the return value.
    $this->assertSame($params['environment']->getResponder(), $response);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\ResultInterface', $response->getResult());
  }
  
  public function testReplaceAction()
  {
    
    //Create an instance.
    $params = array('action' => 'replace');
    $instance = $this->newInstance($params);
    
    //Set the id to request.
    $id = '54321';
    $params['environment']->getRequest()->params->set(array('id' => $id));
    
    //Set the request body.
    $body = array(
      'title' => 'Testing 123...'
    );
    $this->forgeBody($params['environment']->getRequest(), $body);
    
    //Run the action.
    $response = $instance();
    
    //Assert the proper call has been made to the driver.
    $call = $params['environment']->getDriver()->history[0];
    $expect = array(
      'method' => $params['action'],
      'model' => $params['model'],
      'id' => $id,
      'data' => $body
    );
    $this->assertSame($expect, $call);
    
    //Assert the return value.
    $this->assertSame($params['environment']->getResponder(), $response);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\ResultInterface', $response->getResult());
    
  }
  
  public function testReadAction()
  {
    
    //Create an instance.
    $params = array('action' => 'read');
    $instance = $this->newInstance($params);
    
    //Set the id to request.
    $id = '54321';
    $params['environment']->getRequest()->params->set(array('id' => $id));
    
    //Run the action.
    $response = $instance();
    
    //Assert the proper call has been made to the driver.
    $call = $params['environment']->getDriver()->history[0];
    $expect = array(
      'method' => $params['action'],
      'model' => $params['model'],
      'id' => $id
    );
    $this->assertSame($expect, $call);
    
    //Assert the return value.
    $expect = $params['environment']->getDriver()->readResponse;
    $this->assertSame($params['environment']->getResponder(), $response);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\ResultInterface', $response->getResult());
    
  }
  
  public function testDeleteAction()
  {
    
    //Create an instance.
    $params = array('action' => 'delete');
    $instance = $this->newInstance($params);
    
    //Set the id to request.
    $id = '54321';
    $params['environment']->getRequest()->params->set(array('id' => $id));
    
    //Run the action.
    $response = $instance();
    
    //Assert the proper call has been made to the driver.
    $call = $params['environment']->getDriver()->history[0];
    $expect = array(
      'method' => $params['action'],
      'model' => $params['model'],
      'id' => $id
    );
    $this->assertSame($expect, $call);
    
    //Assert the return value.
    $this->assertSame($params['environment']->getResponder(), $response);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\ResultInterface', $response->getResult());
    
  }
  
}