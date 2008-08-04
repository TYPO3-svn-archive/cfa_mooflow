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
	var $pi_checkCHash = true;
        var $linkMethod    = 'remooz';	

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
		$this->initFlexformAndConfig($conf);
		
		$startJS = '
                          <script type="text/javascript">
                          /* <![CDATA[ */
                            var myMooFlowPage = {
                              start: function(){
                                var mf = new MooFlow($(\'MooFlow\'), {';
                $startJS .= "\n";
                if(!empty($this->conf['reflection'])) {
                  $reflection = $this->conf['reflection'];
                  $reflection = substr($reflection, 0, -1);
                  $startJS .= 'reflection: '.$reflection.','."\n";
                }
                if(!empty($this->conf['heightRatio'])) {
                  $heightRatio = $this->conf['heightRatio'];
                  $heightRatio = substr($heightRatio, 0, -1);
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
                /* Cut off last char if needed */
                // $startJS = substr($startJS, 0, -1);
                
                /* Callback function */                
                if($this->linkMethod == "link") {
                $startJS .= '
                                \'onClickView\': function(obj){
                                      myMooFlowPage.link(obj);
                                  }  
                                });
                              },';              
                } elseif($this->linkMethod == "remooz") {
                $startJS .= '
                                  \'onClickView\': function(obj){
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
                              },';
                }
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
		
		$GLOBALS['TSFE']->additionalHeaderData['mooflowCoreJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'mootools-1.2-core.js"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['mooflowMoreJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'mootools-1.2-more.js"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['mooflowJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'MooFlow.js"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['mooflowCSS'] = '<link rel="stylesheet" type="text/css" href="'.$this->webPath.'MooFlow.css" />';
                if($this->linkMethod == "remooz") {
                  $GLOBALS['TSFE']->additionalHeaderData['remoozCSS'] = '<link rel="stylesheet" type="text/css" href="'.$this->webPath.'ReMooz/ReMooz.css" />';
                  $GLOBALS['TSFE']->additionalHeaderData['remoozJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'ReMooz/ReMooz.js"></script>';
                }
		$GLOBALS['TSFE']->additionalHeaderData['startmooflow'] = $startJS;

		
		if(!empty($this->conf['images'])) {
			$content = $this->buildHtmlOutput();
		}
		
		
		return $this->pi_wrapInBaseClass($content);
	}
	
	/**
	 * This function builds the needed HTML code and inserts the images set in the content element
	 * 
	 * @return  HTML Code
	 */
	 function buildHtmlOutput() {
		$parapairs = explode("\n",$this->conf['params']);
  		foreach($parapairs as $item) {
                                /* Reset the arrays */
                                unset($attrstr);
                                unset($attrstrpair);
                                unset($attrpair);
                                unset($attr);

     				$attrstr = explode("=",$item);
     				$attrstrpair = explode(";",$attrstr[1]);
     				foreach($attrstrpair as $keyvalue) {
        				$attrpair = explode(":",$keyvalue);
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
                
                $hashnum = 1;       
                $html = '
                        <div id="MooFlow" class="mf">';
                        
		$images = explode(",",$this->conf['images']);
		foreach($images as $image) {
                        if(!empty($attrHash[$hashnum]['href']) && !empty($attrHash[$hashnum]['rel']) && !empty($attrHash[$hashnum]['target'])) {
                          $imgs .= '<a href="'.$attrHash[$hashnum]['href'].'" rel="'.$attrHash[$hashnum]['rel'].'" target="'.$attrHash[$hashnum]['target'].'">';
                          $imgs .= '<img src="'.$this->uploadPath.$image.'" alt="'.$attrHash[$hashnum]['alt'].'" longdesc="'.$attrHash[$hashnum]['longdesc'].'" title="'.$attrHash[$hashnum]['title'].'" />';
                          $imgs .= '</a>';
                        } else {
			  $imgs .= '<div><img src="'.$this->uploadPath.$image.'" alt="'.$attrHash[$hashnum]['alt'].'" longdesc="'.$attrHash[$hashnum]['longdesc'].'" title="'.$attrHash[$hashnum]['title'].'" /></div>';
                        }
			$hashnum++;
		}
                
                $html .= $imgs.'</div>';
                
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
		$ffimages = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'images','sPage1');
		if(!empty($ffimages)) $this->conf['images'] = $ffimages;
                
                // Image Parameter
		$ffparams = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'params','sPage1');
		if(!empty($ffparams)) $this->conf['params'] = $ffparams;
                                                                
                //Reflection
		$ffreflection = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'reflection','sPage2');
		if(!empty($ffreflection)) $this->conf['reflection'] = $ffreflection;

                //Doubleclick behavior
		$fflinkMethod = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'linkMethod','sPage2');
		if(!empty($fflinkMethod)) $this->linkMethod = $fflinkMethod;

		
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
		
		// Other generic settings are fetched
		$this->conf['pidList'] = $this->cObj->data['pages'];
		$this->conf['recursive'] = $this->cObj->data['recursive'];

		return;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfa_mooflow/pi1/class.tx_cfamooflow_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfa_mooflow/pi1/class.tx_cfamooflow_pi1.php']);
}

?>