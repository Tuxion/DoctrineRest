<?php namespace Tuxion\DoctrineRest\Responder;

use Aura\Web\Response;
use Tuxion\DoctrineRest\Domain\Result\ResultInterface;

interface ResponderInterface
{
  
  public function getResult();
  public function getResponse();
  public function setResult(ResultInterface $result);
  public function __construct(Response $response, StatusCodes $statusCodes);
  public function __invoke();
  
}