<?php namespace Tuxion\DoctrineRest\Responder;

use Tuxion\DoctrineRest\Domain\Result\ResultInterface;

/**
 * A helper class that maps ResultInterface instances to HTTP status codes.
 */
class StatusCodes
{
  
  /**
   * Gets an HTTP status code based on the ResultInterface that was provided.
   * @param  ResultInterface $result The ResultInterface to find the corresponding HTTP status code for.
   * @return integer An HTTP status code.
   */
  public function fromResult(ResultInterface $result)
  {
    
    //Shortcut for the namespace.
    $ns = 'Tuxion\DoctrineRest\Domain\Result';
    
    switch(get_class($result)){
      
      //Custom status codes...
      case "$ns\CustomResult":    return $result->getCode();
      
      //201 Created
      case "$ns\CreatedResult":   return 201;
      
      //200 OK
      case "$ns\ReplacedResult":  return 200;
      case "$ns\DeletedResult":   return 200;
      case "$ns\FoundResult":     return 200;
      
      //404 Not Found
      case "$ns\NotFoundResult":  return 404;
      
      //500 Internal Server Error
      case "$ns\ErrorResult":     return 500;
      default:                    return 500;
      
    }
    
  }
  
}