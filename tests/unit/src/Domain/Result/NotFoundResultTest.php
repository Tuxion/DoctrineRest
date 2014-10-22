<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \ReflectionClass;

class NotFoundResultTest extends \PHPUnit_Framework_TestCase
{
  
  public function testClassExists()
  {
    
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Domain\Result\NotFoundResult'));
    
  }
  
  public function testExtendsAbstract()
  {
    
    $instance = new NotFoundResult(array());
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\AbstractResult', $instance);
    
  }
  
  public function testAcceptsBody()
  {
    
    $body = array(
      'sample',
      'body' => 'value'
    );
    
    $instance = new NotFoundResult($body);
    $this->assertSame($body, $instance->getBody());
    
  }
  
}