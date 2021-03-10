<?php

class MotionInterface extends ModelInterface  {
	// interface between view and motionManager

  private $motionManager;

  public function __construct($inputSettings, $allSettingsArray)  {
  // filtre la liste de paramètres en entrée et lance l'hydratation des
  // variables

    // variable initialisation
    $this->motionManager = new MotionManager;
    $settingsAliasInterface = new SettingsAliasInterface;

    foreach ($allSettingsArray as $key => $row)  {

      // filter settings from alias
      if  (array_key_exists($key, $settingsAliasInterface->allAliasArray)) {
        // get the values of settings from alias
        $aliasList = $settingsAliasInterface->allAliasArray[$key][$row[0]];

        foreach ($aliasList as $keyAlias => $settingAlias)  {
          // alias values initialisation
          $this -> setHydrate($keyAlias, $settingAlias);
        }
      }

      // current setting is not alias
      elseif (array_key_exists($key, $inputSettings))  {
        // normal setting initialisation
        $this -> setHydrate($key, $inputSettings[$key]);
      }
    }
  }


  protected function setThreshold($value)  {
  // specific setter for threshold

    // depends on image size in px already set (higher priority)
    $this -> setGeneral('threshold',
                        strval(intval(floatval($this -> width) * floatval($this -> height) * floatval($value) / 100)));
  }


  protected function setSnapshotInterval($value)  {
  // specific setter for update snapshot_interval and ffmpeg_timelapse

    $this -> setGeneral('snapshot_interval', $value);
    $this -> setGeneral('ffmpeg_timelapse', $value);
  }


  public function updateMotion()  {
  // apply settings to motion

    // backup current settings
    $this->motionManager -> backUpMotion();
    // update settings in config file (motion.conf)
    $this->motionManager -> setAllSettings($this->_properties);
    // restart motion daemon for applying settings
    $this->motionManager -> restartMotion();
  }

}
