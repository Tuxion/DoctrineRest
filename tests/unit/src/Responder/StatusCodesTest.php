<?php namespace Tuxion\DoctrineRest\Responder;

use Tuxion\DoctrineRest\Domain\Result\CreatedResult;
use Tuxion\DoctrineRest\Domain\Result\CustomResult;
use Tuxion\DoctrineRest\Domain\Result\DeletedResult;
use Tuxion\DoctrineRest\Domain\Result\ErrorResult;
use Tuxion\DoctrineRest\Domain\Result\FoundResult;
use Tuxion\DoctrineRest\Domain\Result\NotFoundResult;
use Tuxion\DoctrineRest\Domain\Result\ReplacedResult;
use Tuxion\DoctrineRest\Domain\Result\DummyResult;

class StatusCodesTest extends \PHPUnit_Framework_TestCase
{
  
  public function testConstructor()
  {
    return new StatusCodes();
  }
  
  /**
   * @depends testConstructor
   */
  public function testCreatedResult($instance)
  {
    $code = $instance->fromResult(new CreatedResult(array()));
    $this->assertEquals(201, $code);
  }
  
  /**
   * @depends testConstructor
   */
  public function testDeletedResult($instance)
  {
    $code = $instance->fromResult(new DeletedResult(array()));
    $this->assertEquals(200, $code);
  }
  
  /**
   * @depends testConstructor
   */
  public function testFoundResult($instance)
  {
    $code = $instance->fromResult(new FoundResult(array()));
    $this->assertEquals(200, $code);
  }
  
  /**
   * @depends testConstructor
   */
  public function testNotFoundResult($instance)
  {
    $code = $instance->fromResult(new NotFoundResult(array()));
    $this->assertEquals(404, $code);
  }
  
  /**
   * @depends testConstructor
   */
  public function testReplacedResult($instance)
  {
    $code = $instance->fromResult(new ReplacedResult(array()));
    $this->assertEquals(200, $code);
  }
  
  /**
   * @depends testConstructor
   */
  public function testErrorResult($instance)
  {
    $code = $instance->fromResult(new ErrorResult(array()));
    $this->assertEquals(500, $code);
  }
  
  /**
   * @depends testConstructor
   */
  public function testUnknownResult($instance)
  {
    $code = $instance->fromResult(new DummyResult(array()));
    $this->assertEquals(500, $code);
  }
  
  /**
   * @depends testConstructor
   */
  public function testCustomResult($instance)
  {
    $code = 418;
    $result = new CustomResult(array(), $code);
    $output = $instance->fromResult($result);
    $this->assertEquals($code, $output);
  }
  
}