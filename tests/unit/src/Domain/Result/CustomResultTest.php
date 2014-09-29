<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \ReflectionClass;

class CustomResultTest extends \PHPUnit_Framework_TestCase
{
  
  public function testClassExists()
  {
    
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Domain\Result\CustomResult'));
    
  }
  
  public function testExtendsAbstract()
  {
    
    $instance = new CustomResult(array());
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\AbstractResult', $instance);
    
  }
  
  public function testAcceptsBody()
  {
    
    $body = array(
      'sample',
      'body' => 'value'
    );
    
    $instance = new CustomResult($body);
    $this->assertSame($body, $instance->getBody());
    
  }
  
  public function testAcceptsCode()
  {
    
    $instance = new CustomResult(array());
    
    $code = 418;
    $instance->setCode($code);
    $this->assertSame($code, $instance->getCode());
    
  }
  
}