<?php namespace Tuxion\DoctrineRest\Domain\Composite;

use \Exception;
use Tuxion\DoctrineRest\Domain\Result\ResultInterface;
use Tuxion\DoctrineRest\Domain\Result\ErrorResult;

class CompositeCallFactory
{
  
  public function __invoke()
  {
    return new CompositeCall();
  }
  
}