<?php namespace Tuxion\DoctrineRest\Action;

use Aura\Web\Request;
use Tuxion\DoctrineRest\Domain\Driver\DriverInterface;
use Tuxion\DoctrineRest\Responder\ResponderInterface;

class Environment
{
  
  protected $driver;
  protected $request;
  protected $responder;
  
  public function getRequest(){
    return $this->request;
  }
  
  public function getResponder(){
    return $this->responder;
  }
  
  public function getDriver(){
    return $this->driver;
  }
  
  public function __construct(Request $request, ResponderInterface $responder, DriverInterface $driver)
  {
    $this->driver = $driver;
    $this->request = $request;
    $this->responder = $responder;
  }
  
}