<?php namespace Tuxion\DoctrineRest;

use Tuxion\DoctrineRest\Action\ActionFactory;

class RouteAttacher
{
  
  protected $model;
  protected $resource;
  protected $actionFactory;
  
  public function getModel(){
    return $this->model;
  }
  
  public function getResource(){
    return $this->resource;
  }
  
  public function getActionFactory(){
    return $this->actionFactory;
  }
  
  public function __construct(ActionFactory $actionFactory, $model, $resource)
  {
    $this->model = $model;
    $this->resource = $resource;
    $this->actionFactory = $actionFactory;
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
    $factory->setResource($this->resource);
    
    //Add routes.
    $router->addPost('create', '')->addValues(array('action'=>$factory('create')));
    $router->addGet('read', '/{id}')->addValues(array('action'=>$factory('read')));
    $router->addPut('replace', '/{id}')->addValues(array('action'=>$factory('replace')));
    $router->addDelete('delete', '/{id}')->addValues(array('action'=>$factory('delete')));
    
  }
  
}