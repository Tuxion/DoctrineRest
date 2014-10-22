<?php namespace Tuxion\DoctrineRest\Domain\Result;

/**
 * Represents a result of a domain operation.
 */
interface ResultInterface
{
  
  /**
   * Returns the body of this result.
   * @return mixed
   */
  public function getBody();
  
}
