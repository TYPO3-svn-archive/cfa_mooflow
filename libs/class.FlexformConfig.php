<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

class FlexformConfig extends tslib_pibase {

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
  * Damcat Mode
  * @public boolean
  */
  public $modedamcat = false;
  
  /**
  * Get Damcat recursiv
  * @public boolean
  */
  public $recursivedamcat = '';
  
  /**
  * Script Parameter reflection
  * @public double
  */
  public $reflection = 0.4;
  
  /**
  * Script Parameter autoSetup
  * @public boolean
  */
  public $autoSetup = false;
  
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
  public $useDynLoader = false;
  
  /**
  * Script Parameter heightRation
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
  public $bgColor = '\'transparent\'';
  
  /**
  * Script Parameter useCaption
  * @public boolean
  */
  public $useCaption = true;
  
  /**
  * Script Parameter useResize
  * @public boolean
  */
  public $useResize = false;
  
  /**
  * Script Parameter useSlider
  * @public boolean
  */
  public $useSlider = false;
  
  /**
  * Script Parameter useWindowResize
  * @public boolean
  */
  public $useWindowResize = false;
  
  /**
  * Script Parameter useMouseWheel
  * @public boolean
  */
  public $useMouseWheel = true;
  
  /**
  * Script Parameter useKeyInput
  * @public boolean
  */
  public $useKeyInput = false;
  
  /**
  * Script Parameter useViewer
  * @public boolean
  */
  public $useViewer = false;
  
  /**
  * Script Parameter useAutoPlay
  * @public boolean
  */
  public $useAutoPlay = false;
  
  /**
  * Script Parameter useAutoPlayOnStart
  * @public boolean
  */
  public $useAutoPlayOnStart = false;

  /**
  * Script function parameter onEmptyinit
  * @public string
  */
  public $onEmptyinit = '';

  /**
    * Constructor
    *
    * Sets up the object
    *
    * @param object  class tx_cfamooflow_pi1 object
    * @return [none] none
    */
    public function __construct($obj) {
     
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
      $this->recursivedamcat = (!empty($ffrecursivedamcat)) ? $ffrecursivedamcat : $this->recursivedamcat;
      
      $ffreflection = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'reflection','sPage2');
      $this->reflection = (!empty($ffreflection)) ? $ffreflection : $this->reflection;
    
      $ffautoSetup = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'autoSetup','sPage2');
      $this->autoSetup = (!empty($ffautoSetup)) ? $ffautoSetup : $this->autoSetup;
      
      $fflinkMethod = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'linkMethod','sPage2');
      $this->linkMethod = (!empty($fflinkMethod)) ? $fflinkMethod : $this->linkMethod;
      
      $ffclickOption = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'clickOption','sPage2');
      $this->clickOption = (!empty($ffclickOption)) ? $ffclickOption : $this->clickOption;

      $ffuseDynLoader = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useDynLoader','sPage1');
      $this->useDynLoader = (!empty($ffuseDynLoader)) ? $ffuseDynLoader : $this->useDynLoader;
      
      $ffheightRatio = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useDynLoader','sPage1');
      $this->heightRatio = (!empty($ffheightRatio)) ? $ffheightRatio : $this->heightRatio;
      
      $ffoffsetY = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'offsetY','sPage2');
      $this->offsetY = (!empty($ffoffsetY)) ? $ffoffsetY : $this->offsetY;
      
      $ffstartIndex = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'startIndex','sPage2');
      $this->startIndex = (!empty($ffstartIndex)) ? $ffstartIndex : $this->startIndex;

      $ffinterval = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'interval','sPage2');
      $this->interval = (!empty($ffinterval)) ? $ffinterval : $this->interval;
      
      $fffactor = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'factor','sPage2');
      $this->factor = (!empty($fffactor)) ? $fffactor : $this->factor;
     
      $ffbgColor = $this->getBGColor();
      $this->bgColor = (!empty($ffbgColor)) ? $ffbgColor : $this->bgColor;
      
      $ffuseCaption = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useCaption','sPage2');
      $this->useCaption = (!empty($ffuseCaption)) ? $ffuseCaption : $this->useCaption;
      
      $ffuseResize = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useResize','sPage2');
      $this->useResize = (!empty($ffuseResize)) ? $ffuseResize : $this->useResize;
      
      $ffuseResize = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useResize','sPage2');
      $this->useResize = (!empty($ffuseResize)) ? $ffuseResize : $this->useResize;
      
      $ffuseSlider = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useSlider','sPage2');
      $this->useSlider = (!empty($ffuseSlider)) ? $ffuseSlider : $this->useSlider;
      
      $ffuseWindowResize = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useWindowResize','sPage2');
      $this->useWindowResize = (!empty($ffuseWindowResize)) ? $ffuseWindowResize : $this->useWindowResize;

      $ffuseMouseWheel = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useMouseWheel','sPage2');
      $this->useMouseWheel = (!empty($ffuseMouseWheel)) ? $ffuseMouseWheel : $this->useMouseWheel;
      
      $ffuseKeyInput = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useKeyInput','sPage2');
      $this->useKeyInput = (!empty($ffuseKeyInput)) ? $ffuseKeyInput : $this->useKeyInput;
      
      $ffuseViewer = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useViewer','sPage2');
      $this->useViewer = (!empty($ffuseViewer)) ? $ffuseViewer : $this->useViewer;

      $ffuseAutoPlay = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useAutoPlay','sPage2');
      $this->useAutoPlay = (!empty($ffuseAutoPlay)) ? $ffuseAutoPlay : $this->useAutoPlay;
      
      $ffuseAutoPlayOnStart = $obj->pi_getFFvalue($obj->cObj->data['pi_flexform'], 'useAutoPlayOnStart','sPage2');
      $this->useAutoPlayOnStart = (!empty($ffuseAutoPlayOnStart)) ? $ffuseAutoPlayOnStart : $this->useAutoPlayOnStart;

      $this->onEmptyinit = '\'onEmptyinit\': function() {
        this.loadJSON(\'index.php?eID=tx_cfamooflow_pi1&damcat='.$obj->catArray[0].'\');
       }';
    }
    
    private function getBGColor() {
      $bgColor = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'bgColor','sPage2');
      // Fix FF2 behavior with transparent setting
      if (preg_match("/Firefox\/2/i", $_SERVER['HTTP_USER_AGENT']) && $bgColor == 'transparent') {
   	$bgColor = 'rgba(0,0,0,0)';
      }
      return $bgColor;
    }
}
?>