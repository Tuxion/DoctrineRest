<?php namespace Tuxion\DoctrineRest\Responder;

use Aura\Web\Response;
use Tuxion\DoctrineRest\Domain\Result\ResultInterface;

class DummyResponder implements ResponderInterface
{
  
  protected $statusCodes;
  protected $response;
  protected $result;
  
  public function getResponse(){
    return $this->response;
  }
  
  public function getResult(){
    return $this->result;
  }
  
  public function setResult(ResultInterface $value){
    $this->result = $value;
  }
  
  public function __construct(Response $response, StatusCodes $statusCodes)
  {
    $this->response = $response;
    $this->statusCodes = $statusCodes;
  }
  
  public function __invoke()
  {
    
    //Do nothing really.
    return $this->response;
    
  }
  
}