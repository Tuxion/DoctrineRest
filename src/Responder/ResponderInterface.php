<?php namespace Tuxion\DoctrineRest\Responder;

use Aura\Web\Response;
use Tuxion\DoctrineRest\Domain\Result\ResultInterface;

/**
 * An interface to make sure that responders always accept results and can be invoked.
 */
interface ResponderInterface
{
  
  /**
   * Returns the ResultInterface that holds the final state of the application to communicate to the user.
   * @return ResultInterface
   */
  public function getResult();
  
  /**
   * Sets the ResultInterface that holds the final state of the application to communicate to the user.
   * @param ResultInterface $result
   * @return void
   */
  public function setResult(ResultInterface $result);
  
  /**
   * Sets the response on an Aura Response object and returns it.
   * @return Response
   */
  public function __invoke();
  
}