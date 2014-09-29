<?php namespace Tuxion\DoctrineRest\Domain\Result;

class ErrorResult extends AbstractResult
{
  
  protected $exception;
  
  public function getException(){
    return $this->exception;
  }
  
  public function __construct(array $body, \Exception $exception)
  {
    parent::__construct($body);
    $this->exception = $exception;
  }
  
}
