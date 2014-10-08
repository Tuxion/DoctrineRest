<?php namespace Tuxion\DoctrineRest\Domain\Composite;

interface CompositeCallInterface
{
  
  public function getBefores();
  public function getMethod();
  public function getAfters();
  public function setBefores(array $value);
  public function setMethod($value);
  public function setAfters(array $value);
  public function __invoke();
  
}