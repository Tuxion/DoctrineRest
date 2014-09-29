<?php namespace Tuxion\DoctrineRest\Action;

use Aura\Web\Request;
use Aura\Web\WebFactory;
use Tuxion\DoctrineRest\Domain\Driver\DummyDriver;
use Tuxion\DoctrineRest\Responder\StatusCodes;
use Tuxion\DoctrineRest\Responder\DummyResponder;

class ActionTest extends \PHPUnit_Framework_TestCase
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
        'request' => $this->newRequest(),
        'responder' => $this->newResponder(),
        'driver' => $this->newDriver(),
        'action' => 'read',
        'model' => 'TestModel',
        'resource' => 'test-resource',
      ),
      $params
    );
    
    //Create the action with these parameters.
    return new Action(
      $params['request'],
      $params['responder'],
      $params['driver'],
      $params['action'],
      $params['model'],
      $params['resource']
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
    $this->assertEquals($params['driver'], $instance->getDriver());
    $this->assertEquals($params['action'], $instance->getAction());
    $this->assertEquals($params['request'], $instance->getRequest());
    $this->assertEquals($params['resource'], $instance->getResource());
    $this->assertEquals($params['responder'], $instance->getResponder());
    
  }
  
  public function testUnknownMethod()
  {
    
    //Create an instance.
    $params = array('action' => 'this-action-does-not-exist');
    $instance = $this->newInstance($params);
    
    //Define the exception we're expecting.
    $this->setExpectedException(
      'Exception', "Unknown action '{$params['action']}'"
    );
    
    //Trigger the exception.
    $instance();
    
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
    $this->forgeRawBody($params['request'], json_encode($body), 'application/not-json-at-all');
    
    //Define the exception we're expecting.
    $this->setExpectedException(
      'Exception', "Invalid Content-Type, must be 'application/json'"
    );
    
    //Trigger the exception.
    $instance();
    
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
    $this->forgeBody($params['request'], $body);
    
    //Run the action.
    $response = $instance();
    
    //Assert the proper call has been made to the driver.
    $call = $params['driver']->history[0];
    $expect = array(
      'method' => $params['action'],
      'model' => $params['model'],
      'data' => $body
    );
    $this->assertSame($expect, $call);
    
    //Assert the return value.
    $this->assertSame($params['responder'], $response);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\ResultInterface', $response->getResult());
    
  }
  
  public function testReplaceAction()
  {
    
    //Create an instance.
    $params = array('action' => 'replace');
    $instance = $this->newInstance($params);
    
    //Set the id to request.
    $id = '54321';
    $params['request']->params->id = $id;
    
    //Set the request body.
    $body = array(
      'title' => 'Testing 123...'
    );
    $this->forgeBody($params['request'], $body);
    
    //Run the action.
    $response = $instance();
    
    //Assert the proper call has been made to the driver.
    $call = $params['driver']->history[0];
    $expect = array(
      'method' => $params['action'],
      'model' => $params['model'],
      'id' => $id,
      'data' => $body
    );
    $this->assertSame($expect, $call);
    
    //Assert the return value.
    $this->assertSame($params['responder'], $response);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\ResultInterface', $response->getResult());
    
  }
  
  public function testReadAction()
  {
    
    //Create an instance.
    $params = array('action' => 'read');
    $instance = $this->newInstance($params);
    
    //Set the id to request.
    $id = '54321';
    $params['request']->params->id = $id;
    
    //Run the action.
    $response = $instance();
    
    //Assert the proper call has been made to the driver.
    $call = $params['driver']->history[0];
    $expect = array(
      'method' => $params['action'],
      'model' => $params['model'],
      'id' => $id
    );
    $this->assertSame($expect, $call);
    
    //Assert the return value.
    $expect = $params['driver']->readResponse;
    $this->assertSame($params['responder'], $response);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\ResultInterface', $response->getResult());
    
  }
  
  public function testDeleteAction()
  {
    
    //Create an instance.
    $params = array('action' => 'delete');
    $instance = $this->newInstance($params);
    
    //Set the id to request.
    $id = '54321';
    $params['request']->params->id = $id;
    
    //Run the action.
    $response = $instance();
    
    //Assert the proper call has been made to the driver.
    $call = $params['driver']->history[0];
    $expect = array(
      'method' => $params['action'],
      'model' => $params['model'],
      'id' => $id
    );
    $this->assertSame($expect, $call);
    
    //Assert the return value.
    $this->assertSame($params['responder'], $response);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\ResultInterface', $response->getResult());
    
  }
  
}