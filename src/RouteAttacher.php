<?php namespace Tuxion\DoctrineRest;

class RouteAttacher
{
  
  public function __invoke($router)
  {
    
    //What does an ID look like?
    $router->setTokens(array(
      'id' => '\d+'
    ));
    
    //Add routes.
    $router->addPost('create', '');
    $router->addGet('read', '/{id}');
    $router->addPut('replace', '/{id}');
    $router->addDelete('delete', '/{id}');
    
  }
  
}