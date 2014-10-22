<?php namespace Tuxion\DoctrineRest\Domain\Driver;

use Tuxion\DoctrineRest\Domain\Result\ResultFactory;

class AbstractDriverTest extends \PHPUnit_Framework_TestCase
{
  
  protected function newResultFactory()
  {
    return new ResultFactory();
  }
  
  public function testResultFactorySetter()
  {
    
    $driver = new DummyDriver();
    
    $factory = $this->newResultFactory();
    
    $driver->setResultFactory($factory);
    $output = $driver->getResultFactory();
    
    $this->assertSame($factory, $output);
    
  }
  
}