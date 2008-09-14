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
		if($this->clickOption == "single") {
			$GLOBALS['TSFE']->additionalHeaderData['mooflowJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'MooFlow.Mod.js"></script>';
		} else {
		  $GLOBALS['TSFE']->additionalHeaderData['mooflowJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'MooFlow.js"></script>';
		}
		$GLOBALS['TSFE']->additionalHeaderData['mooflowCSS'] = '<link rel="stylesheet" type="text/css" href="'.$this->webPath.'MooFlow.css" />';
                if($this->linkMethod == "remooz") {
                  $GLOBALS['TSFE']->additionalHeaderData['remoozCSS'] = '<link rel="stylesheet" type="text/css" href="'.$this->webPath.'ReMooz/ReMooz.css" />';
                  $GLOBALS['TSFE']->additionalHeaderData['remoozJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'ReMooz/ReMooz.js"></script>';
                }
		$GLOBALS['TSFE']->additionalHeaderData['startmooflow'] = $startJS;


		
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
  		}
                
                $hashnum = 1;       
                $html = '
                        <div id="MooFlow" class="mf">';
                
               
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

                //Doubleclick behavior
		$fflinkMethod = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'linkMethod','sPage2');
		if(!empty($fflinkMethod)) $this->linkMethod = $fflinkMethod;

                //Click Option
		$ffclickOption = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'clickOption','sPage2');
		if(!empty($ffclickOption)) $this->clickOption = $ffclickOption;
		
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
                    } elseif($this->linkMethod == "link" && !empty($attrHash[$hashnum]['href'])) {
                    	$imgs .= '<a href="'.$attrHash[$hashnum]['href'].'" rel="image" target="_blank">';
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
              $imgs .= '<img src="'.$path.'" alt="'.$row['description'].'" title="'.$row['title'].'" />';
              $imgs .= '</a>';
            } 
            return($imgs);
        } 
          
        function getDamCatImages() {
              // $content.=$this->beginGallery($this->config['id'],$limitImages);
                
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
              $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $tables, $temp_where);
                    
              while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
                $files[$row['uid']] = $row; # just add the image to an array
              }
            }
                      
            // add the image for real
          foreach ($files as $key=>$row) {
            $path =  $row['file_path'].$row['file_name'];
            
            if($this->linkMethod == "link" && $row['instructions']) {
        				/* url fix */
        				if(substr($row['instructions'], 0, 1) != "/") {
                	$row['instructions'] = 'http://'.$row['instructions'];
                }                  
								$imgs .= '<a href="'.$row['instructions'].'" rel="image" target="_blank">';
						} else {
              	$imgs .= '<a href="'.$path.'" rel="image" target="_blank">';
            }
            $imgs .= '<img src="'.$path.'" alt="'.$row['description'].'" title="'.$row['title'].'" />';
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
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfa_mooflow/pi1/class.tx_cfamooflow_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfa_mooflow/pi1/class.tx_cfamooflow_pi1.php']);
}

?>