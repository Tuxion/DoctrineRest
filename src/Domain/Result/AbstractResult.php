<?php namespace Tuxion\DoctrineRest\Domain\Result;

abstract class AbstractResult implements ResultInterface
{
  
  protected $body;
  
  public function getBody(){
    return $this->body;
  }
  
  public function __construct(array $body)
  {
    $this->body = $body;
  }
  
}
