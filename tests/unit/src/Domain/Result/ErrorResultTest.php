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
    
    $instance = new ErrorResult(array(), new Exception());
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\AbstractResult', $instance);
    
  }
  
  public function testAcceptsBody()
  {
    
    $body = array(
      'sample',
      'body' => 'value'
    );
    
    $ex = new Exception();
    
    $instance = new ErrorResult($body, $ex);
    $this->assertSame($body, $instance->getBody());
    
  }
  
  public function testAcceptsException()
  {
    
    $body = array();
    $ex = new Exception("Error message");
    
    $instance = new ErrorResult($body, $ex);
    $this->assertSame($ex, $instance->getException());
    
  }
  
}