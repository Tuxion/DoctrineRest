<?php namespace Tuxion\DoctrineRest\Domain\Result;

class CustomResult extends AbstractResult
{
  
  protected $code;
  
  public function getCode(){
    return $this->code;
  }
  
  public function __construct(array $body, $code=null)
  {
    parent::__construct($body);
    $this->code = $code ? (int)$code : null;
  }
  
}