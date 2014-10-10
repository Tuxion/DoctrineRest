<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \Exception;

/**
 * Represents an error result in the domain.
 * The exact meaning of the result can be derived from the provided exception.
 */
class ErrorResult extends AbstractResult
{
  
  /**
   * The native exception that this error represents.
   * @var Exception?
   */
  protected $exception;
  
  /**
   * Returns the native exception that this error represents.
   * @return Exception?
   */
  public function getException(){
    return $this->exception;
  }
  
  /**
   * Creates a new instance of ErrorResult.
   * @param mixed      $body      The body of this result.
   * @param Exception? $exception (optional) The native exception that this error represents. Default is NULL.
   */
  public function __construct($body, Exception $exception = null)
  {
    parent::__construct($body);
    $this->exception = $exception;
  }
  
  /**
   * Custom implementation of the body.
   * This will give a clear description of the top level Exception, without a stack trace or nested Exceptions.
   * @return array
   */
  public function getBody()
  {
    
    //When an exception is provided, retrieve the data we need from it.
    if(isset($this->exception)){
      
      //The base class name.
      $errorClass = get_class($this->exception);
      $parts = explode('\\', $errorClass);
      $error = array_pop($parts);
      
      //And it's message.
      $message = $this->exception->getMessage();
      
    }
    
    //As a fallback, provide this generic error message.
    else{
      $error = "UnknownError";
      $message = "An unknown server error occurred.";
    }
    
    //Format these variables into an associative array.
    return array(
      'error' => $error,
      'message' => $message,
      'params' => $this->body
    );
    
  }
  
}
