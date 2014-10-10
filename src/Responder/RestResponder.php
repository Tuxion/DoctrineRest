<?php namespace Tuxion\DoctrineRest\Responder;

use Aura\Web\Response;
use Tuxion\DoctrineRest\Domain\Result\ResultInterface;

/**
 * A responder that outputs the results in a (JSON) REST format.
 */
class RestResponder implements ResponderInterface
{
  
  /**
   * The helper class mapping results to HTTP status codes.
   * @var StatusCodes
   */
  protected $statusCodes;
  
  /**
   * The Aura Response object.
   * @var Response
   */
  protected $response;
  
  /**
   * The ResultInterface that holds the final state of the application to communicate to the user.
   * @var ResultInterface
   */
  protected $result;
  
  /**
   * Returns the Aura Response object.
   * @return Response
   */
  public function getResponse(){
    return $this->response;
  }
  
  /**
   * Return the ResultInterface that holds the final state of the application to communicate to the user.
   * @return ResultInterface
   */
  public function getResult(){
    return $this->result;
  }
  
  /**
   * Sets the ResultInterface that holds the final state of the application to communicate to the user.
   * @param ResultInterface $result
   * @return void
   */
  public function setResult(ResultInterface $value){
    $this->result = $value;
  }
  
  /**
   * Creates a new RestResponder instance.
   * @param Response    $response    The Aura Response object.
   * @param StatusCodes $statusCodes The helper class mapping results to HTTP status codes.
   */
  public function __construct(Response $response, StatusCodes $statusCodes)
  {
    $this->response = $response;
    $this->statusCodes = $statusCodes;
  }
  
  /**
   * Sets the response on an Aura Response object and returns it.
   * @return Response
   */
  public function __invoke()
  {
    
    //No result given is a bad thing.
    if(!isset($this->result))
      throw new \Exception('No result was set.');
    
    //Map the status code and set it on the response.
    $code = $this->statusCodes->fromResult($this->result);
    $this->response->status->set($code);
    
    //Get our result body and set it (in JSON format) if it is not empty.
    $body = $this->result->getBody();
    if(!is_null($body)){
      $json = json_encode($this->result->getBody());
      $this->response->content->set($json);
      $this->response->content->setType('application/json');
      $this->response->content->setCharset('utf-8');
    }
    
    //That should conclude our adventures!
    return $this->response;
    
  }
  
}