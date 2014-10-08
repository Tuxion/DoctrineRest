<?php namespace Tuxion\DoctrineRest\Mapper;

use \Exception;
use Tuxion\DoctrineRest\Action\ActionFactory;
use Tuxion\DoctrineRest\Domain\Composite\CompositeCallFactory;

class Resource
{
  
  protected $model;
  protected $actionFactory;
  protected $actions;
  protected $befores;
  protected $afters;
  protected $compositeCallFactory;
  
  public function getCompositeCallFactory(){
    return $this->compositeCallFactory;
  }
  
  public function getAfters(){
    return $this->afters;
  }
  
  public function getBefores(){
    return $this->befores;
  }
  
  public function getActions(){
    return $this->actions;
  }
  
  public function getModel(){
    return $this->model;
  }
  
  public function getActionFactory(){
    return $this->actionFactory;
  }
  
  public function __construct(ActionFactory $actionFactory, CompositeCallFactory $compositeCallFactory, $actions, $model)
  {
    
    if(!(is_string($model) && strlen($model) > 0))
      throw new Exception("Model must be a string (class name).");
    
    $this->model = $model;
    $this->actionFactory = $actionFactory;
    $this->compositeCallFactory = $compositeCallFactory;
    
    $this->actions = $this->normalizeActions($actions);
    
    $this->befores = array();
    $this->afters = array();
    foreach($this->actions as $action => $value){
      $this->befores[$action] = array();
      $this->afters[$action] = array();
    }
    
  }
  
  public function before($actions, $callback)
  {
    
    $actions = $this->normalizeActions($actions);
    
    foreach($actions as $action => $value)
    {
      
      if($value !== true)
        continue;
      
      $this->befores[$action][] = $callback;
      
    }
    
  }
  
  public function after($actions, $callback)
  {
    
    $actions = $this->normalizeActions($actions);
    
    foreach($actions as $action => $value)
    {
      
      if($value !== true)
        continue;
      
      $this->afters[$action][] = $callback;
      
    }
    
  }
  
  public function __invoke($router)
  {
    
    //What does an ID look like?
    $router->setTokens(array(
      'id' => '\d+'
    ));
    
    //Set the model for when we create our action objects.
    $this->actionFactory->setModel($this->model);
    
    //Add routes.
    $action = 'create';
    if($this->hasAction($action)){
      $router->addPost($action, '')->addValues(array('action'=>$this->createAction($action)));
    }
    
    $action = 'read';
    if($this->hasAction($action)){
      $router->addGet($action, '/{id}')->addValues(array('action'=>$this->createAction($action)));
    }
    
    $action = 'replace';
    if($this->hasAction($action)){
      $router->addPut($action, '/{id}')->addValues(array('action'=>$this->createAction($action)));
    }
    
    $action = 'delete';
    if($this->hasAction($action)){
      $router->addDelete($action, '/{id}')->addValues(array('action'=>$this->createAction($action)));
    }
    
  }
  
  protected function createAction($action)
  {
    
    return $this->actionFactory->__invoke(
      $this->createCompositeCall($action),
      $action
    );
  }
  
  protected function createCompositeCall($action)
  {
    $call = $this->compositeCallFactory->__invoke();
    $call->setBefores($this->befores[$action]);
    $call->setAfters($this->afters[$action]);
    return $call;
  }
  
  protected function normalizeActions($actions)
  {
    
    $output = array(
      'create' => false,
      'read' => false,
      'replace' => false,
      'delete' => false
    );
    
    //Parse any string inputs.
    if(is_string($actions)){
      
      //Special case: all actions.
      if($actions === '*'){
        $actions = array_keys($output);
      }
      
      //Other string values are split by pipe.
      else{
        $actions = explode('|', $actions);
      }
      
    }
    
    //Iterate the actions.
    foreach($actions as $key => $value){
      
      //Format one: ['read' => true, 'create' => true]
      if(is_string($key) && $value === true){
        $action = trim(strtolower($key));
      }
      
      //Format two: ['GET', 'POST']
      if(is_string($value)){
        $action = trim(strtolower($value));
      }
      
      //Map GET POST PUT to action names.
      switch ($action) {
        case 'get': $action = 'read'; break;
        case 'post': $action = 'create'; break;
        case 'put': $action = 'replace'; break;
      }
      
      //If we found no action in this item.
      if(!isset($action))
        throw new Exception("Invalid action format '{$key}' => '{$value}'.");
      
      //See if this is a known action.
      if(!array_key_exists($action, $output))
        throw new Exception("Unknown resource action '{$action}'.");
      
      //Set the action to true.
      $output[$action] = true;
      
    }
    
    return $output;
    
  }
  
  protected function hasAction($name)
  {
    return array_key_exists($name, $this->actions) && $this->actions[$name] === true;
  }
  
}