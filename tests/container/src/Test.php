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
    
    //Fake the web-kernel, because this requires php 5.4 currently. And we want to unit test for 5.3.
    $di->set('aura/web-kernel:request', $di->lazyNew('Aura\Web\Request'));
    $di->set('aura/web-kernel:response', $di->lazyNew('Aura\Web\Response'));
    
    //Create an entity manager that handles an in-memory sqlite connection.
    $di->set('doctrine/orm:entity-manager', $di->lazy(function(){
      
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
      return EntityManager::create($connection, $config);
      
    }));
    
  }

  public function modify(Container $di)
  {
    
  }
  
}
