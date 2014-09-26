<?php namespace Tuxion\DoctrineRest\Driver;

class DummyDriver implements DriverInterface
{
  
  public $history;
  public $readResponse;
  
  public function __construct()
  {
    
    $this->history = array();
    $this->readResponse = (object)array(
      'id' => 12345,
      'title' => 'TestValue'
    );
    
  }
  
  public function create($model, $data)
  {
    
    $this->history[] = array(
      'method' => 'create',
      'model' => $model,
      'data' => (array)$data
    );
    
    return $data;
    
  }
  
  public function replace($model, $id, $data)
  {
    
    $this->history[] = array(
      'method' => 'replace',
      'model' => $model,
      'id' => $id,
      'data' => (array)$data
    );
    
    return $data;
    
  }
  
  public function read($model, $id)
  {
    
    $this->history[] = array(
      'method' => 'read',
      'model' => $model,
      'id' => $id
    );
    
    return $this->readResponse;
    
  }
  
  public function delete($model, $id)
  {
    
    $this->history[] = array(
      'method' => 'delete',
      'model' => $model,
      'id' => $id
    );
    
    return null;
    
  }
  
}