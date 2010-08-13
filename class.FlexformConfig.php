<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

class FlexformConfig extends tslib_pibase {

 /**
 * Main Class Object
 * @public Object
 */
  public $obj;

  /**
  * Images
  * @public string
  */
  public $images = '';
  
  /**
  * Directory Mode
  * @public string
  */
  public $directory = '';
  
  /**
  * Directory Mode Image Title
  * @public string
  */
  public $dModeImageTitle = '';
  
  /**
  * Directory Mode Image Alt
  * @public string
  */
  public $dModeImageAlt = '';
  
  /**
  * Image Parameter
  * @public string
  */
  public $params = '';
  
  /**
  * Mode Selection
  * @public string
  */
  public $mode = '';
  
  /**
  * Dam Categories by name
  * @public String
  */
  public $modedamcat = '';
  
  /**
  * Get Damcat recursiv
  * @public boolean
  */
  public $recursivedamcat = 0;
  
  /**
  * Script Parameter reflection
  * @public double
  */
  public $reflection = 0.4;
  
  /**
  * Script Parameter autoSetup
  * @public boolean
  */
  public $autoSetup = 0;
  
  /**
  * Script Parameter linkMethod
  * @public string
  */
  public $linkMethod = 'remooz';
  
  /**
  * Script Parameter clickOption
  * @public string
  */
  public $clickOption = 'single';
  
  /**
  * Script Parameter useDynLoader
  * @public boolean
  */
  public $useDynLoader = 0;
  
  /**
  * Script Parameter heightRatio
  * @public double
  */
  public $heightRatio = 0.6;
  
  /**
  * Script Parameter offsetY
  * @public int
  */
  public $offsetY = 0;
  
  /**
  * Script Parameter startIndex
  * @public int
  */
  public $startIndex = 0;
  
  /**
  * Script Parameter interval
  * @public int
  */
  public $interval = 2000;
  
  /**
  * Script Parameter factor
  * @public int
  */
  public $factor = 115;
  
  /**
  * Script Parameter bgColor
  * @public string
  */
  public $bgColor = '\'#000\'';
  
  /**
  * Script Parameter useCaption
  * @public boolean
  */
  public $useCaption = 'false';
  
  /**
  * Script Parameter useResize
  * @public boolean
  */
  public $useResize = 'false';
  
  /**
  * Script Parameter useSlider
  * @public boolean
  */
  public $useSlider = 'false';
  
  /**
  * Script Parameter useWindowResize
  * @public boolean
  */
  public $useWindowResize = 'false';
  
  /**
  * Script Parameter useMouseWheel
  * @public boolean
  */
  public $useMouseWheel = 'false';
  
  /**
  * Script Parameter useKeyInput
  * @public boolean
  */
  public $useKeyInput = 'false';
  
  /**
  * Script Parameter useViewer
  * @public boolean
  */
  public $useViewer = 'false';
  
  /**
  * Script Parameter useAutoPlay
  * @public boolean
  */
  public $useAutoPlay = 'false';
  
  /**
  * Script Parameter useAutoPlayOnStart
  * @public boolean
  */
  public $useAutoPlayOnStart = 0;

  /**
  * Script function parameter onEmptyinit
  * @public string
  */
  public $onEmptyinit = '';
  
  
  /**
  * Check Attribute depends on autosetup
  * @public boolean
  */
  public $isLoaded = False;
  
  /**
  * Configuration (Part of) as Array
  * @public array
  */
  public $configArray = array();
  
  /**
  * ReMooz Script Parameter Overlay opacity
  * @public double
  */
  public $overlayOpacity = 0.8;
  
  /**
  * ReMooz Script Parameter useOverlay
  * @public boolean
  */
  public $useOverlay = 'false';

  /**
  * ReMooz Script Parameter Overlay bgColor
  * @public string
  */
  public $overlayColor = '\'#333\'';


  /**
    * Constructor
    *
    * Sets up the object
    *
    * @param object  class tx_cfamooflow_pi1 object
    * @return [none] none
    */
    public function __construct($obj) {
     
      $this->obj = $obj;
     
      $ffimages = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'defaultmode','sPage1');
      $this->images = (!empty($ffimages)) ? $ffimages : $this->images;
      
      $ffdirectory = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'directorymode','sPage1');
      $this->directory = (!empty($ffdirectory)) ? $ffdirectory : $this->directory;
      
      $ffdModeImageTitle = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'dModeImageTitle','sPage1');
      $this->dModeImageTitle = (!empty($ffdModeImageTitle)) ? $ffdModeImageTitle : $this->dModeImageTitle;
      
      $ffdModeImageAlt = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'dModeImageAlt','sPage1');
      $this->dModeImageAlt = (!empty($ffdModeImageAlt)) ? $ffdModeImageAlt : $this->dModeImageAlt;
      
      $ffparams = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'params','sPage1');
      $this->params = (!empty($ffparams)) ? $ffparams : $this->params;
      
      $ffmode = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'mode','sPage1');
      $this->mode = (!empty($ffmode)) ? $ffmode : $this->mode;
      
      $ffmodedamcat = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'modedamcat','sPage1');
      $this->modedamcat = (!empty($ffmodedamcat)) ? $ffmodedamcat : $this->modedamcat;
      
      $ffrecursivedamcat = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'recursivedamcat','sPage1');
      $this->recursivedamcat =  (!empty($ffrecursivedamcat)) ? $ffrecursivedamcat : $this->recursivedamcat;
      
      $ffreflection = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'reflection','sPage2');
      $this->reflection = (!empty($ffreflection)) ? $ffreflection : $this->reflection;
      array_push($this->configArray,$this->reflection);
    
      $ffautoSetup = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'autoSetup','sPage2');
      $this->autoSetup = (!empty($ffautoSetup)) ? $ffautoSetup : $this->autoSetup;

      $fflinkMethod = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'linkMethod','sPage2');
      $this->linkMethod = (!empty($fflinkMethod)) ? $fflinkMethod : $this->linkMethod;
      
      $ffclickOption = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'clickOption','sPage2');
      $this->clickOption = (!empty($ffclickOption)) ? $ffclickOption : $this->clickOption;

      $ffuseDynLoader = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useDynLoader','sPage1');
      $this->useDynLoader = (!empty($ffuseDynLoader)) ? $ffuseDynLoader : $this->useDynLoader;
      
      $ffheightRatio = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'heightRatio','sPage2');

      $this->heightRatio = (!empty($ffheightRatio)) ? $ffheightRatio : $this->heightRatio;
      array_push($this->configArray,$this->heightRatio);
      
      $ffoffsetY = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'offsetY','sPage2');
      $this->offsetY = (!empty($ffoffsetY)) ? $ffoffsetY : $this->offsetY;
      array_push($this->configArray,$this->offsetY);
      
      $ffstartIndex = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'startIndex','sPage2');
      $this->startIndex = (!empty($ffstartIndex)) ? $ffstartIndex : $this->startIndex;
      array_push($this->configArray,$this->startIndex);

      $ffinterval = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'interval','sPage2');
      $this->interval = (!empty($ffinterval)) ? $ffinterval : $this->interval;
      array_push($this->configArray,$this->interval);
      
      $fffactor = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'factor','sPage2');
      $this->factor = (!empty($fffactor)) ? $fffactor : $this->factor;
      array_push($this->configArray,$this->factor);
     
      $ffbgColor = $this->getBGColor();
      $this->bgColor = (!empty($ffbgColor)) ? $ffbgColor : $this->bgColor;
      array_push($this->configArray,$this->bgColor);
      
      $ffuseCaption = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useCaption','sPage2');
      $this->useCaption = (!empty($ffuseCaption)) ? $this->getBooleanStringFromInt($ffuseCaption) : $this->useCaption;
      array_push($this->configArray,$this->useCaption);
      
      $ffuseResize = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useResize','sPage2');
      $this->useResize = (!empty($ffuseResize)) ? $this->getBooleanStringFromInt($ffuseResize) : $this->useResize;
      array_push($this->configArray,$this->useResize);
      
      $ffuseSlider = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useSlider','sPage2');
      $this->useSlider = (!empty($ffuseSlider)) ? $this->getBooleanStringFromInt($ffuseSlider) : $this->useSlider;
      array_push($this->configArray,$this->useSlider);
      
      $ffuseWindowResize = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useWindowResize','sPage2');
      $this->useWindowResize = (!empty($ffuseWindowResize)) ? $this->getBooleanStringFromInt($ffuseWindowResize) : $this->useWindowResize;
      array_push($this->configArray,$this->useWindowResize);

      $ffuseMouseWheel = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useMouseWheel','sPage2');
      $this->useMouseWheel = (!empty($ffuseMouseWheel)) ? $this->getBooleanStringFromInt($ffuseMouseWheel) : $this->useMouseWheel;
      array_push($this->configArray,$this->useMouseWheel);
      
      $ffuseKeyInput = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useKeyInput','sPage2');
      $this->useKeyInput = (!empty($ffuseKeyInput)) ? $this->getBooleanStringFromInt($ffuseKeyInput) : $this->useKeyInput;
      array_push($this->configArray,$this->useKeyInput);
      
      $ffuseViewer = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useViewer','sPage2');
      $this->useViewer = (!empty($ffuseViewer)) ? $this->getBooleanStringFromInt($ffuseViewer) : $this->useViewer;
      array_push($this->configArray,$this->useViewer);

      $ffuseAutoPlay = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useAutoPlay','sPage2');
      $this->useAutoPlay = (!empty($ffuseAutoPlay)) ? $this->getBooleanStringFromInt($ffuseAutoPlay) : $this->useAutoPlay;
      array_push($this->configArray,$this->useAutoPlay);
      
      $ffuseAutoPlayOnStart = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useAutoPlayOnStart','sPage2');
      $this->useAutoPlayOnStart = (!empty($ffuseAutoPlayOnStart)) ? $ffuseAutoPlayOnStart : $this->useAutoPlayOnStart;

      $ffuseOverlay = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useOverlay','sPage3');
      $this->useOverlay = (!empty($ffuseOverlay)) ? $this->getBooleanStringFromInt($ffuseOverlay) : $this->useOverlay;
      
      $ffoverlayColor = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'overlayColor','sPage3');
      $this->overlayColor = (!empty($ffoverlayColor)) ? $ffoverlayColor : $this->overlayColor;

      $this->onEmptyinit = '\'onEmptyinit\': function() {
        this.loadJSON(\'index.php?eID=tx_cfamooflow_pi1&damcat='.$obj->catArray[0].'\');
       }';
    }
    
    private function getBGColor() {
      $bgColor = $this->obj->pi_getFFvalue($this->obj->cObj->data['pi_flexform'], 'bgColor','sPage2');
      // Fix FF2 behavior with transparent setting
      if (preg_match("/Firefox\/2/i", $_SERVER['HTTP_USER_AGENT']) && $bgColor == 'transparent') {
   	$bgColor = 'rgba(0,0,0,0)';
      }
      return "'$bgColor'";
    }
    
    private function getBooleanStringFromInt($int) {
    	$booleanString = '';
    	if($int == 0) {
    		$booleanString = 'false';
    	} elseif($int == 1) {
    		$booleanString = 'true';
    	}
    	return $booleanString;
    }
    
    public function getConfigArray(){
    	return $this->configArray;
    }
}
?>