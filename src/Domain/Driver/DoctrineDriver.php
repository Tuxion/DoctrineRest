<?php namespace Tuxion\DoctrineRest\Domain\Driver;

use \Exception;
use Doctrine\ORM\EntityManager;
use Tuxion\DoctrineRest\Domain\AssignableEntityInterface;

/**
 * The Doctrine2 driver implementation that can handle the domain actions required for standard CRUD operations.
 */
class DoctrineDriver extends AbstractDriver
{
  
  /**
   * The Doctrine2 EntityManager.
   * @var EntityManager
   */
  protected $manager;
  
  /**
   * Returns the Doctrine2 EntityManager.
   * @return EntityManager
   */
  public function getManager(){
    return $this->manager;
  }
  
  /**
   * Creates a new instance of DoctrineDriver.
   * @param EntityManager $manager The Doctrine2 EntityManager.
   */
  public function __construct(EntityManager $manager)
  {
    $this->manager = $manager;
  }
  
  /**
   * Creates a new instance of $model with $data as it's contents and persists it to the database.
   * @param  string $model The class name of the model to operate on.
   * @param  array  $data  The data to insert into the model.
   * @return ResultInterface The result of the operation.
   */
  public function create($model, $data)
  {
    
    try {
      
      //Create a new object to populate with data.
      $object = new $model();
      
      //Using the AssignableEntityInterface to populate, so verify the model implements this.
      if(!$object instanceof AssignableEntityInterface)
        throw new Exception("Models must implement AssignableEntityInterface. '$model' does not.");
      
      //Populate the data.
      $object->fromArray($data);
      
      //Persist to the database (immediately flushing).
      $this->manager->persist($object);
      $this->manager->flush();
      
      //Report a CreatedResult.
      return $this->resultFactory->created($object);
      
    }
    
    //Default exception handler.
    catch(Exception $ex){
      return $this->handleException($ex, array('data'=>$data));
    }
    
  }
  
  /**
   * Replaces an existing instance of $model with $data as it's contents and persists it to the database.
   * @param  string $model The class name of the model to operate on.
   * @param  array  $id    The primary key of the model to replace.
   * @param  array  $data  The data to insert into the model.
   * @return ResultInterface The result of the operation.
   */
  public function replace($model, $id, $data)
  {
    
    try {
      
      //Locate the existing model data and have Doctrine populate it for us.
      $object = $this->manager->find($model, $id);
      
      //Report NotFoundResult if this ID does not match in the database.
      if(!isset($object)){
        return $this->resultFactory->notFound(array('id'=>$id));
      }
      
      //Using the AssignableEntityInterface to populate, so verify the model implements this.
      if(!$object instanceof AssignableEntityInterface)
        throw new Exception("Models must implement AssignableEntityInterface. '$model' does not.");
      
      //Populate the data.
      $object->fromArray($data);
      
      //Persist to the database (immediately flushing).
      $this->manager->persist($object);
      $this->manager->flush();
      
      //Report a ReplacedResult.
      return $this->resultFactory->replaced($object);
      
    }
    
    //Default exception handler.
    catch(Exception $ex){
      return $this->handleException($ex, array('id'=>$id, 'data'=>$data));
    }
    
  }
  
  /**
   * Reads and returns an existing instance of $model.
   * @param  string $model The class name of the model to operate on.
   * @param  array? $id    The primary key of the model to read. If NULL reads all items.
   * @return ResultInterface The result of the operation.
   */
  public function read($model, $id=null)
  {
    
    try {
      
      //Find all items?
      if(is_null($id)){
        
        //Do this using the entity repository.
        $repository = $this->manager->getRepository($model);
        $resultSet = $repository->findAll();
        
        //Wrap the output in a FoundResult.
        return $this->resultFactory->found($resultSet);
        
      }
      
      //Must be positive integer.
      $cleanId = intval($id);
      if($cleanId <= 0)
        throw new Exception('Invalid ID "'.$id.'" should be a positive integer.');
      
      //Locate the existing model data and have Doctrine populate it for us.
      $object = $this->manager->find($model, $cleanId);
      
      //Report NotFoundResult if this ID does not match in the database.
      if(!isset($object)){
        return $this->resultFactory->notFound(array('id'=>$id));
      }
      
      //Report a FoundResult.
      return $this->resultFactory->found($object);
      
    }
    
    //Default exception handler.
    catch(Exception $ex){
      return $this->handleException($ex, array('id'=>$id));
    }
    
  }
  
  /**
   * Deletes an existing instance of $model.
   * @param  string $model The class name of the model to operate on.
   * @param  array  $id    The primary key of the model to delete.
   * @return ResultInterface The result of the operation.
   */
  public function delete($model, $id)
  {
    
    try {
      
      //Locate the existing model data and have Doctrine populate it for us.
      $object = $this->manager->find($model, $id);
      
      //Report NotFoundResult if this ID does not match in the database.
      if(!isset($object)){
        return $this->resultFactory->notFound(array('id'=>$id));
      }
      
      //Remove the object and immediately persist this change to the database.
      $this->manager->remove($object);
      $this->manager->flush();
      
      //Report a DeletedResult.
      return $this->resultFactory->deleted(null);
      
    }
    
    //Default exception handler.
    catch(Exception $ex){
      return $this->handleException($ex, array('id'=>$id));
    }
    
  }
  
  /**
   * A helper method that re-opens the database connection if an error has occurred.
   * @return void
   */
  protected function checkConnection()
  {
    
    //If the connection is closed.
    if(!$this->manager->isOpen())
    {
      
      //Create a new manager based on the previous settings.
      $this->manager = $this->manager->create(
        $this->manager->getConnection(),
        $this->manager->getConfiguration()
      );
      
    }
    
  }
  
  /**
   * A helper method that converts an Exception into an ErrorResult and re-opens the database connection.
   * @param  Exception $ex     The Exception that should be turned into an ErrorResult.
   * @param  mixed     $params An array of parameters provided to the driver that led to the exception.
   * @return ErrorResult
   */
  protected function handleException(Exception $ex, array $params)
  {
    $error = $this->resultFactory->error((array)$params, $ex);
    $this->checkConnection();
    return $error;
  }
  
}