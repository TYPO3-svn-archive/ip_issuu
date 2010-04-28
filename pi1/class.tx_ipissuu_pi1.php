<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Ingo Pfennigstorf <i.pfennigstorf@gmail.com>
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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'issuu' for the 'ip_issuu' extension.
 *
 * @author	Ingo Pfennigstorf <i.pfennigstorf@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_ipissuu
 */
class tx_ipissuu_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_ipissuu_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_ipissuu_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ip_issuu';	// The extension key.
	var $pi_checkCHash = true;

	//SWFObject Version to fetch
	var $swfObjectVersion = '2.2';

	// divid for the playeer
	var $contentId = null;

	//flashvars
	var $layout;
	var $backgroundcolor ='FFFFFF';
	var $showflipbtn = true;
	var $documentid = null;
	var $docname;
	var $username;
	var $loadinginfotext;
	var $width;
	var $height;
	var $unit = 'px';
	var $hostname;

	//@TODO experimental
	var $flashTransparent;

	//embedding code
	var $embedCode = null;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();

		$this->hostname = t3lib_div::getIndpEnv('TYPO3_HOST_ONLY');

		//div id of the player
		$this->contentId = 'issuuViewer-'.$GLOBALS['TSFE']->id;

		//Code from Flexform
		$this->embedCode = str_replace(']','',$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'jCode', 'sDEF'));

		//transparentÊ@TODO
		$this->flashTransparent = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'flashTrans', 'sDEF');


		//SwfObject into the header
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] = $this->addSWFObjectToHead();

		//einlesen des doks
		$this->getIssuuJoom();

		//der rest
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = $this->getIssuuCode();

		//nur die simple id in den content
		$content='<div id="'.$this->contentId.'" style="width:'.$this->width.$this->unit.'; height:'.$this->height.$this->unit.';"></div>';

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 *	Function to load SWFObject via Google Ajax Apis
	 *	@TODO for those who don't like google or so maybe implement feature to load SWFObject locally (Selection via TS-Constants)
	 */
	function addSWFObjectToHead(){
		$callSWF = '<script type="text/javascript" src="http://www.google.com/jsapi"></script><script type="text/javascript">google.load("swfobject", '.$this->swfObjectVersion.');</script>';

		return $callSWF;
	}

	/**
	 *	get Issuu Code and Options From XML
	 *	@TODO implement other embeddingoptions
	 */
	function getIssuuCode(){
		$isuuCode = '<script type="text/javascript">  var attributes = { id: \''.$this->contentId.'\' }; var params = { '.$this->getTransparentMode().' allowfullscreen: \'true\', menu: \'false\' }; var flashvars = { jsAPIClientDomain: \''.$this->hostname.'\', mode: \'embed\', backgroundColor:\''.$this->backgroundcolor.'\', layout: \''.$this->layout.'\',   showFlipBtn: \''.$this->showflipbtn.'\', documentId: \''.$this->documentid.'\', docName: \''.$this->docname.'\', username: \''.$this->username.'\', loadingInfoText: \''.$this->loadinginfotext.'\', et: \''.time().'\', er: \'71\' }; ';
		$isuuCode .='swfobject.embedSWF("http://static.issuu.com/webembed/viewers/style1/v1/IssuuViewer.swf", "'.$this->contentId.'", "'.$this->width.'", "'.$this->height.'", "9.0.0","swfobject/expressInstall.swf", flashvars, params, attributes);</script>';
		return $isuuCode;
	}

	/**
	 *	Read Code and call Formatting Function
	 *	@TODO check if code is valid (regex)
	 */
	function getIssuuJoom(){

		$vars = explode(' ',$this->embedCode);

		for ($i= 1; $i < sizeOf($vars); $i++){
			$this->getVars($vars[$i]);
		}

	}

	/**
	 *	Get Elements from Code and put them to Variables
	 *	@TODO maybe check variables
	 *	@TODO maybe an array would be better instead of variables for each parameter
	 */
	function getVars($variable){

		$kette = explode('=',$variable);
		$this->$kette[0] = $kette[1];

	}

	/**
	 * Experimental Use of wmode = transparent
	 * @TODO Testing
	 * @return unknown_type
	 */
	function getTransparentMode(){

		$wmode = '';

		if ($this->flashTransparent == 1){
			$wmode = 'wmode: \'transparent\',';
		}

		return $wmode;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ip_issuu/pi1/class.tx_ipissuu_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ip_issuu/pi1/class.tx_ipissuu_pi1.php']);
}

?>