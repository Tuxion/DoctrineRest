<?php namespace Tuxion\DoctrineRest\Domain\Result;

use \Exception;

class ErrorResult extends AbstractResult
{
  
  protected $exception;
  
  public function getException(){
    return $this->exception;
  }
  
  public function setException(Exception $value){
    $this->exception = $value;
  }
  
  public function getBody()
  {
    
    if(isset($this->exception)){
      $errorClass = get_class($this->exception);
      $parts = explode('\\', $errorClass);
      $error = array_pop($parts);
      $message = $this->exception->getMessage();
    }
    
    else{
      $error = "UnknownError";
      $message = "An unknown server error occurred.";
    }
    
    return array(
      'error' => $error,
      'message' => $message,
      'params' => $this->body
    );
    
  }
  
}
