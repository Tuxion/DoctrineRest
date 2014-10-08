<?php namespace Tuxion\DoctrineRest\Action;

use \Exception;
use Tuxion\DoctrineRest\Domain\Composite\CompositeCallInterface;

class Action
{
  
  protected $model;
  protected $action;
  protected $environment;
  protected $compositeCall;
  protected $knownActions;
  
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
    CompositeCallInterface $compositeCall,
    $action,
    $model
  ){
    
    $this->model = $model;
    $this->action = $action;
    $this->environment = $environment;
    $this->compositeCall = $compositeCall;
    $this->knownActions = array('create', 'read', 'replace', 'delete');
    
    if(!in_array($action, $this->knownActions))
      throw new Exception("Unknown action '".$action."'");
    
    $this->compositeCall->setMethod(array($this, 'performAction'));
    
  }
  
  public function performAction()
  {
    
    $action = $this->action;
    switch($action){
      
      case 'create':
        $data = $this->getRequestContent();
        return $this->environment->getDriver()->$action($this->model, $data);
        
      case 'replace':
        $id = $this->environment->getRequest()->params['id'];
        $data = $this->getRequestContent();
        return $this->environment->getDriver()->$action($this->model, $id, $data);
        
      case 'read':
      case 'delete':
        $id = $this->environment->getRequest()->params['id'];
        return $this->environment->getDriver()->$action($this->model, $id);
        
      default:
        throw new Exception("Unknown action '".$action."'");
      
    }
    
  }
  
  public function __invoke()
  {
    
    $result = $this->compositeCall->__invoke();
    $responder = $this->environment->getResponder();
    $responder->setResult($result);
    return $responder;
    
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