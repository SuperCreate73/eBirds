<?php

abstract class ModelInterface
{
  // Abstract class to make Interface class between views, DB and other output4
  // files.
  // The goal is to have all variables stored as properties in the Interface class
  //

  // getters
  //###################################
  public function getAllSettings()
  // return an array of properties from object
  // [motionSettingName = valeur,...]
  {
    return get_object_vars($this);
  }


  // setters
  //###################################
  protected function setHydrate($key, $value)
  // general procedure to set properties values for object
  {
    // setter name generation from setting name ($key) -> capitalize first letter
    $method = 'set'.ucfirst($key);

    // if specific setter exist
    if (method_exists($this, $method))
    {
      $this->$method($value);
    }
    // else use general setter
    else
    {
      $this->setGeneral($key, $value);
    }
  }


  protected function setGeneral($key, $value)
  // general setter - create a property with MotionSettingsName ($key) as name
  // and $value as value
  {
    $this -> $key = $value;
  }
}
