<?php namespace Tuxion\DoctrineRest\Domain\Result;

interface ResultInterface
{
  
  public function getBody();
  public function __construct(array $body);
  
}
