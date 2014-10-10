<?php namespace Tuxion\DoctrineRest\Domain\Composite;

/**
 * A factory for new CompositeCall instances.
 */
class CompositeCallFactory
{
  
  /**
   * Creates a new CompositeCall instance.
   * @return CompositeCall
   */
  public function __invoke()
  {
    return new CompositeCall();
  }
  
}