<?php namespace Tuxion\DoctrineRest\Domain\Composite;

use \Exception;
use Tuxion\DoctrineRest\Domain\Result\DummyResult;

class CompositeCallFactoryTest extends \PHPUnit_Framework_TestCase
{
  
  public function testInvoke()
  {
    
    $factory = new CompositeCallFactory();
    $instance = $factory();
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Composite\CompositeCallInterface', $instance);
    
  }
  
}