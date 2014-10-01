<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \Exception;

/**
 * A helper factory that creates any available Result class.
 */
class ResultFactory
{
  
  /**
   * Magic mapping of result classes to method calls.
   * When two arrays were given, they will be merged.
   * 
   * Example: $resultFactory->found($data) maps to new Tuxion\DoctrineRest\Domain\Result\FoundResult($data)
   * 
   * @param  string $name      The name of the method that was called.
   * @param  array  $arguments The arguments passed to the method.
   * @return mixed The return value of the method.
   */
  public function __call($name, array $arguments)
  {
    
    //The first argument is the data to pass to the result class.
    //Use an empty array by default.
    $data = isset($arguments[0]) ? $arguments[0] : array();
    
    //Check the type of data.
    if(!is_array($data))
      throw new Exception("Invalid argument. Only arrays can be passed to this function.");
    
    //Create the class and pass it the merged data.
    //Note: this will throw an exception if the class does not exist.
    $class = 'Tuxion\DoctrineRest\Domain\Result\\'.ucfirst($name).'Result';
    return new $class($data);
    
  }
  
}