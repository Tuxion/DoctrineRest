<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \ReflectionClass;

class DeletedResultTest extends \PHPUnit_Framework_TestCase
{
  
  public function testClassExists()
  {
    
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Domain\Result\DeletedResult'));
    
  }
  
  public function testExtendsAbstract()
  {
    
    $instance = new DeletedResult(array());
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\AbstractResult', $instance);
    
  }
  
  public function testAcceptsBody()
  {
    
    $body = array(
      'sample',
      'body' => 'value'
    );
    
    $instance = new DeletedResult($body);
    $this->assertSame($body, $instance->getBody());
    
  }
  
}