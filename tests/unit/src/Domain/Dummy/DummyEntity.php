<?php namespace Tuxion\DoctrineRest\Domain\Dummy;

use Tuxion\DoctrineRest\Domain\AssignableEntityInterface;

/**
 * @Entity @Table(name="dummy_table")
 */
class DummyEntity implements AssignableEntityInterface
{
  
  /** @Id @Column(type="integer") @GeneratedValue **/
  public $id;
  
  /** @Column(type="string") **/
  public $title;
  
  public function getTitle(){
    return $this->title;
  }
  
  public function setTitle($value){
    $this->title = $value;
  }
  
  public function getId(){
    return $this->id;
  }
  
  public function fromArray(array $input)
  {
    foreach($input as $key => $value){
      $setter = 'set'.ucfirst($key);
      if(method_exists($this, $setter)){
        $this->$setter($value);
      }
    }
  }
  
}