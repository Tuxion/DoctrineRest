<?php namespace Tuxion\DoctrineRest\Mapper;

use \Exception;
use Aura\Router\Router;

/**
 * The mapper class that provides all the sugar you need to write simple configuration files.
 * This will generate all the resources you want to provide a REST interface for.
 */
class ResourceMapper
{
  
  /**
   * The Aura Router object to attach new resources to.
   * @var Router
   */
  protected $router;
  
  /**
   * A URL route prefix for all the resources created with this mapper.
   * @var string
   */
  protected $routePrefix;
  
  /**
   * A factory for new Resource instances.
   * @var ResourceFactory
   */
  protected $resourceFactory;
  
  /**
   * Returns the factory for new Resource instances.
   * @return ResourceFactory
   */
  public function getResourceFactory(){
    return $this->resourceFactory;
  }
  
  /**
   * Returns the URL route prefix for all the resources created with this mapper.
   * @return string
   */
  public function getRoutePrefix(){
    return $this->routePrefix;
  }
  
  /**
   * Returns the Aura Router object to attach new resources to.
   * @return Router
   */
  public function getRouter(){
    return $this->router;
  }
  
  /**
   * Constructs a new ResourceMapper instance.
   * @param ResourceFactory $resourceFactory The factory for new Resource instances.
   * @param Router          $router          The Aura Router object to attach new resources to.
   * @param [type]          $routePrefix     A URL route prefix for all the resources created with this mapper.
   */
  public function __construct(ResourceFactory $resourceFactory, Router $router, $routePrefix)
  {
    $this->router = $router;
    $this->routePrefix = $routePrefix;
    $this->resourceFactory = $resourceFactory;
  }
  
  /**
   * Creates a new Resource and attaches it to the Aura Router.
   * @param  mixed  $actions  The actions to support for this resource (GET|POST|PUT|DELETE, read|create|replace|delete, as string or array).
   * @param  string $resource The name of the resource. Will be used in the URL.
   * @param  string $model    The class name of the model to map this resource to.
   * @return Resource A Resource instance to further manipulate this attached resource.
   */
  public function resource($actions, $resource, $model)
  {
    
    //Create a new Resource.
    $instance = $this->resourceFactory->__invoke($actions, $model);
    
    //Attach the resource to the router.
    $this->router->attach('rest.resource.'.$resource, "{$this->routePrefix}/$resource", $instance);
    
    //Return it for further manipulation.
    return $instance;
    
  }
  
}