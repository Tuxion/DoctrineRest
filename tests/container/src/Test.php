<?php namespace Tuxion\DoctrineRest\_Config;

use Aura\Di\Config;
use Aura\Di\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup as DoctrineSetup;

class Test extends Config
{

  public function define(Container $di)
  {
    
    //Create an entity manager that handles an in-memory sqlite connection.
    $isDevMode = true;
    $config = DoctrineSetup::createAnnotationMetadataConfiguration(
      array(dirname(dirname(__DIR__)).'/unit/Dummy'), $isDevMode
    );
    
    //Get DB connection info for sqlite in-memory temporary databases
    $connection = array(
      'driver'    => 'pdo_sqlite',
      'memory'    => true
    );
    
    //Create the entity manager.
    $di->values['Tuxion/DoctrineRest:entityManager'] = EntityManager::create($connection, $config);
    
    // Use direct instances instead of including the web-kernel. Since that requires PHP 5.4.
    $di->values['Tuxion/DoctrineRest:router'] = $di->newInstance('Aura\Router\Router');
    $di->values['Tuxion/DoctrineRest:request'] = $di->newInstance('Aura\Web\Request');
    $di->values['Tuxion/DoctrineRest:response'] = $di->newInstance('Aura\Web\Response');
    
  }

  public function modify(Container $di)
  {
    
    //Force create schema for DummyEntity and UnassignableEntity.
    $manager = $di->lazyValue('Tuxion/DoctrineRest:entityManager')->__invoke();
    $schemaTool = new SchemaTool($manager);
    $schemaTool->createSchema(array(
      $manager->getClassMetadata('Tuxion\DoctrineRest\Domain\Dummy\DummyEntity')
    ));
    
  }
  
}
