<?php namespace Tuxion\DoctrineRest\Responder;

use Aura\Web\Response;
use Aura\Web\WebFactory;
use Tuxion\DoctrineRest\Domain\Result\DummyResult;

class RestResponderTest extends \PHPUnit_Framework_TestCase
{
  
  protected function newResponse()
  {
    $factory = new WebFactory(array());
    return $factory->newResponse();
  }
  
  protected function newResult()
  {
    return new DummyResult(array(
      'example' => 'value'
    ));
  }
  
  protected function newStatusCodes()
  {
    return new StatusCodes();
  }
  
  public function testClassExists()
  {
    
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Responder\RestResponder'));
    
  }
  
  public function testConstructor()
  {
    
    $response = $this->newResponse();
    $statusCodes = $this->newStatusCodes();
    $instance = new RestResponder($response, $statusCodes);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Responder\RestResponder', $instance);
    
  }
  
  public function testResponseProperty()
  {
    
    $response = $this->newResponse();
    $statusCodes = $this->newStatusCodes();
    $instance = new RestResponder($response, $statusCodes);
    $this->assertSame($response, $instance->getResponse());
    
  }
  
  public function testResultProperty()
  {
    
    $response = $this->newResponse();
    $statusCodes = $this->newStatusCodes();
    $instance = new RestResponder($response, $statusCodes);
    
    $result = $this->newResult();
    $instance->setResult($result);
    
    $this->assertSame($result, $instance->getResult());
    
  }
  
  public function testInvokeResult()
  {
    
    $response = $this->newResponse();
    $statusCodes = $this->newStatusCodes();
    $instance = new RestResponder($response, $statusCodes);
    
    $result = $this->newResult();
    $instance->setResult($result);
    
    $output = $instance();
    
    //Should return modified response.
    $this->assertSame($response, $output);
    
    //Check for the header modifications.
    $this->assertEquals(500, $output->status->getCode());
    $this->assertEquals('utf-8', $output->content->getCharset());
    $this->assertEquals('application/json', $output->content->getType());
    
    //And the body.
    $json = json_encode($result->getBody());
    $this->assertEquals($json, $output->content->get());
    
  }
  
  public function testNoResultInvoke()
  {
    
    $response = $this->newResponse();
    $statusCodes = $this->newStatusCodes();
    $instance = new RestResponder($response, $statusCodes);
    
    $this->setExpectedException(
      'Exception', 'No result was set.'
    );
    
    //Throws an exception for missing result object.
    $output = $instance();
    
  }
  
}