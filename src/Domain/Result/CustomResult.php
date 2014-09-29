<?php namespace Tuxion\DoctrineRest\Domain\Result;

class CustomResult extends AbstractResult
{
  
  protected $code;
  
  public function getCode(){
    return $this->code;
  }
  
  public function setCode($value){
    $this->code = (int)$value;
  }
  
}