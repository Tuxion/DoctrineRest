<?php namespace Tuxion\DoctrineRest\Action;

use \ReflectionProperty;
use Aura\Web\WebFactory;
use Tuxion\DoctrineRest\Domain\Driver\DummyDriver;
use Tuxion\DoctrineRest\Domain\Composite\CompositeCall;
use Tuxion\DoctrineRest\Responder\StatusCodes;
use Tuxion\DoctrineRest\Responder\DummyResponder;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
  
  protected $webFactory;
  
  public function setUp()
  {
    $this->webFactory = new WebFactory(array());
  }
  
  public function testConstructor()
  {
    
    $instance = new Environment(
      $this->newRequest(),
      $this->newResponder(),
      $this->newDriver()
    );
    
    $this->assertInstanceOf('Tuxion\DoctrineRest\Action\Environment', $instance);
    
  }
  
  public function testProperties()
  {
    
    $request = $this->newRequest();
    $responder = $this->newResponder();
    $driver = $this->newDriver();
    
    $instance = new Environment($request, $responder, $driver);
    
    $this->assertEquals($request, $instance->getRequest());
    $this->assertEquals($responder, $instance->getResponder());
    $this->assertEquals($driver, $instance->getDriver());
    
  }
  
  protected function newDriver()
  {
    return new DummyDriver();
  }
  
  protected function newResponder()
  {
    return new DummyResponder($this->newResponse(), new StatusCodes());
  }
  
  protected function newRequest()
  {
    return $this->webFactory->newRequest();
  }
  
  protected function newResponse()
  {
    return $this->webFactory->newResponse();
  }
  
}