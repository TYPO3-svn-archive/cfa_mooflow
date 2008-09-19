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

if (!defined ('PATH_typo3conf')) die ('Could not access this script directly!');



/**
 * Plugin 'MooFlow V0.2 integration' for the 'cfa_mooflow' extension.
 *
 * @author	Claus Fassing <claus@fassing.eu>
 * @package	TYPO3
 * @subpackage	tx_cfamooflow
 */
class FlexformConfig {	
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
?>