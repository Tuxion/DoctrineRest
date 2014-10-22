<?php namespace Tuxion\DoctrineRest\Domain\Dummy;

/**
 * @Entity @Table(name="unassignable_table")
 */
class UnassignableEntity
{
  
  /** @Id @Column(type="integer") @GeneratedValue **/
  protected $id;
  
  /** @Column(type="string") **/
  protected $title;
  
  public function getTitle(){
    return $this->title;
  }
  
  public function setTitle($value){
    $this->title = $value;
  }
  
  public function getId(){
    return $this->id;
  }
  
}