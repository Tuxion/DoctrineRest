<?php namespace Tuxion\DoctrineRest\Action;

class ActionFactory
{
  
  protected $model;
  protected $environment;
  
  public function getEnvironment(){
    return $this->environment;
  }
  
  public function getModel(){
    return $this->model;
  }
  
  public function setModel($value){
    $this->model = $value;
  }
  
  public function __construct(Environment $environment)
  {
    $this->environment = $environment;
  }
  
  public function __invoke($action)
  {
    return new Action(
      $this->environment,
      $action,
      $this->model
    );
  }
  
}