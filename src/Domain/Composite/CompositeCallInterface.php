<?php namespace Tuxion\DoctrineRest\Domain\Composite;

/**
 * This represents a 3-stage method call.
 * 
 * First the before methods are called.
 * Secondly the (main) method is called.
 * Lastly the after methods are called.
 * 
 * For the before and after methods, a return value is optional.
 * For the main method a return value is mandatory.
 * Any return value must be a ResultInterface instance.
 * 
 * If a before method returns a result, this ends execution immediately.
 * 
 * For the return values of the main method and after methods, the most recent return value is used as the final output.
 * 
 * If at any point an exception was thrown, the chain stops execution immediately and returns an ErrorResult instance based on the exception.
 * 
 */
interface CompositeCallInterface
{
  
  /**
   * Returns the array of callable before methods.
   * @return array
   */
  public function getBefores();
  
  /**
   * Returns the callable (main) method.
   * @return array
   */
  public function getMethod();
  
  /**
   * Returns the array of callable after methods.
   * @return array
   */
  public function getAfters();
  
  /**
   * Sets an array of callable before methods.
   * @param array $value
   */
  public function setBefores(array $value);
  
  /**
   * Sets a callable (main) method.
   * @param array $value
   */
  public function setMethod($value);
  
  /**
   * Sets an array of callable after methods.
   * @param array $value
   */
  public function setAfters(array $value);
  
  /**
   * Executes the chain of before, main and after methods.
   * @return ResultInterface
   */
  public function __invoke();
  
}