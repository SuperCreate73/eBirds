<?php

class MotionInterface extends ModelInterface
{
	// Accepte une liste de settings en entrée et un objet DbMngSettings.
  // Ce dernier est initialisé avec la liste de tous les settings et la liste des
  // alias issues de la DB. Un nouveau record dans la db engendrera automatiquement
  // une nouvelle propriété de l'objet.
  //
  // Le nom des propriétés correspond aux settings de Motion.


  public function __construct($inputSettings, $dbMngSettings)
  // filtre la liste de paramètres en entrée et lance l'hydratation des
  // variables
  {
    foreach ($dbMngSettings -> allSettingsArray as $key => $setting)
    {
      if (in_array($key, $dbMngSettings -> aliasArray) && isset($inputSettings[$key]))
      // current setting is alias
      {
        // get the values of settings from alias
        $aliasList = $dbMngSettings -> getSettingFromAlias($key, $inputSettings[$key]);
        foreach ($aliasList as $keyAlias => $settingAlias)
        {
          $this -> setHydrate($keyAlias, $settingAlias);
        }
      }
      elseif (array_key_exists($key, $inputSettings))
      // current setting is not alias
      {
        $this -> setHydrate($key, $inputSettings[$key]);
      }
    }
  }
  

  protected function setThreshold($value)
  // specific setter for threshold
  {
    // depends on image size in px already set (higher priority)
    $this -> threshold = strval(intval(floatval($this -> width) * floatval($this -> height) * floatval($value) / 100));
  }


  protected function setSnapshotInterval($value)
  // specific setter for threshold
  {
    // depends on image size in px already set (higher priority)
    $this -> setGeneral('snapshot_interval', $value);
    $this -> setGeneral('ffmpeg_timelapse', $value);
  }
}
