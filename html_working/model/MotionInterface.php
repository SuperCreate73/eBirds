<?php

class MotionInterface extends ModelInterface
{
	// Accepte une liste de settings en entrée et un objet DbMngSettings.
  // Ce dernier est initialisé avec la liste de tous les settings et la liste des
  // alias issues de la DB. Un nouveau record dans la db engendrera automatiquement
  // une nouvelle propriété de l'objet.
  //
  // Le nom des propriétés correspond aux settings de Motion.
  private $motionManager;
  // public $allMotionArray = [];

  public function __construct($inputSettings, $allSettingsArray)
  // filtre la liste de paramètres en entrée et lance l'hydratation des
  // variables
  {
    $this->motionManager = new MotionManager;
    $settingsAliasInterface = new SettingsAliasInterface;
    $output = shell_exec('echo "MotionInterface - inputSetting : '. json_encode($inputSettings) .'" >> /var/www/debug.log');
    $output = shell_exec('echo "MotionInterface - allSettingsArray : '. json_encode($allSettingsArray) .'" >> /var/www/debug.log');

    foreach ($allSettingsArray as $key => $row)
    {
      $output = shell_exec('echo "MotionInterface - allSettingsArray1 : '. $key .'" >> /var/www/debug.log');
      $output = shell_exec('echo "MotionInterface - allSettingsArray2 : '. json_encode($inputSettings) .'" >> /var/www/debug.log');
      // if current setting is an alias and not null
      // if  (array_key_exists($key, $settingsAliasInterface->allAliasArray) &&
      //     (isset($inputSettings[$key]) && ! $inputSettings[$key] == ""))
      if  (array_key_exists($key, $settingsAliasInterface->allAliasArray))
      {
        $output = shell_exec('echo "MotionInterface - isAlias : '. $key .'" >> /var/www/debug.log');
        // get the values of settings from alias
        $aliasList = $settingsAliasInterface->allAliasArray[$key][$row[0]];
        foreach ($aliasList as $keyAlias => $settingAlias)
        {
          $this -> setHydrate($keyAlias, $settingAlias);
        }
      }
      elseif (array_key_exists($key, $inputSettings))
      // current setting is not alias
      {
        $output = shell_exec('echo "MotionInterface - isnotAlias : '. $key .'" >> /var/www/debug.log');
        $this -> setHydrate($key, $inputSettings[$key]);
      }
    }
    $output = shell_exec('echo "MotionInterface : '. json_encode($this->_properties) .'" >> /var/www/debug.log');
  }


  protected function setThreshold($value)
  // specific setter for threshold
  {
    // depends on image size in px already set (higher priority)
    $this -> setGeneral('threshold', strval(intval(floatval($this -> width) * floatval($this -> height) * floatval($value) / 100)));
  }


  protected function setSnapshotInterval($value)
  // specific setter for threshold
  {
    // depends on image size in px already set (higher priority)
    $this -> setGeneral('snapshot_interval', $value);
    $this -> setGeneral('ffmpeg_timelapse', $value);
  }

  public function updateMotion()
  {
    $this->motionManager -> backUpMotion();
    // update settings in config file (motion.conf)
    $output = shell_exec('echo "MotionInterface : '. json_encode($this->_properties) .'" >> /var/www/debug.log');
    $this->motionManager -> setAllSettings($this->_properties);
    // restart motion daemon for applying settings
    $this->motionManager -> restartMotion();
  }
}
