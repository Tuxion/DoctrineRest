<?php namespace Tuxion\DoctrineRest\Mapper;

use \Exception;
use Tuxion\DoctrineRest\Action\ActionFactory;

class Resource
{
  
  protected $model;
  protected $actionFactory;
  protected $actions;
  protected $befores;
  protected $afters;
  
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
  
  public function __construct(ActionFactory $actionFactory, $actions, $model)
  {
    
    if(!(is_string($model) && strlen($model) > 0))
      throw new Exception("Model must be a string (class name).");
    
    $this->model = $model;
    $this->actionFactory = $actionFactory;
    
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
    
    //action factory, action params
    $factory = $this->actionFactory;
    $factory->setModel($this->model);
    
    //Add routes.
    if($this->hasAction('create')){
      $router->addPost('create', '')->addValues(array('action'=>$factory('create')));
    }
    
    if($this->hasAction('read')){
      $router->addGet('read', '/{id}')->addValues(array('action'=>$factory('read')));
    }
    
    if($this->hasAction('replace')){
      $router->addPut('replace', '/{id}')->addValues(array('action'=>$factory('replace')));
    }
    
    if($this->hasAction('delete')){
      $router->addDelete('delete', '/{id}')->addValues(array('action'=>$factory('delete')));
    }
    
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