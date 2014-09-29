<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \ReflectionClass;

class ReplacedResultTest extends \PHPUnit_Framework_TestCase
{
  
  public function testClassExists()
  {
    
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Domain\Result\ReplacedResult'));
    
  }
  
  public function testExtendsAbstract()
  {
    
    $instance = new ReplacedResult(array());
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\AbstractResult', $instance);
    
  }
  
  public function testAcceptsBody()
  {
    
    $body = array(
      'sample',
      'body' => 'value'
    );
    
    $instance = new ReplacedResult($body);
    $this->assertSame($body, $instance->getBody());
    
  }
  
}