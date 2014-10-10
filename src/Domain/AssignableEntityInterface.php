<?php namespace Tuxion\DoctrineRest\Domain;

/**
 * Implementing classes are entities that can be assigned new data all at once from an associative array.
 */
interface AssignableEntityInterface
{
  
  /**
   * Assigns new data to the class based on an associative array.
   * @param  array  $value An associative array of values to set on the entity.
   * @return void
   */
  public function fromArray(array $value);
  
}