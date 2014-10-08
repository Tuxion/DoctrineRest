<?php namespace Tuxion\DoctrineRest\Mapper;

use \Exception;
use Aura\Router\Router;

class ResourceMapper
{
  
  protected $router;
  protected $routePrefix;
  protected $resourceFactory;
  
  public function getResourceFactory(){
    return $this->resourceFactory;
  }
  
  public function getRoutePrefix(){
    return $this->routePrefix;
  }
  
  public function getRouter(){
    return $this->router;
  }
  
  public function __construct(ResourceFactory $resourceFactory, Router $router, $routePrefix)
  {
    $this->router = $router;
    $this->routePrefix = $routePrefix;
    $this->resourceFactory = $resourceFactory;
  }
  
  public function resource($actions, $resource, $model)
  {
    
    $instance = $this->resourceFactory->__invoke($actions, $model);
    $this->router->attach('rest.resource.'.$resource, "{$this->routePrefix}/$resource", $instance);
    return $instance;
    
  }
  
}