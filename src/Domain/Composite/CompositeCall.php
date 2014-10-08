<?php namespace Tuxion\DoctrineRest\Domain\Composite;

use \Exception;
use Tuxion\DoctrineRest\Domain\Result\ResultInterface;
use Tuxion\DoctrineRest\Domain\Result\ErrorResult;

class CompositeCall implements CompositeCallInterface
{
  
  protected $befores;
  protected $afters;
  protected $method;
  
  public function getMethod(){
    return $this->method;
  }
  
  public function setMethod($value)
  {
    
    if(!is_callable($value))
      throw new Exception("Method must be callable.");
    
    $this->method = $value;
    
  }
  
  public function getAfters(){
    return $this->afters;
  }
  
  public function setAfters(array $value)
  {
    
    foreach($value as $method){
      if(!is_callable($method))
        throw new Exception("All methods must be callable.");
    }
    
    $this->afters = $value;
    
  }
  
  public function getBefores(){
    return $this->befores;
  }
  
  public function setBefores(array $value)
  {
    
    foreach($value as $method){
      if(!is_callable($method))
        throw new Exception("All methods must be callable.");
    }
    
    $this->befores = $value;
    
  }
  
  public function __construct()
  {
    $this->befores = array();
    $this->afters = array();
  }
  
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