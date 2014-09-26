<?php namespace Tuxion\DoctrineRest\Driver;

interface DriverInterface
{
  
  public function create($model, $data);
  public function replace($model, $id, $data);
  public function read($model, $id);
  public function delete($model, $id);
  
}