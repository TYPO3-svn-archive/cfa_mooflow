<?php
// Exit, wenn das Script direkt aufgerufen wird
if (!defined ('PATH_typo3conf')) die ('Could not access this script directly!');

// FE-User initialisieren
$feUserObj = tslib_eidtools::initFeUser();

// Verbindung zur Datenbank
tslib_eidtools::connectDB();


// get all "get" parameters from URL
$get = explode('&', t3lib_div::implodeArrayForUrl('', $GLOBALS['_GET'])) ;
$excludeList = 'id,L,tx_ttnews[pointer],cHash,no_cache';
if (is_array($get)) {
    foreach ($get as $pair) {
        $tmp=explode("=",$pair);
        if ($tmp[0] != "") $urlParameters[$tmp[0]]=$tmp[1];
    }
}

// get typo3 linkvars
$linkVars = t3lib_div::trimExplode('&', $GLOBALS['TSFE']->linkVars);
if (is_array($linkVars)) {
	foreach($linkVars as $pair) {
        $tmp=explode("=",$pair);
        if ($tmp[0] != "")  $urlParameters[$tmp[0]]=$tmp[1];
    }
}

$cat = $urlParameters['damcat'];
$linkmethod = $urlParameters['linkmethod'];
$orderBy = rawurldecode($urlParameters['sortinstruction']);

$jsonResponse =  '{"images":[';

$files = Array();
// add images from categories
$fields = 'tx_dam.uid,tx_dam.title,tx_dam.description,tx_dam.file_name,tx_dam.file_path,tx_dam.instructions';
$tables = 'tx_dam,tx_dam_mm_cat';
$temp_where = 'tx_dam.deleted = 0 AND tx_dam.file_mime_type=\'image\' AND tx_dam.hidden=0 AND tx_dam_mm_cat.uid_foreign='.$cat.' AND tx_dam_mm_cat.uid_local=tx_dam.uid';
$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $tables, $temp_where, '', 'tx_dam.' . $orderBy);
                    
while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
  $files[$row['uid']] = $row; # just add the image to an array
}

// add the image for real
foreach ($files as $key=>$row) {
  $path =  $row['file_path'].$row['file_name'];
$instructions = explode(";",$row['instructions']);
	if(!empty($instructions[1])) {
		$target = $instructions[1];
	} else {
		$target = "_blank";
	}
	if(($linkmethod == "link" || $linkmethod == "detailView") && $row['instructions']) {	
		/* url fix */
		if((substr($instructions[0], 0, 1) != "/") && (substr($instructions[0], 0, 1) != "i")) {
			$instructions[0] = 'http://'.$instructions[0];
		}
		$jsonResponse .= '{"src":"'.$path.'", "href":"'.$instructions[0].'", "title":"'.strtr(htmlspecialchars($row['title']), array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />', '&lt;' => '<', '&gt;' => '>')).'", "alt":"'.strtr(htmlspecialchars($row['description']), array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />', '&lt;' => '<', '&gt;' => '>')).'", "rel":"image", "target":"_blank"},';
		 
	} else {
	  	$jsonResponse .= '{"src":"'.$path.'", "href":"'.$path.'", "title":"'.strtr(htmlspecialchars($row['title']), array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />', '&lt;' => '<', '&gt;' => '>')).'", "alt":"'.strtr(htmlspecialchars($row['description']), array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />', '&lt;' => '<', '&gt;' => '>')).'", "rel":"image", "target":"_blank"},';
	}
}
$jsonResponse = substr($jsonResponse, 0, -1);
$jsonResponse .= ']}';
echo $jsonResponse; 
exit;
?>
