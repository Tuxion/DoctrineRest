<?php namespace Tuxion\DoctrineRest\Mapper;

use Tuxion\DoctrineRest\Action\ActionFactory;
use Tuxion\DoctrineRest\Domain\Composite\CompositeCallFactory;

class ResourceFactory
{
  
  protected $actionFactory;
  protected $compsiteCallFactory;
  
  public function getCompsiteCallFactory(){
    return $this->compsiteCallFactory;
  }
  
  public function getActionFactory(){
    return $this->actionFactory;
  }
  
  public function __construct(ActionFactory $actionFactory, CompositeCallFactory $compsiteCallFactory)
  {
    $this->actionFactory = $actionFactory;
    $this->compsiteCallFactory = $compsiteCallFactory;
  }
  
  public function __invoke($actions, $model)
  {
    return new Resource($this->actionFactory, $this->compsiteCallFactory, $actions, $model);
  }
  
}