<?php namespace Tuxion\DoctrineRest\Domain\Driver;

use Tuxion\DoctrineRest\Domain\Result\ResultFactory;

/**
 * A driver that can handle the domain actions required for standard CRUD operations.
 */
abstract class AbstractDriver implements DriverInterface
{
  
  /**
   * A results factory to allow easier creation of result objects.
   * @var ResultFactory
   */
  protected $resultFactory;
  
  /**
   * Sets a results factory to allow easier creation of result objects.
   * @param ResultFactory $value
   */
  public function setResultFactory(ResultFactory $value){
    $this->resultFactory = $value;
  }
  
  //See DriverInterface for details of the methods below.
  abstract public function create($model, $data);
  abstract public function replace($model, $id, $data);
  abstract public function read($model, $id=null);
  abstract public function delete($model, $id);
  
}