<?php namespace Tuxion\DoctrineRest\Action;

use \Exception;
use Tuxion\DoctrineRest\Domain\Composite\CompositeCallInterface;

/**
 * A generic action class that implements the Action phase in the Action-Domain-Responder paradigm.
 * It covers each of the read, replace, delete and create actions, since they are very similar.
 */
class Action
{
  
  /**
   * The class name of the model that this action should operate on.
   * @var string
   */
  protected $model;
  
  /**
   * The action that should be performed on this model.
   * Can be read, replace, delete or create.
   * @var string
   */
  protected $action;
  
  /**
   * The action environment that wraps several dependencies for the action.
   * @var Environment
   */
  protected $environment;
  
  /**
   * The composite call that contains the before and after hooks for this action instance.
   * @var CompositeCallInterface
   */
  protected $compositeCall;
  
  /**
   * An array of actions that are currently implemented.
   * @var array
   */
  protected $knownActions;
  
  /**
   * Returns the action environment that wraps several dependencies for the action.
   * @return Environment
   */
  public function getEnvironment(){
    return $this->environment;
  }
  
  /**
   * Returns the class name of the model that this action should operate on.
   * @return string
   */
  public function getModel(){
    return $this->model;
  }
  
  /**
   * Return the action that should be performed on this model.
   * @return string
   */
  public function getAction(){
    return $this->action;
  }
  
  /**
   * Creates a new Action instance.
   * @param Environment            $environment   The action environment that wraps several dependencies for the action.
   * @param CompositeCallInterface $compositeCall The composite call that contains the before and after hooks for this action instance.
   * @param string                 $action        The action that should be performed on this model.
   * @param string                 $model         The class name of the model that this action should operate on.
   */
  public function __construct(
    Environment $environment,
    CompositeCallInterface $compositeCall,
    $action,
    $model
  ){
    
    //Store the provided dependencies.
    $this->model = $model;
    $this->action = $action;
    $this->environment = $environment;
    $this->compositeCall = $compositeCall;
    
    //Define the actions we implemented.
    $this->knownActions = array('create', 'read', 'replace', 'delete');
    
    //Verify the provided action is a known one.
    if(!in_array($action, $this->knownActions))
      throw new Exception("Unknown action '".$action."'");
    
    //Add ourself as the main method of the composite call.
    $this->compositeCall->setMethod(array($this, 'performAction'));
    
  }
  
  /**
   * Executes the main method as part of the composite call chain.
   * @return ResultInterface
   */
  public function performAction()
  {
    
    //Initialize a safe default.
    $result = null;
    
    //Shortcut the action variable.
    $action = $this->action;
    switch($action){
      
      //A create operation, takes the body and persists a new instance of $model.
      case 'create':
        $data = $this->getRequestContent();
        $result = $this->environment->getDriver()->$action($this->model, $data);
        break;
      
      //A replace operation, takes the body and replaces that with the current data, given the ID.
      case 'replace':
        $id = $this->environment->getRequest()->params['id'];
        $data = $this->getRequestContent();
        $result = $this->environment->getDriver()->$action($this->model, $id, $data);
        break;
      
      //Read or read-all operations, has an optional ID parameter.
      case 'read':
        $id = null;
        $params = $this->environment->getRequest()->params;
        
        if(array_key_exists('id', $params))
          $id = $params['id'];
        
        $result = $this->environment->getDriver()->$action($this->model, $id);
        break;
      
      //Delete operations, needs an ID to operate.
      case 'delete':
        $id = $this->environment->getRequest()->params['id'];
        $result = $this->environment->getDriver()->$action($this->model, $id);
        break;
      
    }
    
    //Return the result our Driver has given us.
    return $result;
    
  }
  
  /**
   * Executes the full action, including before and after hooks from the composite call.
   * @return Responder
   */
  public function __invoke()
  {
    
    //Get the result of the full composite call.
    $result = $this->compositeCall->__invoke();
    
    //Set this result on the responder.
    $responder = $this->environment->getResponder();
    $responder->setResult($result);
    
    //Return the responder, as per Aura standards.
    return $responder;
    
  }
  
  /**
   * Helper method to retrieve the message body as an associative array.
   * Note: the body must be in JSON, since these are REST actions.
   * @return array
   */
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