<?php namespace Tuxion\DoctrineRest\Domain\Composite;

use \Exception;
use Tuxion\DoctrineRest\Domain\Result\ResultInterface;
use Tuxion\DoctrineRest\Domain\Result\ErrorResult;

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
class CompositeCall implements CompositeCallInterface
{
  
  /**
   * The array of callable before methods.
   * @var array
   */
  protected $befores;
  
  /**
   * The array of callable after methods.
   * @var array
   */
  protected $afters;
  
  /**
   * The callable (main) method.
   * @var array
   */
  protected $method;
  
  /**
   * Returns the callable (main) method.
   * @return array
   */
  public function getMethod(){
    return $this->method;
  }
  
  /**
   * Sets a callable (main) method.
   * @param array $value
   */
  public function setMethod($value)
  {
    
    //Verify manually, since PHP 5.3 does not support type hinting of this yet.
    if(!is_callable($value))
      throw new Exception("Method must be callable.");
    
    $this->method = $value;
    
  }
  
  /**
   * Returns the array of callable after methods.
   * @return array
   */
  public function getAfters(){
    return $this->afters;
  }
  
  /**
   * Sets an array of callable after methods.
   * @param array $value
   */
  public function setAfters(array $value)
  {
    
    //Verify manually since PHP does not support type hinting of array contents.
    foreach($value as $method){
      if(!is_callable($method))
        throw new Exception("All methods must be callable.");
    }
    
    $this->afters = $value;
    
  }
  
  /**
   * Returns the array of callable before methods.
   * @return array
   */
  public function getBefores(){
    return $this->befores;
  }
  
  /**
   * Sets an array of callable before methods.
   * @param array $value
   */
  public function setBefores(array $value)
  {
    
    //Verify manually since PHP does not support type hinting of array contents.
    foreach($value as $method){
      if(!is_callable($method))
        throw new Exception("All methods must be callable.");
    }
    
    $this->befores = $value;
    
  }
  
  /**
   * Creates a new instance of CompositeCall.
   */
  public function __construct()
  {
    $this->befores = array();
    $this->afters = array();
  }
  
  /**
   * Executes the chain of before, main and after methods.
   * @return ResultInterface
   */
  public function __invoke()
  {
    
    //A method must be defined.
    if(!is_callable($this->method))
      throw new Exception("A callable method must be set before invoking.");
    
    try {
      
      //Track the result.
      $result = null;
      
      //Loop through the before calls.
      foreach($this->befores as $method)
      {
        
        //Execute the callback.
        $result = call_user_func($method);
        
        //If the return value is null, continue the operation.
        if(!is_null($result)){
          
          //Only results may be returned.
          if(!$result instanceof ResultInterface)
            throw new Exception("Before calls must return a domain result, or throw exceptions. Returned \"".gettype($result)."\".");
          
          //When we do have a result, this means the end of the chain.
          return $result;
          
        }
        
      }
      
      //Execute the main method.
      $result = call_user_func($this->method);
      
      //Only results may be returned.
      if(!$result instanceof ResultInterface)
        throw new Exception("The main method must return a domain result, or throw exceptions. Returned \"".gettype($result)."\".");
      
      //Now post-process it with the afters.
      //Note: only exceptions can break the chain here.
      foreach($this->afters as $method)
      {
        
        //Execute the callback.
        $newResult = call_user_func($method);
        
        //Only if something was returned.
        if(!is_null($newResult))
          $result = $newResult;
        
        //Only results may be returned.
        if(!$result instanceof ResultInterface)
          throw new Exception("After calls must return a domain result, or throw exceptions. Returned \"".gettype($result)."\".");
        
      }
      
      //Return the final result.
      return $result;
      
    }
    
    //An exception at any point interrupts the chain.
    catch(Exception $ex){
      return new ErrorResult(null, $ex);
    }
    
  }
  
}