<?php namespace Tuxion\DoctrineRest\_Config;

use Aura\Di\_Config\AbstractContainerTest;

class CommonTest extends AbstractContainerTest
{
  
  protected function getConfigClasses()
  {
    return array(
      'Aura\Web\_Config\Common',
      'Tuxion\DoctrineRest\_Config\Test', //This is for including our required stubs.
      'Tuxion\DoctrineRest\_Config\Common',
    );
  }
  
  public function provideGet()
  {
    
    //Do this to silence the skip.
    return array(
      array('doctrine/orm:entity-manager', 'Doctrine\ORM\EntityManager')
    );
    
  }
  
  public function provideNewInstance()
  {
    return array(
      array('Tuxion\DoctrineRest\Action\ActionFactory'),
      array('Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver'),
      array('Tuxion\DoctrineRest\Domain\Result\ResultFactory'),
      array('Tuxion\DoctrineRest\Responder\RestResponder'),
      array('Tuxion\DoctrineRest\Responder\StatusCodes')
    );
  }
  
  public function testInstantiateRouteAttacher()
  {
    $this->di->newInstance('Tuxion\DoctrineRest\RouteAttacher', array(
      'model' => 'Tuxion\DoctrineRest\Domain\Dummy\DummyEntity',
      'resource' => 'integration-dummy'
    ));
  }
  
}