<?php namespace Tuxion\DoctrineRest\Action;

use Tuxion\DoctrineRest\Domain\Composite\CompositeCallInterface;

/**
 * Factory that can create multiple Action instances.
 */
class ActionFactory
{
  
  /**
   * The class name of the model that created actions should operate on.
   * @var string
   */
  protected $model;
  
  /**
   * The action environment that wraps several dependencies for the created actions.
   * @var Environment
   */
  protected $environment;
  
  /**
   * Returns the action environment that wraps several dependencies for the created actions.
   * @return Environment
   */
  public function getEnvironment(){
    return $this->environment;
  }
  
  /**
   * Returns the class name of the model that created actions should operate on.
   * @return string
   */
  public function getModel(){
    return $this->model;
  }
  
  /**
   * Sets the class name of the model that created actions should operate on.
   * @param string $value
   */
  public function setModel($value){
    $this->model = $value;
  }
  
  /**
   * Constructs a new ActionFactory instance.
   * @param Environment $environment The environment to pass to new actions.
   */
  public function __construct(Environment $environment)
  {
    $this->environment = $environment;
  }
  
  /**
   * Creates a new Action instance.
   * @param  CompositeCallInterface $compositeCall The composite call (including before and afters) for this Action.
   * @param  string                 $action        The action name to be executed by the created Action.
   * @return Action
   */
  public function __invoke(CompositeCallInterface $compositeCall, $action)
  {
    return new Action(
      $this->environment,
      $compositeCall,
      $action,
      $this->model
    );
  }
  
}