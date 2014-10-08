<?php namespace Tuxion\DoctrineRest\Mapper;

use Tuxion\DoctrineRest\Action\ActionFactory;

class ResourceFactory
{
  
  protected $actionFactory;
  
  public function getActionFactory(){
    return $this->actionFactory;
  }
  
  public function __construct(ActionFactory $actionFactory)
  {
    $this->actionFactory = $actionFactory;
  }
  
  public function __invoke($actions, $model)
  {
    return new Resource($this->actionFactory, $actions, $model);
  }
  
}