<?php namespace Tuxion\DoctrineRest\Domain\Result;

/**
 * Represents a result of a domain operation.
 */
abstract class AbstractResult implements ResultInterface
{
  
  /**
   * The body of this result.
   * @var mixed
   */
  protected $body;
  
  /**
   * Returns the body of this result.
   * @return mixed
   */
  public function getBody(){
    return $this->body;
  }
  
  /**
   * Creates a new Result instance.
   * @param mixed $body The body of this result.
   */
  public function __construct($body)
  {
    $this->body = $body;
  }
  
}
