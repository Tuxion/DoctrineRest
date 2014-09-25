<?php namespace Tuxion\DoctrineRest\Action;

use Tuxion\DoctrineRest\Driver\DriverInterface;
use Tuxion\DoctrineRest\Responder\ResponderInterface;
use Aura\Web\Request;

class Action
{
  
  protected $model;
  protected $driver;
  protected $action;
  protected $request;
  protected $resource;
  protected $responder;
  
  public function getResponder(){
    return $this->responder;
  }
  
  public function getModel(){
    return $this->model;
  }
  
  public function getAction(){
    return $this->action;
  }
  
  public function getDriver(){
    return $this->driver;
  }
  
  public function getRequest(){
    return $this->request;
  }
  
  public function getResource(){
    return $this->resource;
  }
  
  public function __construct(Request $request, ResponderInterface $responder, DriverInterface $driver, $action, $model, $resource)
  {
    $this->model = $model;
    $this->driver = $driver;
    $this->action = $action;
    $this->request = $request;
    $this->resource = $resource;
    $this->responder = $responder;
  }
  
  #TODO
  public function __invoke()
  {
    
    switch($this->action){
      
      case 'create':
      case 'replace':
        $data = json_decode($this->request->content);
        return $this->driver->$action($this->model, $data);
      
      case 'read':
      case 'delete':
        return $this->driver->$action($this->model, $this->request->params->id);
      
      default:
        throw new Exception("Unknown action '".$this->action."'");
      
    }
    
  }
  
}