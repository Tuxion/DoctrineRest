<?php namespace Tuxion\DoctrineRest\Action;

use Tuxion\DoctrineRest\Domain\Driver\DriverInterface;
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
  
  public function __construct(
    Request $request,
    ResponderInterface $responder,
    DriverInterface $driver,
    $action,
    $model,
    $resource)
  {
    $this->model = $model;
    $this->driver = $driver;
    $this->action = $action;
    $this->request = $request;
    $this->resource = $resource;
    $this->responder = $responder;
  }
  
  public function __invoke()
  {
    
    $action = $this->action;
    switch($action){
      
      case 'create':
        $data = $this->getRequestContent();
        $result = $this->driver->$action($this->model, $data);
        $this->responder->setResult($result);
        return $this->responder;
        
      case 'replace':
        $data = $this->getRequestContent();
        $result = $this->driver->$action($this->model, $this->request->params->id, $data);
        $this->responder->setResult($result);
        return $this->responder;
      
      case 'read':
      case 'delete':
        $result = $this->driver->$action($this->model, $this->request->params->id);
        $this->responder->setResult($result);
        return $this->responder;
      
      default:
        throw new \Exception("Unknown action '".$action."'");
      
    }
    
  }
  
  protected function getRequestContent()
  {
    
    $content = $this->request->content;
    
    if($content->getType() !== 'application/json'){
      throw new \Exception("Invalid Content-Type, must be 'application/json'.");
    }
    
    return $content->get();
    
  }
  
}