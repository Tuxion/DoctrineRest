<?php namespace Tuxion\DoctrineRest\Responder;

use Aura\Web\Response;
use Tuxion\DoctrineRest\Domain\Result\ResultInterface;

class RestResponder implements ResponderInterface
{
  
  //Check for common errors
  //Output JSON body and/or status codes
  
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
    
    if(!isset($this->result))
      throw new \Exception('No result was set.');
    
    $code = $this->statusCodes->fromResult($this->result);
    $this->response->status->set($code);
    
    $json = json_encode($this->result->getBody());
    $this->response->content->set($json);
    $this->response->content->setType('application/json');
    $this->response->content->setCharset('utf-8');
    
    return $this->response;
    
  }
  
}