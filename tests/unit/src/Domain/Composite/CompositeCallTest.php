<?php namespace Tuxion\DoctrineRest\Domain\Composite;

use \Exception;
use Tuxion\DoctrineRest\Domain\Result\DummyResult;

class CompositeCallTest extends \PHPUnit_Framework_TestCase
{
  
  public function testConstructor()
  {
    return new CompositeCall();
  }
  
  public function testBeforesSetter()
  {
    
    $expect = array(
      function(){/* do nothing */},
      function(){/* do nothing */}
    );
    
    $instance = new CompositeCall();
    $instance->setBefores($expect);
    $this->assertSame($expect, $instance->getBefores());
    
  }
  
  public function testInvalidBeforesSetter()
  {
    
    //These are not callable.
    $input = array(
      null,
      array()
    );
    
    $this->setExpectedException(
      'Exception', 'All methods must be callable.'
    );
    
    $instance = new CompositeCall();
    $instance->setBefores($input);
    
  }
  
  public function testAftersSetter()
  {
    
    $expect = array(
      function(){/* do nothing */},
      function(){/* do nothing */}
    );
    
    $instance = new CompositeCall();
    $instance->setAfters($expect);
    $this->assertEquals($expect, $instance->getAfters());
    
  }
  
  public function testInvalidAftersSetter()
  {
    
    //These are not callable.
    $input = array(
      null,
      array()
    );
    
    $this->setExpectedException(
      'Exception', 'All methods must be callable.'
    );
    
    $instance = new CompositeCall();
    $instance->setAfters($input);
    
  }
  
  public function testMethodSetter()
  {
    
    $expect = function(){/* do nothing */};
    
    $instance = new CompositeCall();
    $instance->setMethod($expect);
    $this->assertSame($expect, $instance->getMethod());
    
  }
  
  public function testInvalidMethodSetter()
  {
    
    //These are not callable.
    $input = null;
    
    $this->setExpectedException(
      'Exception', 'Method must be callable.'
    );
    
    $instance = new CompositeCall();
    $instance->setMethod($input);
    
  }
  
  public function testNoMethodInvoke()
  {
    
    $this->setExpectedException(
      'Exception', 'A callable method must be set before invoking.'
    );
    
    $instance = new CompositeCall();
    $result = $instance();
    
  }
  
  public function testMethodOnlyChain()
  {
    
    $tracker = $this->newCallTracker();
    
    $method = $this->newDummyCall($tracker, 'method', new DummyResult(null));
    
    $instance = new CompositeCall();
    $instance->setMethod($method);
    $result = $instance();
    
    $expect = array(
      'method' => 1
    );
    
    $this->assertEquals($expect, $tracker->executed);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\DummyResult', $result);
    
  }
  
  public function testBeforeAndMethodChain()
  {
    
    $tracker = $this->newCallTracker();
    
    $befores = array(
      $this->newDummyCall($tracker, 'before 1'),
      $this->newDummyCall($tracker, 'before 2')
    );
    
    $method = $this->newDummyCall($tracker, 'method', new DummyResult(null));
    
    $instance = new CompositeCall();
    $instance->setMethod($method);
    $instance->setBefores($befores);
    $result = $instance();
    
    $expect = array(
      'before 1' => 1,
      'before 2' => 2,
      'method' => 3
    );
    
    $this->assertEquals($expect, $tracker->executed);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\DummyResult', $result);
    
  }
  
  public function testBeforeReturnAndMethodChain()
  {
    
    $tracker = $this->newCallTracker();
    
    $befores = array(
      $this->newDummyCall($tracker, 'before 1', new DummyResult(null)),
      $this->newDummyCall($tracker, 'before 2')
    );
    
    $method = $this->newDummyCall($tracker, 'method', new DummyResult(null));
    
    $instance = new CompositeCall();
    $instance->setMethod($method);
    $instance->setBefores($befores);
    $result = $instance();
    
    $expect = array(
      'before 1' => 1
    );
    
    $this->assertEquals($expect, $tracker->executed);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\DummyResult', $result);
    
  }
  
  public function testBeforeExceptionAndMethodChain()
  {
    
    $tracker = $this->newCallTracker();
    
    $befores = array(
      $this->newDummyCall($tracker, 'before 1'),
      $this->newDummyCall($tracker, 'before 2', null, function(){ throw new Exception("Testing 1, 2, 3..."); })
    );
    
    $method = $this->newDummyCall($tracker, 'method', new DummyResult(null));
    
    $instance = new CompositeCall();
    $instance->setMethod($method);
    $instance->setBefores($befores);
    $result = $instance();
    
    $expect = array(
      'before 1' => 1,
      'before 2' => 2
    );
    
    $this->assertEquals($expect, $tracker->executed);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\ErrorResult', $result);
    $this->assertEquals('Testing 1, 2, 3...', $result->getException()->getMessage());
    
  }
  
  public function testMethodAndAfterChain()
  {
    
    $tracker = $this->newCallTracker();
    
    $afters = array(
      $this->newDummyCall($tracker, 'after 1'),
      $this->newDummyCall($tracker, 'after 2')
    );
    
    $method = $this->newDummyCall($tracker, 'method', new DummyResult(null));
    
    $instance = new CompositeCall();
    $instance->setMethod($method);
    $instance->setAfters($afters);
    $result = $instance();
    
    $expect = array(
      'method' => 1,
      'after 1' => 2,
      'after 2' => 3
    );
    
    $this->assertEquals($expect, $tracker->executed);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\DummyResult', $result);
    
  }
  
  public function testMethodExceptionAndAfterChain()
  {
    
    $tracker = $this->newCallTracker();
    
    $afters = array(
      $this->newDummyCall($tracker, 'after 1'),
      $this->newDummyCall($tracker, 'after 2')
    );
    
    $method = $this->newDummyCall($tracker, 'method', null, function(){ throw new Exception('Testing 1, 2, 3...'); });
    
    $instance = new CompositeCall();
    $instance->setMethod($method);
    $instance->setAfters($afters);
    $result = $instance();
    
    $expect = array(
      'method' => 1
    );
    
    $this->assertEquals($expect, $tracker->executed);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\ErrorResult', $result);
    $this->assertEquals('Testing 1, 2, 3...', $result->getException()->getMessage());
    
  }
  
  public function testMethodAndAfterExceptionChain()
  {
    
    $tracker = $this->newCallTracker();
    
    $afters = array(
      $this->newDummyCall($tracker, 'after 1'),
      $this->newDummyCall($tracker, 'after 2', null, function(){ throw new Exception('Testing 1, 2, 3...'); })
    );
    
    $method = $this->newDummyCall($tracker, 'method', new DummyResult(null));
    
    $instance = new CompositeCall();
    $instance->setMethod($method);
    $instance->setAfters($afters);
    $result = $instance();
    
    $expect = array(
      'method' => 1,
      'after 1' => 2,
      'after 2' => 3
    );
    
    $this->assertEquals($expect, $tracker->executed);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\ErrorResult', $result);
    $this->assertEquals('Testing 1, 2, 3...', $result->getException()->getMessage());
    
  }
  
  public function testMethodAndAfterReturnChain()
  {
    
    $tracker = $this->newCallTracker();
    
    $afters = array(
      $this->newDummyCall($tracker, 'after 1', new DummyResult('456')),
      $this->newDummyCall($tracker, 'after 2')
    );
    
    $method = $this->newDummyCall($tracker, 'method', new DummyResult('123'));
    
    $instance = new CompositeCall();
    $instance->setMethod($method);
    $instance->setAfters($afters);
    $result = $instance();
    
    $expect = array(
      'method' => 1,
      'after 1' => 2,
      'after 2' => 3
    );
    
    $this->assertEquals($expect, $tracker->executed);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\DummyResult', $result);
    $this->assertEquals('456', $result->getBody());
    
  }
  
  public function testFullChain()
  {
    
    $tracker = $this->newCallTracker();
    
    $befores = array(
      $this->newDummyCall($tracker, 'before 1'),
      $this->newDummyCall($tracker, 'before 2')
    );
    
    $afters = array(
      $this->newDummyCall($tracker, 'after 1'),
      $this->newDummyCall($tracker, 'after 2')
    );
    
    $method = $this->newDummyCall($tracker, 'method', new DummyResult(null));
    
    $instance = new CompositeCall();
    $instance->setBefores($befores);
    $instance->setMethod($method);
    $instance->setAfters($afters);
    $result = $instance();
    
    $expect = array(
      'before 1' => 1,
      'before 2' => 2,
      'method' => 3,
      'after 1' => 4,
      'after 2' => 5
    );
    
    $this->assertEquals($expect, $tracker->executed);
    $this->assertInstanceOf('Tuxion\DoctrineRest\Domain\Result\DummyResult', $result);
    
  }
  
  protected function newDummyCall($tracker, $key, $returnValue=null, $extraCall=null)
  {
    return function()use($tracker, $key, $returnValue, $extraCall){
      $tracker->executed[$key] = $tracker->index;
      $tracker->index++;
      if($extraCall)
        $extraCall();
      if($returnValue)
        return $returnValue;
    };
  }
  
  protected function newCallTracker()
  {
    return (object)array(
      'index' => 1,
      'executed' => array()
    );
  }
  
}