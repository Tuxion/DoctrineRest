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
      $error = basename(get_class($this->exception));
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
