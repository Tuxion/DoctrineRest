<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \ReflectionClass;

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
    
    //Create the class and pass it the supplied arguments.
    //Note: this will throw an exception if the class does not exist.
    $className = 'Tuxion\DoctrineRest\Domain\Result\\'.ucfirst($name).'Result';
    $class = new ReflectionClass($className);
    return $class->newInstanceArgs($arguments);
    
  }
  
}