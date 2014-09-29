<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \Exception;
use \ReflectionClass;

class ErrorResultTest extends \PHPUnit_Framework_TestCase
{
  
  public function testClassExists()
  {
    
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Domain\Result\ErrorResult'));
    
  }
  
  public function testExtendsAbstract()
  {
    
    $instance = new ErrorResult(array());
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\AbstractResult', $instance);
    
  }
  
  public function testAcceptsBody()
  {
    
    $body = array(
      'sample',
      'body' => 'value'
    );
    
    $instance = new ErrorResult($body);
    $output = $instance->getBody();
    
    $this->assertSame($body, $output['params']);
    
  }
  
  public function testAcceptsException()
  {
    
    $body = array();
    $ex = new Exception("Error message");
    
    $instance = new ErrorResult($body);
    $instance->setException($ex);
    
    $this->assertSame($ex, $instance->getException());
    
  }
  
  public function testBodyContents()
  {
    
    $body = array(
      'sample',
      'body' => 'value'
    );
    
    $instance = new ErrorResult($body);
    
    //Without exception.
    $output = $instance->getBody();
    $expect = array(
      'error' => 'UnknownError',
      'message' => 'An unknown server error occurred.',
      'params' => $body
    );
    $this->assertSame($expect, $output);
    
    //With exception.
    $ex = new Exception("This is a testing exception.");
    $instance->setException($ex);
    $output = $instance->getBody();
    $expect = array(
      'error' => 'Exception',
      'message' => 'This is a testing exception.',
      'params' => $body
    );
    $this->assertSame($expect, $output);
    
  }
  
}