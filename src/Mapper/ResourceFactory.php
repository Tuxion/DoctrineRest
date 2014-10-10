<?php namespace Tuxion\DoctrineRest\Mapper;

use Tuxion\DoctrineRest\Action\ActionFactory;
use Tuxion\DoctrineRest\Domain\Composite\CompositeCallFactory;

/**
 * A factory for new Resource instances.
 */
class ResourceFactory
{
  
  /**
   * A factory for new Action instances.
   * @var ActionFactory
   */
  protected $actionFactory;
  
  /**
   * A factory for new CompositeCall instances.
   * @var CompositeCallFactory
   */
  protected $compsiteCallFactory;
  
  /**
   * Returns the factory for new CompositeCall instances.
   * @return CompositeCallFactory
   */
  public function getCompsiteCallFactory(){
    return $this->compsiteCallFactory;
  }
  
  /**
   * Returns the factory for new Action instances.
   * @return ActionFactory
   */
  public function getActionFactory(){
    return $this->actionFactory;
  }
  
  /**
   * Creates a new instance of ResourceFactory.
   * @param ActionFactory        $actionFactory       A factory for new Action instances.
   * @param CompositeCallFactory $compsiteCallFactory A factory for new CompositeCall instances.
   */
  public function __construct(ActionFactory $actionFactory, CompositeCallFactory $compsiteCallFactory)
  {
    $this->actionFactory = $actionFactory;
    $this->compsiteCallFactory = $compsiteCallFactory;
  }
  
  /**
   * Creates a new instance of Resource.
   * @param  mixed  $actions The actions to support for this resource (GET|POST|PUT|DELETE, read|create|replace|delete, as string or array).
   * @param  string $model   The class name of the model to map this resource to.
   * @return Resource
   */
  public function __invoke($actions, $model)
  {
    return new Resource($this->actionFactory, $this->compsiteCallFactory, $actions, $model);
  }
  
}