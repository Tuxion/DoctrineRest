<?php namespace Tuxion\DoctrineRest\Domain\Driver;

/**
 * A driver that can handle the domain actions required for standard CRUD operations.
 */
interface DriverInterface
{
  
  /**
   * Creates a new instance of $model with $data as it's contents and persists it to the database.
   * @param  string $model The class name of the model to operate on.
   * @param  array  $data  The data to insert into the model.
   * @return ResultInterface The result of the operation.
   */
  public function create($model, $data);
  
  /**
   * Replaces an existing instance of $model with $data as it's contents and persists it to the database.
   * @param  string $model The class name of the model to operate on.
   * @param  mixed  $id    The primary key of the model to replace.
   * @param  array  $data  The data to insert into the model.
   * @return ResultInterface The result of the operation.
   */
  public function replace($model, $id, $data);
  
  /**
   * Reads and returns an existing instance of $model.
   * @param  string $model The class name of the model to operate on.
   * @param  mixed  $id    The primary key of the model to read.
   * @return ResultInterface The result of the operation.
   */
  public function read($model, $id);
  
  /**
   * Reads and returns all instances of $model.
   * @param  string $model   The class name of the model to operate on.
   * @param  array  $options Filters and other options for this reading operation.
   *                         See implementing class for more details.
   * @return ResultInterface The result of the operation.
   */
  public function readAll($model, $options=array());
  
  /**
   * Deletes an existing instance of $model.
   * @param  string $model The class name of the model to operate on.
   * @param  mixed  $id    The primary key of the model to delete.
   * @return ResultInterface The result of the operation.
   */
  public function delete($model, $id);
  
}
