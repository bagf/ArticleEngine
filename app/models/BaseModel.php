<?php

/**
 * Extending this abstract model will enable snake-case attributes to be
 * referenced in camel-case. It does this by overriding __get __set methods and
 * passing the snake-case key to the parent implementation.
 *
 * @author bagf
 */
abstract class BaseModel extends \Eloquent {
  
  public function __get($name)
  {
    return parent::__get(snake_case($name));
  }
  
  public function __set($name, $value)
  {
    parent::__set(snake_case($name), $value);
  }
  
  /**
   * Converts all attributes to key-value array with all the keys converted to
   * camel-case.
   * 
   * @return array
   */
  public function toCamelArray()
  {
    $camelArray = array();
    foreach ($this->toArray() AS $key => $value) {
      $snakeCaseKey = camel_case($key);
      // Make adjustment for Id
      if (substr($snakeCaseKey, -2, 2) === 'Id') {
        $snakeCaseKey = substr($snakeCaseKey, 0, -2) ."ID";  
      }
      $camelArray[$snakeCaseKey] = $value;
    }
    return $camelArray;
  }
}
