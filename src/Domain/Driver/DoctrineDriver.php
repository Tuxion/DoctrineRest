<?php namespace Tuxion\DoctrineRest\Domain\Driver;

use \Exception;
use Doctrine\ORM\EntityManager;
use Tuxion\DoctrineRest\Domain\Result\ErrorResult;
use Tuxion\DoctrineRest\Domain\AssignableEntityInterface;

class DoctrineDriver extends AbstractDriver
{
  
  protected $manager;
  
  public function __construct(EntityManager $manager)
  {
    $this->manager = $manager;
  }
  
  public function create($model, $data)
  {
    
    try {
      
      $object = new $model();
      
      if(!$object instanceof AssignableEntityInterface)
        throw new Exception("Models must implement AssignableEntityInterface. '$model' does not.");
      
      $object->fromArray($data);
      
      $this->manager->persist($object);
      $this->manager->flush();
      
      return $this->resultFactory->created(array($object));
      
    }
    
    catch(Exception $ex){
      return $this->handleException($ex, $data);
    }
    
  }
  
  public function replace($model, $id, $data)
  {
    
    try {
      
      $object = $this->manager->find($model, $id);
      
      if(!isset($object)){
        return $this->resultFactory->notFound(array('id'=>$id));
      }
      
      if(!$object instanceof AssignableEntityInterface)
        throw new Exception("Models must implement AssignableEntityInterface. '$model' does not.");
      
      $object->fromArray($data);
      
      $this->manager->persist($object);
      $this->manager->flush();
      
      return $this->resultFactory->replaced(array($object));
      
    }
    
    catch(Exception $ex){
      return $this->handleException($ex, array('id'=>$id));
    }
    
  }
  
  public function read($model, $id)
  {
    
    try {
      
      $object = $this->manager->find($model, $id);
      
      if(!isset($object)){
        return $this->resultFactory->notFound(array('id'=>$id));
      }
      
      return $this->resultFactory->found(array($object));
      
    }
    
    catch(Exception $ex){
      return $this->handleException($ex, array('id'=>$id));
    }
    
  }
  
  public function delete($model, $id)
  {
    
    try {
      
      $object = $this->manager->find($model, $id);
      
      if(!isset($object)){
        return $this->resultFactory->notFound(array('id'=>$id));
      }
      
      $this->manager->remove($object);
      $this->manager->flush();
      
      return $this->resultFactory->deleted(array());
      
    }
    
    catch(Exception $ex){
      return $this->handleException($ex, array('id'=>$id));
    }
    
  }
  
  protected function checkConnection()
  {
    
    //If the connection is closed.
    if(!$this->manager->isOpen())
    {
      
      //Create a new manager based on previous settings.
      $this->manager = $this->manager->create(
        $this->manager->getConnection(),
        $this->manager->getConfiguration()
      );
      
    }
    
  }
  
  protected function handleException(Exception $ex, array $params)
  {
    $error = $this->resultFactory->error($params, $ex);
    $this->checkConnection();
    return $error;
  }
  
}