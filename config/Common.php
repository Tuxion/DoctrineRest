<?php namespace Tuxion\DoctrineRest\_Config;

use Aura\Di\Config;
use Aura\Di\Container;

class Common extends Config
{
  
  public function define(Container $di)
  {
    
    $di->params['Tuxion\DoctrineRest\Action\ActionFactory'] = array(
      'request' => $di->lazyGet('aura/web-kernel:request'),
      'driver' => $di->lazyNew('Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver'),
      'responder' => $di->lazyNew('Tuxion\DoctrineRest\Responder\RestResponder')
    );
    
    $di->params['Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver'] = array(
      'manager' => $di->lazyGet('doctrine/orm:entity-manager')
    );
    
    $di->params['Tuxion\DoctrineRest\Responder\RestResponder'] = array(
      'response' => $di->lazyGet('aura/web-kernel:response'),
      'statusCodes' => $di->lazyNew('Tuxion\DoctrineRest\Responder\StatusCodes')
    );
    
  }
  
  public function modify(Container $di)
  {
    
  }
  
}
