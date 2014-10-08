<?php namespace Tuxion\DoctrineRest\_Config;

use Aura\Di\Config;
use Aura\Di\Container;

class Common extends Config
{
  
  public function define(Container $di)
  {
    
    $di->params['Tuxion\DoctrineRest\Action\Environment'] = array(
      'request' => $di->lazyGet('aura/web-kernel:request'),
      'driver' => $di->lazyNew('Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver'),
      'responder' => $di->lazyNew('Tuxion\DoctrineRest\Responder\RestResponder')
    );
    
    $di->params['Tuxion\DoctrineRest\Action\ActionFactory'] = array(
      'environment' => $di->lazyNew('Tuxion\DoctrineRest\Action\Environment')
    );
    
    $di->params['Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver'] = array(
      'manager' => $di->lazyValue('Tuxion/DoctrineRest:entityManager')
    );
    
    $di->setters['Tuxion\DoctrineRest\Domain\Driver\AbstractDriver'] = array(
      'setResultFactory' => $di->lazyNew('Tuxion\DoctrineRest\Domain\Result\ResultFactory')
    );
    
    $di->params['Tuxion\DoctrineRest\Responder\RestResponder'] = array(
      'response' => $di->lazyGet('aura/web-kernel:response'),
      'statusCodes' => $di->lazyNew('Tuxion\DoctrineRest\Responder\StatusCodes')
    );
    
    $di->params['Tuxion\DoctrineRest\Mapper\ResourceFactory'] = array(
      'actionFactory' => $di->lazyNew('Tuxion\DoctrineRest\Action\ActionFactory'),
      'compositeCallFactory' => $di->lazyNew('Tuxion\DoctrineRest\Domain\Composite\CompositeCallFactory')
    );
    
    $di->params['Tuxion\DoctrineRest\Mapper\ResourceMapper'] = array(
      'resourceFactory' => $di->lazyNew('Tuxion\DoctrineRest\Mapper\ResourceFactory'),
      'routers' => $di->lazyGet('aura/web-kernel:router'),
      'routePrefix' => $di->lazyValue('Tuxion/DoctrineRest:routePrefix')
    );
      
  }
  
  public function modify(Container $di)
  {
    
  }
  
}
