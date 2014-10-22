<?php namespace Tuxion\DoctrineRest\Domain\Result;

/**
 * Represents a custom result in the domain.
 * The exact meaning of the result can be retrieved from an HTTP status code.
 */
class CustomResult extends AbstractResult
{
  
  /**
   * The HTTP status code representing the status of this result.
   * @var integer?
   */
  protected $code;
  
  /**
   * Returns the HTTP status code representing the status of this result.
   * @return integer?
   */
  public function getCode(){
    return $this->code;
  }
  
  /**
   * Creates a new instance of CustomResult.
   * @param mixed    $body The body of this result.
   * @param integer? $code (optional) The HTTP status code representing the status of this result. Default is NULL.
   */
  public function __construct($body, $code=null)
  {
    parent::__construct($body);
    $this->code = $code ? (int)$code : null;
  }
  
}