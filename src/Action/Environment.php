<?php namespace Tuxion\DoctrineRest\Action;

use Aura\Web\Request;
use Tuxion\DoctrineRest\Domain\Driver\DriverInterface;
use Tuxion\DoctrineRest\Responder\ResponderInterface;

/**
 * A class that wraps dependencies that apply to every Action.
 */
class Environment
{
  
  /**
   * The driver that will execute our actions on the Domain.
   * @var DriverInterface
   */
  protected $driver;
  
  /**
   * The Aura request providing us with the required input.
   * @var Request
   */
  protected $request;
  
  /**
   * The Responder that will take our result object.
   * @var ResponderInterface
   */
  protected $responder;
  
  /**
   * Returns the Aura request providing us with the required input.
   * @return Request
   */
  public function getRequest(){
    return $this->request;
  }
  
  /**
   * Returns the Responder that will take our result object.
   * @return ResponderInterface
   */
  public function getResponder(){
    return $this->responder;
  }
  
  /**
   * Returns the driver that will execute our actions on the Domain.
   * @return DriverInterface
   */
  public function getDriver(){
    return $this->driver;
  }
  
  /**
   * Creates a new Environment instance.
   * @param Request            $request   The Aura request providing us with the required input.
   * @param ResponderInterface $responder The Responder that will take our result object.
   * @param DriverInterface    $driver    The driver that will execute our actions on the domain.
   */
  public function __construct(Request $request, ResponderInterface $responder, DriverInterface $driver)
  {
    $this->driver = $driver;
    $this->request = $request;
    $this->responder = $responder;
  }
  
}