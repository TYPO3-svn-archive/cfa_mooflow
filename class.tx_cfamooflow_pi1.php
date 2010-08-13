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

require_once(t3lib_extMgm::extPath('cfa_mooflow') . 'libs/class.FlexformConfig.php');

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
	var $catArray = Array();
	var $catCaptionArray = Array();
	var $template = array();

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->filePath = dirname(t3lib_div::getIndpEnv("SCRIPT_FILENAME")).'/';
		$this->extPath = t3lib_extMgm::extPath("cfa_mooflow");
		$this->pi_initPIflexForm();
		// Other generic settings are fetched
		$this->conf['pidList'] = $this->cObj->data['pages'];
		$this->conf['recursive'] = $this->cObj->data['recursive'];
		$this->damcatOrderBy = addslashes($conf['damcat.']['sorting.']['field']);
		$this->mootoolsLibCoreEnable = $conf['mootoolslib.']['core.']['enable'];
		$this->mootoolsLibMoreEnable = $conf['mootoolslib.']['more.']['enable'];
		$this->oConfig = new FlexformConfig($this);
		$this->damcatOrderBy = addslashes($conf['damcat.']['sorting.']['field']);
		
		/* JS Templating begin */
		$this->templatePrepareLinkMethod = $this->cObj->fileResource("EXT:cfa_mooflow/templates/prepareLinkMethod.tmpl");
		$this->templateStartJS = $this->cObj->fileResource("EXT:cfa_mooflow/templates/startJS.tmpl");
		$this->templateAutoSetupJS = $this->cObj->fileResource("EXT:cfa_mooflow/templates/autosetupJS.tmpl");

		$this->template["startjs"]=$this->cObj->getSubpart($this->templateStartJS,'###STARTJS###');
		if(($this->oConfig->useDynLoader) && ($this->oConfig->mode == 'DAMCAT')) {
			$this->template["onEmptyInit"]=$this->cObj->getSubpart($this->templateStartJS,'###ONEMPTYINIT###');
			$this->template["dynloader"]=$this->cObj->getSubpart($this->templateStartJS,'###DYNLOADER###');
		} else {
			$this->template["onEmptyInit"]='';
			$this->template["dynloader"] = '';
		}

		if($this->oConfig->useAutoPlayOnStart) {
			$this->template["useAutoPlayOnStart"]=$this->cObj->getSubpart($this->templateStartJS,'###USEAUTOPLAYONSTART###');
		} else {
			$this->template["useAutoPlayOnStart"]='';
		}

		if($this->oConfig->linkMethod == "link") {
			$this->template["linkFunction"]=$this->cObj->getSubpart($this->templateStartJS,'###LINKFUNCTION###');
		} elseif ($this->oConfig->linkMethod == "detailView") {
			$this->template["linkFunction"]=$this->cObj->getSubpart($this->templateStartJS,'###LINKFUNCTIONDETAIL###');
		} else {
			$this->template["linkFunction"]= '';
		}

		$this->template["onClickView"]=$this->cObj->getSubpart($this->templateStartJS,'###ONCLICKVIEW###');

		/* AutoSetup Subparts */
		$this->template["autoSetup"]=$this->cObj->getSubpart($this->templateAutoSetupJS,'###AUTOSETUPJS###');

		/* JS Templating end */

		if(($this->oConfig->useDynLoader) && ($this->oConfig->mode=='DAMCAT')) {
			/* Call this function only to get the categories without return code */
			$this->getDamCatImages();
		}
		
		/* Renew StartJS */

		$onClickViewMarkerArray['###LINKMETHOD###'] = $this->prepareLinkMethod();
		$catMarkerArray['###CATARRAY###'] = $this->catArray[0];
		$catMarkerArray['###SORT###'] = $this->damcatOrderBy;
		$catMarkerArray['###LINKMETHOD###'] = $this->oConfig->linkMethod;
		/*
		 $configArray = $this->oConfig->getConfigArray();
		 foreach ($configArray as $marker) {
		 $defaultMarkerArray['###'.strtoupper($marker).'###'] = $this->pi_getLL($marker);
		 }
		 */
		$defaultMarkerArray['###REFLECTION###'] = $this->oConfig->reflection;
		$defaultMarkerArray['###HEIGHTRATIO###'] = $this->oConfig->heightRatio;
		$defaultMarkerArray['###OFFSETY###'] = $this->oConfig->offsetY;
		$defaultMarkerArray['###STARTINDEX###'] = $this->oConfig->startIndex;
		$defaultMarkerArray['###INTERVAL###'] = $this->oConfig->interval;
		$defaultMarkerArray['###FACTOR###'] = $this->oConfig->factor;
		$defaultMarkerArray['###BGCOLOR###'] = $this->oConfig->bgColor;
		$defaultMarkerArray['###USECAPTION###'] = $this->oConfig->useCaption;
		$defaultMarkerArray['###USERESIZE###'] = $this->oConfig->useResize;
		$defaultMarkerArray['###USESLIDER###'] = $this->oConfig->useSlider;
		$defaultMarkerArray['###USEWINDOWRESIZE###'] = $this->oConfig->useWindowResize;
		$defaultMarkerArray['###USEMOUSEWHEEL###'] = $this->oConfig->useMouseWheel;
		$defaultMarkerArray['###USEKEYINPUT###'] = $this->oConfig->useKeyInput;
		$defaultMarkerArray['###USEVIEWER###'] = $this->oConfig->useViewer;
		$defaultMarkerArray['###USEAUTOPLAY###'] = $this->oConfig->useAutoPlay;

		
		$defaultMarkerArray['###USEAUTOPLAYONSTARTMARKER###'] = $this->template["useAutoPlayOnStart"];
		#$defaultMarkerArray['###ONEMPTYINITMARKER###'] = $this->template["onEmptyInit"];
		$defaultMarkerArray['###ONEMPTYINITMARKER###'] = $this->cObj->substituteMarkerArrayCached($this->template["onEmptyInit"],$catMarkerArray);
		$defaultMarkerArray['###DYNLOADERMARKER###'] = $this->template["dynloader"];
		$defaultMarkerArray['###ONCLICKVIEWMARKER###'] = $this->cObj->substituteMarkerArrayCached($this->template["onClickView"],$onClickViewMarkerArray);
		//$defaultMarkerArray['###ONCLICKVIEWMARKER###'] = '';
		$defaultMarkerArray['###LINKFUNCTIONMARKER###'] = $this->template["linkFunction"];
		//$defaultMarkerArray['###LINKFUNCTIONMARKER###'] = '';


		$startJS = $this->cObj->substituteMarkerArrayCached($this->template["startjs"],$defaultMarkerArray);

		if(!empty($GLOBALS['TSFE']->additionalHeaderData['mooflowJS'])){
		 $this->oConfig->isLoaded = True;
		 $this->oConfig->autoSetup = 1;
		 $content = $this->buildHtmlOutput();
		 return $this->pi_wrapInBaseClass($content);
		}

		if(($this->oConfig->autoSetup) && (!$this->oConfig->isLoaded)) {
			unset($this->oConfig->useDynLoader);
			$autoSetupContent = $this->cObj->substituteMarkerArrayCached($this->template["autoSetup"],$defaultMarkerArray);
			t3lib_div::writeFile($this->extPath.'res/MooFlow.autoSetup.js',$autoSetupContent);
		}


		$GLOBALS['TSFE']->additionalHeaderData['mooflowCoreJS'] = ($this->mootoolsLibCoreEnable) ? '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'mootools-1.2.4-core.js"></script>' : '';
		$GLOBALS['TSFE']->additionalHeaderData['mooflowMoreJS'] = ($this->mootoolsLibMoreEnable) ? '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'mootools-1.2.4.4-more.js"></script>' : '';
		if(($this->oConfig->clickOption == "single") && (!$this->oConfig->autoSetup))  {
			$GLOBALS['TSFE']->additionalHeaderData['mooflowJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'MooFlow.Mod.js"></script>';
		} elseif ($this->oConfig->autoSetup) {
			$GLOBALS['TSFE']->additionalHeaderData['mooflowJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'MooFlow.autoSetup.js"></script>';
		} else {
			$GLOBALS['TSFE']->additionalHeaderData['mooflowJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'MooFlow.js"></script>';
		}
		$GLOBALS['TSFE']->additionalHeaderData['mooflowCSS'] = '<link rel="stylesheet" type="text/css" href="'.$this->webPath.'MooFlow.css" />';

		if($this->oConfig->linkMethod == "remooz") {
			$GLOBALS['TSFE']->additionalHeaderData['moordCoreJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'moo.rd_v1.3.2.js"></script>';
			$GLOBALS['TSFE']->additionalHeaderData['remoozCSS'] = '<link rel="stylesheet" type="text/css" href="'.$this->webPath.'ReMooz/ReMooz.css" />';
			$GLOBALS['TSFE']->additionalHeaderData['remoozJS'] = '<script language="JavaScript" type="text/javascript" src="'.$this->webPath.'ReMooz/ReMooz.js"></script>';
		}
		if(!$this->oConfig->autoSetup) {
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
		if(!empty($this->oConfig->params)) {
			$parapairs = explode("\n",$this->oConfig->params);
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
					if((substr($attr['href'], 0, 1) != "/") && (substr($attr['href'], 0, 1) != "u")) {
						$attr['href'] = 'http://'.$attr['href'];
					}
				}
				$attrHash[$attrstr[0]] = $attr;
			}
		}

		$hashnum = 1;
		if(!$this->oConfig->autoSetup) {
			$html = '
            <div id="MooFlow" class="mf">';
		} else {
			$html = '
            <div id="MooFlow" class="MooFlowieze mf">';
		}
		/* DynLoader */
		if(($this->oConfig->useDynLoader) && ($this->oConfig->mode=='DAMCAT')) {
			$html .= '</div><div id="tx_cfamooflow_pi1_dynLoaderControl">';
			$countCat = 0;
			foreach($this->catArray as $cat) {
				if($countCat == 0) {
                $html .= '<div id="isInitLoadCat" class="tx_cfamooflow_pi1_loadjson isInitLoadCat"><a class="loadjson" href="index.php?eID=tx_cfamooflow_pi1&amp;damcat='.$cat.'&amp;sortinstruction='.$this->damcatOrderBy.'&amp;linkmethod='.$this->oConfig->linkMethod.'" >'.$this->catCaptionArray[$cat].'</a></div>';
				} else {
        	$html .= '<div class="tx_cfamooflow_pi1_loadjson"><a class="loadjson" href="index.php?eID=tx_cfamooflow_pi1&amp;damcat='.$cat.'&amp;sortinstruction='.$this->damcatOrderBy.'&amp;linkmethid='.$this->oConfig->linkMethod.'" >'.$this->catCaptionArray[$cat].'</a></div>';
				}
				++$countCat;
			}

			$html .= '<div style="clear:both"></div></div>';

		} else {
			 
			if ($this->oConfig->mode=='MANUAL') {
				$imgs = $this->getManualImages($attrHash,$hashnum);
			} elseif ($this->oConfig->mode == 'DIRECTORY') {
				$imgs = $this->getDirectoryImages();
			} elseif ($this->oConfig->mode=='DAM') {
				$imgs = $this->getDamImages();
			} elseif ($this->oConfig->mode=='DAMCAT') {
				$imgs = $this->getDamCatImages();
			}

			$html .= $imgs.'</div>';
		}
		// TODO if abfrage
		$html.= '<div id="tx-cfamooflow-pi1_response" style="visibility=hidden;">&nbsp;</div>';
		return $html;
	}


	function getManualImages($attrHash,$hashnum) {
		$images = explode(",",$this->oConfig->images);
		foreach($images as $image) {
			if($this->oConfig->linkMethod == "remooz") {
				if($attrHash[0]) {
					/* If there an override for all picture, use only this */
					$hashnum = 0;
				}
				$imgs .= '<a href="'.$this->uploadPath.$image.'" rel="image" target="_blank">';
				$imgs .= '<img src="'.$this->uploadPath.$image.'" alt="'.$attrHash[$hashnum]['alt'].'" longdesc="" title="'.$attrHash[$hashnum]['title'].'" />';
				$imgs .= '</a>';
			} elseif($this->oConfig->linkMethod == "link" || $this->oConfig->linkMethod == "detailView") {
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
		if (is_dir($this->oConfig->directory)) {
			$images = array();
			$images = $this->getFiles($this->oConfig->directory);
			 
			// add the images
			foreach ($images as $key=>$value) {
				$path = $this->oConfig->directory.$value;

				$imgs .= '<a href="'.$path.'" rel="image" target="_blank">';
				$imgs .= '<img src="'.$path.'" ';
				if(!empty($this->oConfig->dModeImageTitle)) {
					$imgs .= 'title="'.$this->oConfig->dModeImageTitle.'" ';
				} else {
					$imgs .= 'title=" " ';
				}
				if(!empty($this->oConfig->dModeImageAlt)) {
					$imgs .= 'alt="'.$this->oConfig->dModeImageAlt.'" ';
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
			if(($this->oConfig->linkMethod == "link" || $this->oConfig->linkMethod == "detailView") && $row['instructions']) {
				$instructions = explode(";",$row['instructions']);
				if(!empty($instructions[1])) {
					$target = $instructions[1];
				} else {
					$target = "_blank";
				}
				/* url fix */
				if((substr($instructions[0], 0, 1) != "/") && (substr($instructions[0], 0, 1) != "i")) {
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

	function getDamCatImages() {

		// add image
		$list= str_replace('tx_dam_cat_', '',$this->oConfig->modedamcat);

		$listRecursive = $this->getDamCatRecursive($list,$this->oConfig->recursivedamcat);
		$listArray = explode(',',$listRecursive);
		$files = Array();
		foreach($listArray as $cat) {
			// add images from categories
			$fields = 'tx_dam.uid,tx_dam.title,tx_dam.description,tx_dam.file_name,tx_dam.file_path,tx_dam.instructions';
			$tables = 'tx_dam,tx_dam_mm_cat';
			$temp_where = 'tx_dam.deleted = 0 AND tx_dam.file_mime_type=\'image\' AND tx_dam.hidden=0 AND tx_dam_mm_cat.uid_foreign='.$cat.' AND tx_dam_mm_cat.uid_local=tx_dam.uid';
		        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $tables, $temp_where, '', 'tx_dam.' . $this->damcatOrderBy);
			 
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$files[$row['uid']] = $row; # just add the image to an array
			}

			/* The dynLoader need the cat title to set this as caption */
			if($this->oConfig->useDynLoader) {
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
		if($this->oConfig->useDynLoader) {
			return;
		}

		// add the image for real
		foreach ($files as $key=>$row) {
			$path =  $row['file_path'].$row['file_name'];

			if(($this->oConfig->linkMethod == "link" || $this->oConfig->linkMethod == "detailView") && $row['instructions']) {
				$instructions = explode(";",$row['instructions']);
				if(!empty($instructions[1])) {
					$target = $instructions[1];
				} else {
					$target = "_blank";
				}
				/* url fix */
				if((substr($instructions[0], 0, 1) != "/") && (substr($instructions[0], 0, 1) != "i")) {
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

		if($this->oConfig->linkMethod == "link" || $this->oConfig->linkMethod == "detailView") {
			if($this->oConfig->autoSetup) {
				$this->template["linkMethod"]=$this->cObj->getSubpart($this->templatePrepareLinkMethod,'###LINKMETHOD_LINK_AUTOSETUP###');
			} else {
				$this->template["linkMethod"]=$this->cObj->getSubpart($this->templatePrepareLinkMethod,'###LINKMETHOD_LINK###');
			}
		} elseif($this->oConfig->linkMethod == "remooz") {
			if($this->oConfig->autoSetup) {
				$this->template["linkMethod"]=$this->cObj->getSubpart($this->templatePrepareLinkMethod,'###LINKMETHOD_REMOOZ_AUTOSETUP###');
			} else {
				//$this->template["linkMethod"]=$this->cObj->getSubpart($this->templatePrepareLinkMethod,'###LINKMETHOD_REMOOZ###');
				$remoozMarkerArray['###USEOVERLAY###'] = $this->oConfig->useOverlay;
				$remoozMarkerArray['###OVERLAYCOLOR###'] = $this->oConfig->overlayColor;
				$this->template["linkMethod"]=$this->cObj->substituteMarkerArrayCached($this->cObj->getSubpart($this->templatePrepareLinkMethod,'###LINKMETHOD_REMOOZ###'),$remoozMarkerArray);
			}
		}
		 

		return($this->template["linkMethod"]);

	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfa_mooflow/pi1/class.tx_cfamooflow_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfa_mooflow/pi1/class.tx_cfamooflow_pi1.php']);
}

?>
