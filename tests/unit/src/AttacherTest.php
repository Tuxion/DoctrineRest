<?php namespace Tuxion\DoctrineRest;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
  
  protected $attacher;
  
  protected function setUp()
  {
    parent::setUp();
    // $this->attacher = new Attacher();
  }
  
  public function testClassExists()
  {
    $this->assertTrue(class_exists('Tuxion\DoctrineRest\Attacher'));
  }
  
}