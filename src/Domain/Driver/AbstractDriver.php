<?php namespace Tuxion\DoctrineRest\Domain\Driver;

abstract class AbstractDriver implements DriverInterface
{
  
  protected $resultFactory;
  
  public function setResultFactory($value){
    $this->resultFactory = $value;
  }
  
  abstract public function create($model, $data);
  abstract public function replace($model, $id, $data);
  abstract public function read($model, $id);
  abstract public function delete($model, $id);
  
}