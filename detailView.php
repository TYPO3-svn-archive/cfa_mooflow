<?php
/*****NOT IN USE !!!*****/
// Exit, wenn das Script direkt aufgerufen wird
if (!defined ('PATH_typo3conf')) die ('Could not access this script directly!');


require_once(PATH_tslib.'class.tslib_pibase.php');

class LinkDetailView extends tslib_pibase {

	var $prefixId = 'tx_cfamooflow_p1_LinkDetailView';
	var $scriptRelPath = 'p1/detailView.php';
	var $extKey = 'cfa_mooflow';
	var $pi_checkCHash = true;
	var $pid;

	public function __construct() {
		// FE-User initialisieren
		$feUserObj = tslib_eidtools::initFeUser();
		
		// Verbindung zur Datenbank
		tslib_eidtools::connectDB();
		
		$this->pi_setPiVarDefaults();
	}

	function main() {

		$uid = intval(t3lib_div::_GET('uid'));
		

		$field = 'bodytext';
		$table = 'tt_content';
		$where = 'uid='.$uid;

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($field, $table, $where);
		if($res !== false) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if($row !== false) {
				//echo '<div>'.$row['bodytext'].'</div>';
				echo $this->pi_wrapInBaseClass($row['bodytext']);
				// $this->pid = $row['pid'];
			}
		}
	}
}
$extensionkey = t3lib_div::makeInstance('LinkDetailView');
$extensionkey->main();


?>