<?php namespace Tuxion\DoctrineRest\Mapper;

use \Exception;
use Tuxion\DoctrineRest\Action\ActionFactory;
use Tuxion\DoctrineRest\Domain\Composite\CompositeCallFactory;

/**
 * A class that represents one resource that has been attached to the Aura Router.
 */
class Resource
{
  
  /**
   * The class name of the model to map this resource to.
   * @var string
   */
  protected $model;
  
  /**
   * A factory for new Action instances.
   * @var ActionFactory
   */
  protected $actionFactory;
  
  /**
   * The actions to support for this resource (in a normalized array format).
   * @var array
   * @see $this->normalizeActions()
   */
  protected $actions;
  
  /**
   * The array of callable before methods per action.
   * @var array
   */
  protected $befores;
  
  /**
   * The array of callable after methods per action.
   * @var array
   */
  protected $afters;
  
  /**
   * A factory for new CompositeCall instances.
   * @var CompositeCallFactory
   */
  protected $compositeCallFactory;
  
  /**
   * A cache of already generated composite calls per action.
   * @var array
   */
  protected $generatedComposites;
  
  /**
   * Returns the factory for new CompositeCall instances.
   * @return CompositeCallFactory
   */
  public function getCompositeCallFactory(){
    return $this->compositeCallFactory;
  }
  
  /**
   * Returns the array of callable after methods per action.
   * @return array
   */
  public function getAfters(){
    return $this->afters;
  }
  
  /**
   * Returns the array of callable before methods per action.
   * @return array
   */
  public function getBefores(){
    return $this->befores;
  }
  
  /**
   * Returns the actions to support for this resource (in a normalized array format).
   * @return array
   * @see $this->normalizeActions()
   */
  public function getActions(){
    return $this->actions;
  }
  
  /**
   * Returns the class name of the model to map this resource to.
   * @return string
   */
  public function getModel(){
    return $this->model;
  }
  
  /**
   * Returns the factory for new Action instances.
   * @return ActionFactory
   */
  public function getActionFactory(){
    return $this->actionFactory;
  }
  
  /**
   * Creates a new Resource instance.
   * @param ActionFactory        $actionFactory        A factory for new Action instances.
   * @param CompositeCallFactory $compositeCallFactory A factory for new CompositeCall instances.
   * @param mixed                $actions              The actions to support for this resource (GET|POST|PUT|DELETE, read|create|replace|delete, as string or array).
   * @param string               $model                The class name of the model to map this resource to.
   */
  public function __construct(ActionFactory $actionFactory, CompositeCallFactory $compositeCallFactory, $actions, $model)
  {
    
    //Strings should really be strings... PHP doesn't type hint this for you.
    if(!(is_string($model) && strlen($model) > 0))
      throw new Exception("Model must be a string (class name).");
    
    //Store the dependencies.
    $this->model = $model;
    $this->actionFactory = $actionFactory;
    $this->compositeCallFactory = $compositeCallFactory;
    
    //Normalize the given actions into our internal format.
    $this->actions = $this->normalizeActions($actions);
    
    //Create empty arrays for each of our actions, so we can append items later.
    $this->befores = array();
    $this->afters = array();
    foreach($this->actions as $action => $value){
      $this->befores[$action] = array();
      $this->afters[$action] = array();
    }
    $this->generatedComposites = array();
    
  }
  
  /**
   * Attaches a new before method to the given actions.
   * @param  mixed    $actions  The actions to support for this resource (GET|POST|PUT|DELETE, read|create|replace|delete, as string or array).
   * @param  callable $callback The callable that is to be executed.
   * @return self For the sake of chaining calls.
   */
  public function before($actions, $callback)
  {
    
    //Normalize the given actions to our internal format and iterate them.
    $actions = $this->normalizeActions($actions);
    foreach($actions as $action => $value)
    {
      
      //The format dictates you should explicitly declare support for this action.
      if($value !== true)
        continue;
      
      //Append the method to our internal references for this action.
      $this->befores[$action][] = $callback;
      
      //If the CompositeCall has already been created, update it with the new set of actions.
      if(array_key_exists($action, $this->generatedComposites)){
        $composite = $this->generatedComposites[$action];
        $composite->setBefores($this->befores[$action]);
      }
      
    }
    
    //Enable chaining of calls.
    return $this;
    
  }
  
  /**
   * Attaches a new after method to the given actions.
   * @param  mixed    $actions  The actions to support for this resource (GET|POST|PUT|DELETE, read|create|replace|delete, as string or array).
   * @param  callable $callback The callable that is to be executed.
   * @return self For the sake of chaining calls.
   */
  public function after($actions, $callback)
  {
    
    //Normalize the given actions to our internal format and iterate them.
    $actions = $this->normalizeActions($actions);
    foreach($actions as $action => $value)
    {
      
      //The format dictates you should explicitly declare support for this action.
      if($value !== true)
        continue;
      
      //Append the method to our internal references for this action.
      $this->afters[$action][] = $callback;
      
      //If the CompositeCall has already been created, update it with the new set of actions.
      if(array_key_exists($action, $this->generatedComposites)){
        $composite = $this->generatedComposites[$action];
        $composite->setAfters($this->afters[$action]);
      }
      
    }
    
    //Enable chaining of calls.
    return $this;
    
  }
  
  /**
   * Attach all the required routes on the Aura Router.
   * @param  Router $router The Aura Router to attach our routes to.
   * @return void
   */
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
      $router->addGet($action, '{/id}')->addValues(array('action'=>$this->createAction($action)));
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
  
  /**
   * Simplification to create a new Action.
   * @param  string $action The name of the action to create the Action for.
   * @return Action
   */
  protected function createAction($action)
  {
    
    //Return a new instance that includes the required composite call.
    return $this->actionFactory->__invoke(
      $this->createCompositeCall($action),
      $action
    );
    
  }
  
  /**
   * Simplification to create a composite call for a given action.
   * @param  string $action The name of the action to create the CompositeCall for.
   * @return CompositeCall
   */
  protected function createCompositeCall($action)
  {
    
    //Creates a new CompositeCall.
    $call = $this->compositeCallFactory->__invoke();
    
    //Maps the known befores and afters to it.
    $call->setBefores($this->befores[$action]);
    $call->setAfters($this->afters[$action]);
    
    //Stores a reference in the generated calls array.
    $this->generatedComposites[$action] = $call;
    
    //Returns the new call.
    return $call;
    
  }
  
  /**
   * Normalizes a given actions representation to an internal format.
   * 
   * Format option 1: '*' (wildcard string)
   *   This resolves to all actions being supported.
   * 
   * Format option 2: 'action1|action2'
   *   This resolves to each of the given actions to be accepted.
   * 
   * Format option 3: ['action1', 'action2']
   *   This resolves to each of the given actions to be accepted.
   * 
   * Format option 4: ['action1' => true, 'action2' => false]
   *   This resolves to each of the given actions that have a value of TRUE, to be accepted.
   * 
   * Possible action names (values behind equals are HTTP method aliases):
   *   read     == GET
   *   create   == POST
   *   replace  == PUT
   *   delete   == DELETE
   * 
   * The normalized output is in the option 4 format,
   *   but with all available actions defined either as true or false
   *   and all aliases will be resolved to their primary names.
   * 
   * @param  mixed  $actions A set of actions in one of the possible formats.
   * @return array
   */
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
  
  /**
   * Simplified check that an action has been enabled for this resource.
   * @param  string  $name The name of the action to run this check for.
   * @return boolean       Whether or not this action has been enabled for this resource.
   */
  protected function hasAction($name)
  {
    return array_key_exists($name, $this->actions) && $this->actions[$name] === true;
  }
  
}