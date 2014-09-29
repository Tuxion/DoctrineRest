<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \ReflectionClass;

class FoundResultTest extends \PHPUnit_Framework_TestCase
{
  
  public function testClassExists()
  {
    
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Domain\Result\FoundResult'));
    
  }
  
  public function testExtendsAbstract()
  {
    
    $instance = new FoundResult(array());
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\AbstractResult', $instance);
    
  }
  
  public function testAcceptsBody()
  {
    
    $body = array(
      'sample',
      'body' => 'value'
    );
    
    $instance = new FoundResult($body);
    $this->assertSame($body, $instance->getBody());
    
  }
  
}