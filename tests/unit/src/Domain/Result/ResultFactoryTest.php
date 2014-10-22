<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \ReflectionClass;

class ResultFactoryTest extends \PHPUnit_Framework_TestCase
{
  
  public function testGenerateDummy()
  {
    
    $factory = new ResultFactory();
    $result = $factory->dummy(null);
    
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\DummyResult', $result);
    $this->assertEquals(null, $result->getBody());
    
  }
  
}