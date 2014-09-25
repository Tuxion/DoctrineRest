<?php namespace Tuxion\DoctrineRest\Action;

use Tuxion\DoctrineRest\Driver\DriverInterface;
use Tuxion\DoctrineRest\Responder\ResponderInterface;
use Aura\Web\Request;

class ActionFactory
{
  
  protected $model;
  protected $driver;
  protected $request;
  protected $resource;
  protected $responder;
  
  public function getModel(){
    return $this->model;
  }
  
  public function setModel($value){
    $this->model = $value;
  }
  
  public function getResource(){
    return $this->resource;
  }
  
  public function setResource($value){
    $this->resource = $value;
  }
  
  public function __construct(Request $request, ResponderInterface $responder, DriverInterface $driver)
  {
    $this->driver = $driver;
    $this->request = $request;
    $this->responder = $responder;
  }
  
  public function __invoke($action)
  {
    return new Action(
      $this->request,
      $this->responder,
      $this->driver,
      $action,
      $this->model,
      $this->resource
    );
  }
  
}