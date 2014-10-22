<?php namespace Tuxion\DoctrineRest\Domain\Driver;

use Tuxion\DoctrineRest\Domain\Result\DummyResult;

class DummyDriver extends AbstractDriver
{
  
  public $history;
  public $readResponse;
  public $readAllResponse;
  
  public function getResultFactory(){
    return $this->resultFactory;
  }
  
  public function __construct()
  {
    
    $this->history = array();
    $this->readResponse = new DummyResult(array(
      'id' => 12345,
      'title' => 'TestValue'
    ));
    $this->readAllResponse = new DummyResult(array(array(
      'id' => 12345,
      'title' => 'TestValue'
    )));
    
  }
  
  public function create($model, $data)
  {
    
    $this->history[] = array(
      'method' => 'create',
      'model' => $model,
      'data' => (array)$data
    );
    
    return new DummyResult((array)$data);
    
  }
  
  public function replace($model, $id, $data)
  {
    
    $this->history[] = array(
      'method' => 'replace',
      'model' => $model,
      'id' => $id,
      'data' => (array)$data
    );
    
    return new DummyResult((array)$data);
    
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
  
  public function readAll($model, $options=array())
  {
    
    $this->history[] = array(
      'method' => 'readAll',
      'model' => $model,
      'options' => $options
    );
    
    return $this->readAllResponse;
    
  }
  
  public function delete($model, $id)
  {
    
    $this->history[] = array(
      'method' => 'delete',
      'model' => $model,
      'id' => $id
    );
    
    return new DummyResult(array('id'=>$id));
    
  }
  
}
