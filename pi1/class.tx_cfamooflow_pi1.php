<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Claus Fassing <claus@fassing.eu>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');



/**
 * Plugin 'MooFlow V0.2 integration' for the 'cfa_mooflow' extension.
 *
 * @author	Claus Fassing <claus@fassing.eu>
 * @package	TYPO3
 * @subpackage	tx_cfamooflow
 */
class tx_cfamooflow_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_cfamooflow_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_cfamooflow_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'cfa_mooflow';	// The extension key.
	var $filePath 	   = '';
	var $uploadPath	   = 'uploads/tx_cfamooflow/';
	var $webPath       = 'typo3conf/ext/cfa_mooflow/res/';
        var $extPath       = '';
	var $pi_checkCHash = true;
        var $linkMethod    = 'remooz';	
	var $catArray = Array();
	var $catCaptionArray = Array();
	var $multipleIsActive = false;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->filePath = dirname(t3lib_div::getIndpEnv("SCRIPT_FILENAME")).'/';
                $this->extPath = t3lib_extMgm::extPath("cfa_mooflow");
                $this->damcatOrderBy = addslashes($conf['damcat.']['sorting.']['field']);
		
		$this->initFlexformAndConfig($conf);
		
		if(!empty($GLOBALS['TSFE']->additionalHeaderData['mooflowJS'])){
		 $this->multipleIsActive = true;	
		}
		
		if((!empty($this->conf['autoSetup'])) && ($this->multipleIsActive == false)) {
		  $this->initAutoSetup();
		}
		
		if(!empty($this->conf['useDynLoader']) && $this->conf['mode']=='DAMCAT') {
                  /* Call this function only to get the categories without return code */
                  $this->getDamCatImages();
                }
		

				
		$startJS = '
              <script type="text/javascript">
              /* <![CDATA[ */
                var myMooFlowPage = {
                  start: function(){
                    var mf = new MooFlow($(\'MooFlow\'), {';
                $startJS .= "\n";
                if(!empty($this->conf['reflection'])) {
                  $reflection = $this->conf['reflection'];
                  //$reflection = substr($reflection, 0, -1);
                  $startJS .= 'reflection: '.$reflection.','."\n";
                }
                if(!empty($this->conf['heightRatio'])) {
                  $heightRatio = $this->conf['heightRatio'];
                  $startJS .= 'heightRatio: '.$heightRatio.','."\n";
                }
                if(!empty($this->conf['offsetY'])) {
                  $offsetY = $this->conf['offsetY'];
                  $startJS .= 'offsetY: '.$offsetY.','."\n";
                }                
                if(!empty($this->conf['startIndex'])) {
                  $startIndex = $this->conf['startIndex'];
                  $startJS .= 'startIndex: '.$startIndex.','."\n";
                }
                if(!empty($this->conf['interval'])) {
                  $interval = $this->conf['interval'];
                  $startJS .= 'interval: '.$interval.','."\n";
                }
                if(!empty($this->conf['factor'])) {
                  $factor = $this->conf['factor'];
                  $startJS .= 'factor: '.$factor.','."\n";
                }
                if(!empty($this->conf['bgColor'])) {
                  $bgColor = $this->conf['bgColor'];
                  $startJS .= 'bgColor: "'.$bgColor.'",'."\n";
                }
                if(!empty($this->conf['useCaption'])) {
                  $startJS .= 'useCaption: true,'."\n";
                }
                if(!empty($this->conf['useResize'])) {
                  $startJS .= 'useResize: true,'."\n";
                }
                if(!empty($this->conf['useSlider'])) {
                  $startJS .= 'useSlider: true,'."\n";
                }
                if(!empty($this->conf['useWindowResize'])) {
                  $startJS .= 'useWindowResize: true,'."\n";
                }
                if(!empty($this->conf['useMouseWheel'])) {
                  $startJS .= 'useMouseWheel: true,'."\n";
                }
                if(!empty($this->conf['useKeyInput'])) {
                  $startJS .= 'useKeyInput: true,'."\n";
                }
                if(!empty($this->conf['useViewer'])) {
                  $startJS .= 'useViewer: true,'."\n";
                }
                if(!empty($this->conf['useAutoPlay'])) {
                  $startJS .= 'useAutoPlay: true,'."\n";
                }
                $startJS .= 
                						'\'onEmptyinit\': function(){
                              this.loadJSON(\'index.php?eID=tx_cfamooflow_pi1&damcat='.$this->catArray[0].'&sortinstruction='.$this->damcatOrderBy.'\');
                            },'."\n";
                /* Cut off last char if needed */
                // $startJS = substr($startJS, 0, -1);

                /* Callback function */  
                /* Start autoPlay from onStart function */
                if(!empty($this->conf['useAutoPlayOnStart'])) {
                	$startJS .= '
      			\'onStart\': function(){
	      		this.autoPlay = this.auto.periodical(this.options.interval, this);
			this.isAutoPlay = true;
			this.fireEvent(\'autoPlay\');
    			},';              
    		}
    		// Call linkMethod function
    		$startJS .= '\'onClickView\': '.$this->prepareLinkMethod();
                
                $startJS .= '
                              link: function(result){
                                if(result.target == "_blank") {
                                  window.open(result.href);
                                } else {
                                  document.location = result.href;
                                }
	                      }
                            };
                        
                          window.addEvent(\'domready\', myMooFlowPage.start);
                          
                          /* ]]> */
                          </script>';
		
		$GLOBALS['TSFE']->additionalHeaderData['mooflowCoreJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'mootools-1.2.5-core.js"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['mooflowMoreJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'mootools-1.2.3.1-more.js"></script>';
		if(($this->clickOption == "single") && (empty($this->conf['autoSetup'])))  {
			$GLOBALS['TSFE']->additionalHeaderData['mooflowJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'MooFlow.Mod.js"></script>';
		} elseif (!empty($this->conf['autoSetup'])) {
		        $GLOBALS['TSFE']->additionalHeaderData['mooflowJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'MooFlow.autoSetup.js"></script>';
		} else {
		  $GLOBALS['TSFE']->additionalHeaderData['mooflowJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'MooFlow.js"></script>';
		}
		$GLOBALS['TSFE']->additionalHeaderData['mooflowCSS'] = '<link rel="stylesheet" type="text/css" href="'.$this->webPath.'MooFlow.css" />';
    
	    if($this->linkMethod == "remooz") {
	      $GLOBALS['TSFE']->additionalHeaderData['remoozCSS'] = '<link rel="stylesheet" type="text/css" href="'.$this->webPath.'ReMooz/ReMooz.css" />';
	      $GLOBALS['TSFE']->additionalHeaderData['remoozJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'ReMooz/ReMooz.js"></script>';
	    }
            if(empty($this->conf['autoSetup'])) {
		$GLOBALS['TSFE']->additionalHeaderData['startmooflow'] = $startJS;
	    }
		$content = $this->buildHtmlOutput();
		
		return $this->pi_wrapInBaseClass($content);
	}
	
	/**
	 * This function builds the needed HTML code and inserts the images set in the content element
	 * 
	 * @return  HTML Code
	 */
	 function buildHtmlOutput() {
	  if(!empty($this->conf['params'])) {
            $parapairs = explode("\n",$this->conf['params']);
            foreach($parapairs as $item) {
              /* Reset the arrays */
              unset($attrstr);
              unset($attrstrpair);
              unset($attrpair);
              unset($attr);

              //$attrstr = explode("=",$item); obsolte
              // We do need a split function working with escape character to avoid delimiter characters from split
              //if using inside the string. Needed to get url (which have : inside) in title and/or description.
              $attrstr = $this->splitWithEscape($item,'=','#');
              $attrstrpair = explode(";",$attrstr[1]);
              foreach($attrstrpair as $keyvalue) {
                //$attrpair = explode(":",$keyvalue); obsolete see description above
                $attrpair = $this->splitWithEscape($keyvalue,':','#');        				
                $attr[$attrpair[0]] = $attrpair[1];
              }
              /* url fix */
              if(!empty($attr['href'])) {
                if(substr($attr['href'], 0, 1) != "/") {
                  $attr['href'] = 'http://'.$attr['href'];
                }
              }
          $attrHash[$attrstr[0]] = $attr; 
          }
  	}
                
    $hashnum = 1; 
    if(empty($this->conf['autoSetup'])) {
    	$html = '
            <div id="MooFlow" class="mf">';
    } else {
    	$html = '
            <div id="MooFlow" class="MooFlowieze mf">';
    }
    /* DynLoader */
    if(!empty($this->conf['useDynLoader']) && $this->conf['mode']=='DAMCAT') {
      $html .= '</div><div id="tx_cfamooflow_pi1_dynLoaderControl">';
      $countCat = 0;
      foreach($this->catArray as $cat) {
         if($countCat == 0) {
                $html .= '<div id="isInitLoadCat" class="tx_cfamooflow_pi1_loadjson isInitLoadCat"><a class="loadjson" href="index.php?eID=tx_cfamooflow_pi1&amp;damcat='.$cat.'&amp;sortinstruction='.$this->damcatOrderBy.'" >'.$this->catCaptionArray[$cat].'</a></div>';
         } else {
        	$html .= '<div class="tx_cfamooflow_pi1_loadjson"><a class="loadjson" href="index.php?eID=tx_cfamooflow_pi1&amp;damcat='.$cat.'&amp;sortinstruction='.$this->damcatOrderBy.'" >'.$this->catCaptionArray[$cat].'</a></div>';
        }
        ++$countCat;
      }
      
      $html .= '<div style="clear:both"></div></div>';
      
    } else {
   
      if ($this->conf['mode']=='MANUAL') {       
        $imgs = $this->getManualImages($attrHash,$hashnum);
      } elseif ($this->conf['mode'] == 'DIRECTORY') {
        $imgs = $this->getDirectoryImages();
      } elseif ($this->conf['mode']=='DAM') {
        $imgs = $this->getDamImages();
      } elseif ($this->conf['mode']=='DAMCAT') {
        $imgs = $this->getDamCatImages();
      }
              
      $html .= $imgs.'</div>';
    }
		return $html;
	 }
	
	/**
	 * Initializes Flexform values and TS, priority to FlexForms as they are more specific to the element
	 *
	 * @param	[type]		$conf: ...
	 * @return	[none]		none
	 * @param:	[array]		$conf: TSconf array
	 */
	function initFlexformAndConfig($conf) {
		// Initialize the FlexForms array
		$this->pi_initPIflexForm();
                
                // Images
		$ffimages = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'defaultmode','sPage1');
		if(!empty($ffimages)) $this->conf['images'] = $ffimages;
    
    // Directory
    $ffdirectory = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'directorymode','sPage1');
		if(!empty($ffdirectory)) $this->conf['directory'] = $ffdirectory;                
    
    $ffdModeImageTitle = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'dModeImageTitle','sPage1');
		if(!empty($ffdModeImageTitle)) $this->conf['dModeImageTitle'] = $ffdModeImageTitle;
		
		$ffdModeImageAlt = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'dModeImageAlt','sPage1');
		if(!empty($ffdModeImageAlt)) $this->conf['dModeImageAlt'] = $ffdModeImageAlt;
		
    // Image Parameter
		$ffparams = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'params','sPage1');
		if(!empty($ffparams)) $this->conf['params'] = $ffparams;
                
    // Mode selection
    $ffmode = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'mode','sPage1');
		if(!empty($ffmode)) $this->conf['mode'] = $ffmode;
		
		// Get DamCat
		$ffmodedamcat = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'modedamcat','sPage1');
		if(!empty($ffmodedamcat)) $this->conf['modedamcat'] = $ffmodedamcat;
		
		$ffrecursivedamcat = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'recursivedamcat','sPage1');
		if(!empty($ffrecursivedamcat)) $this->conf['recursivedamcat'] = $ffrecursivedamcat;

    //Reflection
		$ffreflection = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'reflection','sPage2');
		if(!empty($ffreflection)) $this->conf['reflection'] = $ffreflection;

		//Auto Setup
		$ffautoSetup = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'autoSetup','sPage2');
		if(!empty($ffautoSetup)) $this->conf['autoSetup'] = $ffautoSetup;

    //Doubleclick behavior
		$fflinkMethod = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'linkMethod','sPage2');
		if(!empty($fflinkMethod)) $this->linkMethod = $fflinkMethod;

    //Click Option
		$ffclickOption = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'clickOption','sPage2');
		if(!empty($ffclickOption)) $this->clickOption = $ffclickOption;
		
		//DynLoader
		$ffuseDynLoader = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'useDynLoader','sPage1');
		if(!empty($ffuseDynLoader)) $this->conf['useDynLoader'] = $ffuseDynLoader;		

		//heightRatio
		$ffheightRatio = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'heightRatio','sPage2');
		if(!empty($ffheightRatio)) $this->conf['heightRatio'] = $ffheightRatio;
	         
	  //offsetY
		$ffoffsetY = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'offsetY','sPage2');
		if(!empty($ffoffsetY)) $this->conf['offsetY'] = $ffoffsetY;
		
		//startIndex
		$ffstartIndex = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'startIndex','sPage2');
		if(!empty($ffstartIndex)) $this->conf['startIndex'] = $ffstartIndex;
		
		//interval
		$ffinterval = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'interval','sPage2');
		if(!empty($ffinterval)) $this->conf['interval'] = $ffinterval;
		
		//factor
		$fffactor = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'factor','sPage2');
		if(!empty($fffactor)) $this->conf['factor'] = $fffactor;
		
		//bgColor
		$ffbgColor = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'bgColor','sPage2');
		// Fix FF2 behavior with transparent setting
		if (preg_match("/Firefox\/2/i", $_SERVER['HTTP_USER_AGENT']) && $ffbgColor == 'transparent') {
   			$ffbgColor = 'rgba(0,0,0,0)';
		}
		if(!empty($ffbgColor)) $this->conf['bgColor'] = $ffbgColor;
		
		//useCaption
		$ffuseCaption = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'useCaption','sPage2');
		if(!empty($ffuseCaption)) $this->conf['useCaption'] = $ffuseCaption;
		
		//useResize
		$ffuseResize = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'useResize','sPage2');
		if(!empty($ffuseResize)) $this->conf['useResize'] = $ffuseResize;
		
		//useSlider
		$ffuseSlider = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'useSlider','sPage2');
		if(!empty($ffuseSlider)) $this->conf['useSlider'] = $ffuseSlider;
		
		//useWindowResize
		$ffuseWindowResize = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'useWindowResize','sPage2');
		if(!empty($ffuseWindowResize)) $this->conf['useWindowResize'] = $ffuseWindowResize;
		
		//useMouseWheel
		$ffuseMouseWheel = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'useMouseWheel','sPage2');
		if(!empty($ffuseMouseWheel)) $this->conf['useMouseWheel'] = $ffuseMouseWheel;
		
		//useKeyInput
		$ffuseKeyInput = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'useKeyInput','sPage2');
		if(!empty($ffuseKeyInput)) $this->conf['useKeyInput'] = $ffuseKeyInput;
		
		//useViewer
		$ffuseViewer = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'useViewer','sPage2');
		if(!empty($ffuseViewer)) $this->conf['useViewer'] = $ffuseViewer;

    		//useAutoPlay
		$ffuseAutoPlay = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'useAutoPlay','sPage2');
		if(!empty($ffuseAutoPlay)) $this->conf['useAutoPlay'] = $ffuseAutoPlay;
		
                //Autoplay on Start
		$ffuseAutoPlayOnStart = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'useAutoPlayOnStart','sPage2');
		if(!empty($ffuseAutoPlayOnStart)) $this->conf['useAutoPlayOnStart'] = $ffuseAutoPlayOnStart;


		// Other generic settings are fetched
		$this->conf['pidList'] = $this->cObj->data['pages'];
		$this->conf['recursive'] = $this->cObj->data['recursive'];
		
		
		return;
	}
	
  function initAutoSetup() {
    // reset dynloader if autosetup enabled
      unset($this->conf['useDynLoader']);
      
      // default confArray
      $destConfArray = array('\'onClickView\':' => $this->prepareLinkMethod(), 'reflection:' => '0.4', 'heightRatio:' => '0.6', 'offsetY:' => '0', 'startIndex:' => '0', 'interval:' => '3000', 'factor:' => '115', 'bgColor:' => '\'#000\'', 'useCaption:' => 'false', 'useResize:' => 'false', 'useSlider:' => 'false', 'useWindowResize:' => 'false', 'useMouseWheel:' => 'true', 'useKeyInput:' => 'false', 'useViewer:' => 'false');
      // override if is not empty
      if(!empty($this->conf['reflection'])){
        $destConfArray['reflection:'] = $this->conf['reflection'];
      }
      if(!empty($this->conf['heightRatio'])){
        $destConfArray['heightRatio:'] = $this->conf['heightRatio'];
      }
      if(!empty($this->conf['offsetY'])){
        $destConfArray['offsetY:'] = $this->conf['offsetY'];
      }
      if(!empty($this->conf['startIndex'])){
        $destConfArray['startIndex:'] = $this->conf['startIndex'];
      }
      if(!empty($this->conf['interval'])){
        $destConfArray['interval:'] = $this->conf['interval'];
      }
      if(!empty($this->conf['factor'])){
        $destConfArray['factor:'] = $this->conf['factor'];
      }
      if(!empty($this->conf['bgColor'])){
        $destConfArray['bgColor:'] = '\''.$this->conf['bgColor'].'\'';
      }
      if(!empty($this->conf['useCaption'])){
        $destConfArray['useCaption:'] = $this->conf['useCaption'];
      }
      if(!empty($this->conf['useResize'])){
        $destConfArray['useResize:'] = $this->conf['useResize'];
      }
      if(!empty($this->conf['useSlider'])){
        $destConfArray['useSlider:'] = $this->conf['useSlider'];
      }
      if(!empty($this->conf['useWindowResize'])){
        $destConfArray['useWindowResize:'] = $this->conf['useWindowResize'];
      }
      if(!empty($this->conf['useMouseWheel'])){
        $destConfArray['useMouseWheel:'] = $this->conf['useMouseWheel'];
      }
      if(!empty($this->conf['useKeyInput'])){
        $destConfArray['useKeyInput:'] = $this->conf['useKeyInput'];
      }
      if(!empty($this->conf['useViewer'])){
        $destConfArray['useViewer:'] = $this->conf['useViewer'];
      }
      $strSerialized = serialize($destConfArray);
      $destFileName = 'strSerialized';
      if(file_exists($this->extPath.$destFileName)) {
        $sourceFileName = t3lib_div::getURL($this->extPath.$destFileName);
        $sourceConfArray = unserialize($sourceFileName);
        if($destConfArray != $sourceConfArray){
              t3lib_div::writeFile($this->extPath.$destFileName,$strSerialized);
              $lines = file($this->extPath.'res/MooFlow.pre.js');
              $destLines = '';
              foreach ($lines as $line_num => $line) {
                if(preg_match("/###OPTIONS###/",$lines[$line_num]) != 0){
                      $count = count($destConfArray);
                      $i = 0;
                      foreach($destConfArray as $key => $value) {
                        $i++;
                        $destLines .= $key.$value;
                        if($i != $count) {
                          $destLines .= ',';
                        }
                        $destLines .= "\n";
                      }
                      continue;
                }
                $destLines .= $line;
              }
              t3lib_div::writeFile($this->extPath.'res/MooFlow.autoSetup.js',$destLines);
        }
      } else {
        t3lib_div::writeFile($this->extPath.$destFileName,$strSerialized);
              $lines = file($this->extPath.'res/MooFlow.pre.js');
              $destLines = '';
              foreach ($lines as $line_num => $line) {
                if(preg_match("/###OPTIONS###/",$lines[$line_num]) != 0){
                      $count = count($destConfArray);
                      $i = 0;
                      foreach($destConfArray as $key => $value) {
                        $i++;
                        $destLines .= $key.$value;
                        if($i != $count) {
                          $destLines .= ',';
                        }
                        $destLines .= "\n";
                      }
                      continue;
                }
                $destLines .= $line;
              }
              t3lib_div::writeFile($this->extPath.'res/MooFlow.autoSetup.js',$destLines);
      }
    return;		
  }
	
  function getManualImages($attrHash,$hashnum) {
      $images = explode(",",$this->conf['images']);
      foreach($images as $image) {
        if($this->linkMethod == "remooz") {
        	if($attrHash[0]) {
        		/* If there an override for all picture, use only this */
        		$hashnum = 0;
        	}
          $imgs .= '<a href="'.$this->uploadPath.$image.'" rel="image" target="_blank">';
          $imgs .= '<img src="'.$this->uploadPath.$image.'" alt="'.$attrHash[$hashnum]['alt'].'" longdesc="" title="'.$attrHash[$hashnum]['title'].'" />';
          $imgs .= '</a>';
        } elseif($this->linkMethod == "link") {
           if(empty($attrHash[$hashnum]['href'])) {
              $href = $this->uploadPath.$image;
           } else {
              $href = $attrHash[$hashnum]['href'];
           }
           if(empty($attrHash[$hashnum]['target'])) {
            $target = '_blank';   
           } else {
            $target = $attrHash[$hashnum]['target'];
           }	
          $imgs .= '<a href="'.$href.'" rel="image" target="'.$target.'">';
          $imgs .= '<img src="'.$this->uploadPath.$image.'" alt="'.$attrHash[$hashnum]['alt'].'" longdesc="" title="'.$attrHash[$hashnum]['title'].'" />';
          $imgs .= '</a>';
        } else {
          $imgs .= '<div><img src="'.$this->uploadPath.$image.'" alt="'.$attrHash[$hashnum]['alt'].'" longdesc="'.$attrHash[$hashnum]['longdesc'].'" title="'.$attrHash[$hashnum]['title'].'" /></div>';
        }            
        $hashnum++;
      }
      return($imgs);
          
  }
	function getDirectoryImages() {  		 
        if (is_dir($this->conf['directory'])) {
  		  $images = array(); 
  		  $images = $this->getFiles($this->conf['directory']);      	
  		     
        // add the images
        foreach ($images as $key=>$value) {
          $path = $this->conf['directory'].$value;

          $imgs .= '<a href="'.$path.'" rel="image" target="_blank">';
          $imgs .= '<img src="'.$path.'" ';
          if(!empty($this->conf['dModeImageTitle'])) {
                  $imgs .= 'title="'.$this->conf['dModeImageTitle'].'" ';
          } else {
                  $imgs .= 'title=" " ';
          }
          if(!empty($this->conf['dModeImageAlt'])) {
                  $imgs .= 'alt="'.$this->conf['dModeImageAlt'].'" ';
          } else {
                  $imgs .= 'alt=" " ';
          }
					
          /* $imgs .= '<img src="'.$path.'" alt=" " title=" " />'; */
          $imgs .= ' /></a>';	

        } # end foreach file
        
  		  
  		} # end is_dir 
  		return $imgs;
  }
  
  function getFiles($path, $extra = "") {
    // check for needed slash at the end
		$length = strlen($path);
		if ($path{$length-1}!='/') { 
      $path.='/';
    }
    
    $imagetypes = $this->conf["filetypes"] ? explode(',', $this->conf["filetypes"]) : array(
        'jpg',
        'jpeg',
        'gif',
        'png'
    );

    if($dir = dir($path)) {
        $files = Array();

        while(false !== ($file = $dir->read())) {
            if ($file != '.' && $file != '..') {
                $ext = strtolower(substr($file, strrpos($file, '.')+1));
                if (in_array($ext, $imagetypes)) {
                    array_push($files, $extra . $file);
                }
                else if ($this->conf["recursive"] == '1' && is_dir($path . "/" . $file)) {
                    $dirfiles = $this->getFiles($path . "/" . $file, $extra . $file . "/");
                    if (is_array($dirfiles)) {
                        $files = array_merge($files, $dirfiles);
                    }
                }
            }
        }

        $dir->close();
        // sort files, thx to all
        sort($files);

        return $files;
    }
  } # end getFiles
  
  
	function getDamImages() {		
    // check if there's a localized version of the current content object
    $uid = $this->cObj->data['uid'];
    if ($this->cObj->data['_LOCALIZED_UID']) {
            $uid = $this->cObj->data['_LOCALIZED_UID'];
    }
    $sys_language_uid = $GLOBALS['TSFE']->sys_language_content;

    // get all DAM files
    $images = tx_dam_db::getReferencedFiles('tt_content',$uid,'cfa_mooflow','tx_dam_mm_ref');

    // add image
    foreach ($images['files'] as $key=>$path) {    
      // get data from the single image
      $fields = 'title,description,file_name,instructions';
      $tables = 'tx_dam';

      // now i check the tx_dam table to see if there's a localization for the current DAM record (image)
      $temp_where='l18n_parent = '.$key.' AND sys_language_uid = '.$sys_language_uid;
      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', $tables, $temp_where);
      // if i find a localized record i overwrite the default language $key with the localized language $key
      if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
        $key = $row['uid'];
      }
      $GLOBALS['TYPO3_DB']->sql_free_result($res);
      
      $temp_where='uid = '.$key;
      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $tables, $temp_where);
      $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
      /** 
      * Get link from instructions field if is available and linkMethod is "link" 
      * Elsewhere use picture path as link
      */
      if($this->linkMethod == "link" && $row['instructions']) {
        /* url fix */
        if(substr($row['instructions'], 0, 1) != "/") {
          $row['instructions'] = 'http://'.$row['instructions'];
        }                  
	$imgs .= '<a href="'.$row['instructions'].'" rel="image" target="_blank">';
      } else {
      	$imgs .= '<a href="'.$path.'" rel="image" target="_blank">';
      }
      $imgs .= '<img src="'.$path.'" alt="'.strtr(htmlspecialchars($row['description']), array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')).'" title="'.strtr(htmlspecialchars($row['title']), array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')).'" />';
      $imgs .= '</a>';
    } 
    return($imgs);
  } 
          
  function getDamCatImages() {

          // add image
    $list= str_replace('tx_dam_cat_', '',$this->conf['modedamcat']);

    $listRecursive = $this->getDamCatRecursive($list,$this->conf['recursivedamcat']);
    $listArray = explode(',',$listRecursive);
    $files = Array();
      foreach($listArray as $cat) {				
        // add images from categories
        $fields = 'tx_dam.uid,tx_dam.title,tx_dam.description,tx_dam.file_name,tx_dam.file_path,tx_dam.instructions';
        $tables = 'tx_dam,tx_dam_mm_cat';
        $temp_where = 'tx_dam.deleted = 0 AND tx_dam.file_mime_type=\'image\' AND tx_dam.hidden=0 AND tx_dam_mm_cat.uid_foreign='.$cat.' AND tx_dam_mm_cat.uid_local=tx_dam.uid';
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $tables, $temp_where, '', 'tx_dam.' . $this->damcatOrderBy);
             
        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
        //t3lib_div::debug($row['uid'],"rowuid");
          $files[$row['uid']] = $row; # just add the image to an array
        }
        
        /* The dynLoader need the cat title to set this as caption */
        if(!empty($this->conf['useDynLoader'])) {
          array_push($this->catArray,$cat);
          $field = 'title';
          $table = 'tx_dam_cat';
          $where = 'uid='.$cat;
          $catres = $GLOBALS['TYPO3_DB']->exec_SELECTquery($field, $table, $where);
          while($catrow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($catres)){
            $this->catCaptionArray[$cat] = $catrow['title'];
          }
        }
      }
      
      /* If DynLoader is active, just get the categories and return */
      if(!empty($this->conf['useDynLoader'])) {
        return;
      }
                
      // add the image for real
    foreach ($files as $key=>$row) {
      $path =  $row['file_path'].$row['file_name'];
      
      if($this->linkMethod == "link" && $row['instructions']) {
      $instructions = explode(";",$row['instructions']);
      if(!empty($instructions[1])) {
        $target = $instructions[1];
      } else {
      	$target = "_blank";
      }
  	/* url fix */
      	if(substr($instructions[0], 0, 1) != "/") {
        	$instructions[0] = 'http://'.$instructions[0];
        }                  
	 $imgs .= '<a href="'.$instructions[0].'" rel="image" target="'.$target.'">';
      } else {
       	$imgs .= '<a href="'.$path.'" rel="image" target="_blank">';
      }
      $imgs .= '<img src="'.$path.'" alt="'.strtr(htmlspecialchars($row['description']), array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')).'" title="'.strtr(htmlspecialchars($row['title']), array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')).'" />';
      $imgs .= '</a>';
    }			
    return($imgs);
  }
      
  function getDamCatRecursive($id,$level=0) {
    $result = $id.','; # add id of 1st level 
    $idList = explode(',',$id);
    
    if ($level > 0) {
      $level--;
      
      foreach ($idList as $key=>$value) {
        $where = 'hidden=0 AND deleted=0 AND parent_id='.$id;
        $res= $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_dam_cat', $where);
        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){	
          $all[$row['uid']]=$row['uid'];
          $rec = $this->getDamCatRecursive($row['uid'],$level);
          if ($rec!='')  {
            $result.=$rec.',';
          }
        }
      } # end for each
    } # end if level
        
    $result = str_replace(',,',',',$result);
    $result = substr($result,0,-1);
    return $result;
  }  
  function splitWithEscape($str, $delimiterChar = ',', $escapeChar = '"') {
    $len = strlen($str);
    $tokens = array();
    $i = 0;
    $inEscapeSeq = false;
    $currToken = '';
    while ($i < $len) {
        $c = substr($str, $i, 1);
        if ($inEscapeSeq) {
            if ($c == $escapeChar) {
                // lookahead to see if next character is also an escape char
                if ($i == ($len - 1)) {
                    // c is last char, so must be end of escape sequence
                    $inEscapeSeq = false;
                } else if (substr($str, $i + 1, 1) == $escapeChar) {
                    // append literal escape char
                    $currToken .= $escapeChar;
                    $i++;
                } else {
                    // end of escape sequence
                    $inEscapeSeq = false;
                }
            } else {
                $currToken .= $c;
            }
        } else {
            if ($c == $delimiterChar) {
                // end of token, flush it
                array_push($tokens, $currToken);
                $currToken = '';
            } else if ($c == $escapeChar) {
                // begin escape sequence
                $inEscapeSeq = true;
            } else {
                $currToken .= $c;
            }
        }
        $i++;
    }
    // flush the last token
    array_push($tokens, $currToken);
    return $tokens;
  }
        
  function prepareLinkMethod(){
    if($this->linkMethod == "link") {
      if(empty($this->conf['autoSetup'])) {
        $initLinkMethod .= '
                    function(obj){
                          myMooFlowPage.link(obj);
                      }  
                    });
                      },';            
      } else {
        $initLinkMethod .= '
          function(obj){
            if(obj.target == "_blank") {
              window.open(obj.href);
            } else {
              document.location = obj.href;
            }
          }';
      }
    } elseif($this->linkMethod == "remooz") {
      if(empty($this->conf['autoSetup'])) {
        $initLinkMethod .= '
                      function(obj){
                              var img = new Element(\'img\',{src:obj.src, title:obj.title, alt:obj.alt, styles:obj.coords}).setStyles({\'position\':\'absolute\',\'border\':\'none\'});
                              var link = new Element(\'a\',{\'class\':\'remooz-element\',\'href\':obj.href,\'title\':obj.title + \' - \'+ obj.alt, styles:{\'border\':\'none\'}});
                              $(document.body).adopt(link.adopt(img));
                              var remooz = new ReMooz(link, {
                                centered: true,
                                resizeFactor: 0.8,
                                origin: link.getElement(\'img\'),
                                      onCloseEnd: function(){link.destroy()}
                      });
                              remooz.open();
                      }
        });
                    $$(\'.loadremote\').addEvent(\'click\', function(){
                            mf.loadHTML(this.get(\'href\'), this.get(\'rel\'));
                            return false;
                    });
                    /* Dynloader */
                    $$(\'.loadjson\').addEvent(\'click\', function(){
                      mf.loadJSON(this.get(\'href\'));
                      $(\'isInitLoadCat\').removeClass(\'isInitLoadCat\');
                      var allToggler = $$(\'.tx_cfamooflow_pi1_loadjson\');
                      allToggler.each(function(item, index){
                              item.removeClass(\'activeCatMarker\');
                      });
                      this.getParent().addClass(\'activeCatMarker\');
                      return false;
                    });
                  },';
      } else {
        $initLinkMethod .= '
           function(obj){
            var img = new Element(\'img\',{src:obj.src, title:obj.title, alt:obj.alt, styles:obj.coords}).setStyles({\'position\':\'absolute\',\'border\':\'none\'});
            var link = new Element(\'a\',{\'class\':\'remooz-element\',\'href\':obj.href,\'title\':obj.title + \' - \'+ obj.alt, styles:{\'border\':\'none\'}});
            $(document.body).adopt(link.adopt(img));
            var remooz = new ReMooz(link, {
              centered: true,
              resizeFactor: 0.8,
              origin: link.getElement(\'img\'),
              onCloseEnd: function(){link.destroy()}
              });
            remooz.open();
          
          
          $$(\'.loadremote\').addEvent(\'click\', function(){
            mf.loadHTML(this.get(\'href\'), this.get(\'rel\'));
            return false;
          });
          /* Dynloader */
          $$(\'.loadjson\').addEvent(\'click\', function(){
            mf.loadJSON(this.get(\'href\'));
            $(\'isInitLoadCat\').removeClass(\'isInitLoadCat\');
            var allToggler = $$(\'.tx_cfamooflow_pi1_loadjson\');
            allToggler.each(function(item, index){
              item.removeClass(\'activeCatMarker\');
            });
            this.getParent().addClass(\'activeCatMarker\');
            return false;
          });
          }';
      }
    }
    
    return $initLinkMethod;
  }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfa_mooflow/pi1/class.tx_cfamooflow_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfa_mooflow/pi1/class.tx_cfamooflow_pi1.php']);
}

?>
