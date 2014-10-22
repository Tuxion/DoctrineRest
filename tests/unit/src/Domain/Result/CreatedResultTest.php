<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \ReflectionClass;

class CreatedResultTest extends \PHPUnit_Framework_TestCase
{
  
  public function testClassExists()
  {
    
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Domain\Result\CreatedResult'));
    
  }
  
  public function testExtendsAbstract()
  {
    
    $instance = new CreatedResult(array());
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\AbstractResult', $instance);
    
  }
  
  public function testAcceptsBody()
  {
    
    $body = array(
      'sample',
      'body' => 'value'
    );
    
    $instance = new CreatedResult($body);
    $this->assertSame($body, $instance->getBody());
    
  }
  
}