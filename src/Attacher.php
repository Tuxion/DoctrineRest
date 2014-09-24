<?php namespace Tuxion\DoctrineRest;

class Attacher
{
  
  protected $router;
  
  public function __construct($router)
  {
    $this->router = $router;
  }
  
}