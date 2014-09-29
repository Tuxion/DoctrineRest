<?php namespace Tuxion\DoctrineRest\Responder;

use Tuxion\DoctrineRest\Domain\Result\ResultInterface;

class StatusCodes
{
  
  public function fromResult(ResultInterface $result)
  {
    
    //Shortcut for the namespace.
    $ns = 'Tuxion\DoctrineRest\Domain\Result';
    
    switch(get_class($result)){
      
      case "$ns\CustomResult":    return $result->getCode();
      case "$ns\CreatedResult":   return 201;
      case "$ns\ReplacedResult":  return 200;
      case "$ns\DeletedResult":   return 200;
      case "$ns\FoundResult":     return 200;
      case "$ns\ErrorResult":     return 500;
      default:                    return 500;
      
    }
    
  }
  
}