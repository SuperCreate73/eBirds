<?php

require_once("model/DbManager.php");

class MotionInterface extends DbManager {
	// Accepte une liste des settings en entrée, filtre les settings concernés
  // et donne une liste correspondante des options de Motion :
  //  - Traduction des alias
  //  - Traduction des options génériques
  //      (threshold, ffmpeg none, ...)
  //  - Construction d'une liste en sortie directement interprétable par
  //    MotionManager

  // Inputsettings :
  //    imageSize - low, medium or high
  //    on_motion_detected - e-mail
  //    threshold - % to transform in Pixel Amount
  //    quality - image compression
  //    ffmpeg_timelapse - interval between photos
  //    ffmpeg_timelapse_mode - none, daily, weekly, ...
  //
  // To transform settings :
  //    imageSize -> width and height
  //    threshold -> % to pixels
  //    ffmpeg_timelapse_mode -> none to ffmpeg_timelapse = 0
  //

  private $width;
	private $heigth;
	private $on_motion_detected;
	private $threshold;
  private $quality;
  private $ffmpeg_timelapse;
  private $ffmpeg_timelapse_mode;

  // private $imageTypeDetection;
  // private $imageTypeInterval;

//  private $acceptedList=array();
  public function __construct($inputSettings) {
    // filtre la liste de paramètres en entrée et lance l'hydratation des
    // variables
    // $this->acceptedList = array('on_motion_detected',
    //   'imageSize',
    //   'threshold',
    //   'quality',
    //   'ffmpeg_timelapse',
    //   'ffmpeg_timelapse_mode' );
    // $filteredArray = array_filter($inputSettings, function($key) {
    //     $this->filterArray($key, $this->acceptedList, false);
    //   }, ARRAY_FILTER_USE_KEY);

    $this->hydrate($inputSettings);
  }

  // getters
  //###################################
  public function getAllSettings()
  {
    return get_object_vars($this);
  }

  // setters
  //###################################
  public function hydrate(array $inputSettings)
  {

    $motionOnlyArray = array_filter($inputSettings, function($key) {
        $acceptedList = array('on_motion_detected',
                              'imageSize',
                              'imageTypeDetection',
                              'imageTypeInterval',
                              'threshold',
                              'quality',
                              'ffmpeg_timelapse',
                              'ffmpeg_timelapse_mode');
                              // 'snapshots_interval',
                              // 'snapshot_on_off',
                              // 'output_pictures',
                              // 'ffmpeg_output_movies' );
        $this->filterArray($key, $acceptedList, false);
      }, ARRAY_FILTER_USE_KEY);


  // array of settings that must be updated at the end


    // parameters to set, no order specific
    $filteredArray = array_filter($motionOnlyArray, function($key) {
        $endSettingsList = array('threshold', 'ffmpeg_timelapse_mode');
        $this->filterArray($key, $endSettingsList, true);
      }, ARRAY_FILTER_USE_KEY);

    $this->setHydrate($filteredArray);

    // parameters to set at the end
    $filteredArray = array_filter($motionOnlyArray, function($key) {
        $endSettingsList = array('threshold', 'ffmpeg_timelapse_mode');
        $this->filterArray($key, $endSettingsList, false);
      }, ARRAY_FILTER_USE_KEY);
    $this->setHydrate($filteredArray);
  }

  private function setHydrate($inputList)
  {
    // general procedure to set values
    foreach ($inputList as $key => $value)
    {
      // setter name generation from setting name ($key)
      $method = 'set'.ucfirst($key);

      // if specific setter exist
      if (method_exists($this, $method))
      {
        $this->$method($value);
      }
      // general setter
      else
      {
        $this->setGeneral($key, $value);
      }
    }
  }

  private function setGeneral($key, $value)
  {
    // general setter
    $this->$key = $value;
  }


  private function setImageSize($value)
  {
    // alias for 'width' and 'eight'
    $settingsList = $this->getSettingFromAlias('imageSize', $value)();
    $this->setHydrate($settingsList);
  }

  private function setThreshold($value)
  {
    // depends on image size in px
    $this->threshold = strval(intval(floatval($this->width) * floatval($this->heigth) * floatval($value) / 100));
  }

  private function setFfmpeg_timelapse_mode($value)
  {
    // special case 'none' value
    if ( $value == 'none') {
      $this->setGeneral('ffmpeg_timelapse', 0);
    }
    else {
      $this->setGeneral('ffmpeg_timelapse_mode', $value);
    }
  }

  // private functions
  //###################################
  private function filterArray($key, $array, $invert)
  {
    if ($invert)
    {
      return (!in_array($key, $array)) ;
    }
    else
    {
      return (in_array($key, $array)) ;
    }
  }

  private function getSettingFromAlias ($alias, $value)
  {
    // Get all records from $configAlias
    // where alias = $alias and aliasValue = $value
    //
    $this->_table = 'configAlias';
    $db = $this->dbConnect();
    $sql = "SELECT setting, settingValue
            FROM configAlias
            WHERE alias = '".$alias."' AND aliasValue = '".$value."' ;";
    $stmt = $db->query($sql);
    $list = $stmt->fetchall(PDO::FETCH_KEY_PAIR);

    return($list);
  }





//	}
// ##################################################################
// Don't now if needed ?
// ##################################################################
	public function getAliasValue ($alias) {
		// Return current value from alias
		$db = $this->dbConnect();
		$sql = "SELECT aliasValue FROM configAlias
						INNER JOIN config
						ON (configAlias.setting = config.setting
							AND configAlias.settingValue = config.value)
						WHERE configAlias.alias = '".$alias."'
						LIMIT 1 ;";
		$stmt = $db->query($sql);
		$list = $stmt->fetchall();
		$list = array_shift($list);

		return($list['aliasValue']);
	}

}
