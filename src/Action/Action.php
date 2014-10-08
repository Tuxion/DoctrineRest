<?php namespace Tuxion\DoctrineRest\Action;

use \Exception;

class Action
{
  
  protected $model;
  protected $action;
  protected $environment;
  
  public function getEnvironment(){
    return $this->environment;
  }
  
  public function getModel(){
    return $this->model;
  }
  
  public function getAction(){
    return $this->action;
  }
  
  public function __construct(
    Environment $environment,
    $action,
    $model)
  {
    $this->model = $model;
    $this->action = $action;
    $this->environment = $environment;
  }
  
  public function __invoke()
  {
    
    $action = $this->action;
    switch($action){
      
      case 'create':
        $data = $this->getRequestContent();
        $result = $this->environment->getDriver()->$action($this->model, $data);
        $responder = $this->environment->getResponder();
        $responder->setResult($result);
        return $responder;
        
      case 'replace':
        $data = $this->getRequestContent();
        $result = $this->environment->getDriver()->$action($this->model, $this->environment->getRequest()->params['id'], $data);
        $responder = $this->environment->getResponder();
        $responder->setResult($result);
        return $responder;
      
      case 'read':
      case 'delete':
        $result = $this->environment->getDriver()->$action($this->model, $this->environment->getRequest()->params['id']);
        $responder = $this->environment->getResponder();
        $responder->setResult($result);
        return $responder;
      
      default:
        throw new Exception("Unknown action '".$action."'");
      
    }
    
  }
  
  protected function getRequestContent()
  {
    
    $content = $this->environment->getRequest()->content;
    
    if($content->getType() !== 'application/json'){
      throw new Exception("Invalid Content-Type, must be 'application/json'.");
    }
    
    $content = json_decode($content->getRaw(), true);
    
    if(!is_array($content))
      throw new Exception("Empty body while a JSON object was expected.");
    
    return $content;
    
  }
  
}