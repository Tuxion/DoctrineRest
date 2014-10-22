<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \ReflectionClass;

class AbstractResultTest extends \PHPUnit_Framework_TestCase
{
  
  public function testClassExists()
  {
    
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Domain\Result\AbstractResult'));
    
  }
  
  public function testIsAbstract()
  {
    
    $class = new ReflectionClass('Tuxion\DoctrineRest\Domain\Result\AbstractResult');
    $this->assertTrue($class->isAbstract());
    
  }
  
  public function testAcceptsBody()
  {
    
    $body = array(
      'sample',
      'body' => 'value'
    );
    
    $instance = new DummyResult($body);
    $this->assertSame($body, $instance->getBody());
    
  }
  
}