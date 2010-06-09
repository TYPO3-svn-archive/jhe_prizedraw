<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Jari-Hermann Ernst <jari-hermann.ernst@bad-gmbh.de>
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
 *
 *
 *   57: class  tx_jheprizedraw_module1 extends t3lib_SCbase
 *   65:     function init()
 *   82:     function menuConfig()
 *   99:     function main()
 *  118:     function jumpToUrl(URL)
 *  170:     function printContent()
 *  181:     function moduleContent()
 *  288:     function getNoOfRecordsPerPage($table, $pid)
 *
 * TOTAL FUNCTIONS: 7
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


$LANG->includeLLFile('EXT:jhe_prizedraw/mod1/locallang.xml');

require_once(PATH_t3lib . 'class.t3lib_scbase.php');
require_once(PATH_t3lib . 'class.t3lib_befunc.php');
require_once(PATH_t3lib . 'class.t3lib_div.php');
require_once(PATH_t3lib . 'class.t3lib_flashmessage.php');


$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

/**
 * Module 'Prize draw' for the 'jhe_prizedraw' extension.
 *
 * @author	Jari-Hermann Ernst <jari-hermann.ernst@bad-gmbh.de>
 * @package	TYPO3
 * @subpackage	tx_jheprizedraw
 */
class  tx_jheprizedraw_module1 extends t3lib_SCbase {
				var $pageinfo;

				/**
 * Initializes the Module
 *
 * @return	void
 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					parent::init();

					/*
					if (t3lib_div::_GP('clear_all_cache'))	{
						$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
					}
					*/
				}

				/**
 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
 *
 * @return	void
 */
				function menuConfig()	{
					global $LANG;
					$this->MOD_MENU = Array (
						'function' => Array (
							'1' => $LANG->getLL('function1'),
							'2' => $LANG->getLL('function2'),
						)
					);
					parent::menuConfig();
				}

				/**
 * Main function of the module. Write the content to $this->content
 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
 *
 * @return	[type]		...
 */
				function main()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
					$access = is_array($this->pageinfo) ? 1 : 0;

					if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

							// Draw the header.
						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;
						$this->doc->form='<form action="" id="tx_jheprizedraw_form" name="editform" method="post" enctype="multipart/form-data">';
							// JavaScript
						$this->doc->JScode = '
							<script language="javascript" type="text/javascript">
								script_ended = 0;
								function jumpToUrl(URL)	{
									document.location = URL;
								}
							</script>
						';
						$this->doc->postCode='
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';

						$this->doc->inDocStylesArray[] = t3lib_div::getURL(t3lib_extMgm::extPath('jhe_prizedraw').'res/css/be.css');
						
						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
						$this->content.=$this->doc->divider(5);

						// Render content:
						$this->moduleContent();
						
						// ShortCut
						if ($BE_USER->mayMakeShortcut())	{
							$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
						}
					
						$this->content.=$this->doc->spacer(10);
						
					} else {
							// If no access or if ID == zero

						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->spacer(10);
					}

				}

				/**
 * Prints out the module HTML
 *
 * @return	void
 */
				function printContent()	{

					$this->content.=$this->doc->endPage();
					echo $this->content;
				}

				/**
 * Generates the module content
 *
 * @return	void
 */
				function moduleContent(){
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					$this->doc->postCode .= '
						<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
						<script type="text/javascript">
				
							$(document).ready(function() {
								
								// AJAX Request per ajaxID
								$("#no_of_records").bind("focusout", function() {
									$.ajax({
					    				url: "/dev/typo3/ajax.php?ajaxID=tx_jheprizedraw::check&data=" + $(this).val() + "&type=number",
					    				success: function(result) {
					    					if(result) {
												$("#no_of_records").after("<span id=\"no_of_records_error\">" + result + "<span>");
												$("#bt_execute").attr("value", "NEIN");
											} else {
												$("#bt_execute").attr("value", "Starten");
											}
										}
									});
						
									return false;
								});
					
								$("#no_of_records").bind("focus", function() {
									$("#no_of_records_error").remove();
								});
								
								$("#record_type").bind("focusout", function() {
									$.ajax({
					    				url: "/dev/typo3/ajax.php?ajaxID=tx_jheprizedraw::check&data=" + $(this).val() + "&type=select",
					    				success: function(result) {
					    					if(result) {
												$("#record_type").after("<span id=\"record_type_error\">" + result + "<span>");
												$("#bt_execute").attr("value", "NEIN");
											} else {
												$("#bt_execute").attr("value", "Starten");
											} 
										}
									});
						
									return false;
								});
					
								$("#record_type").bind("focus", function() {
									$("#record_type_error").remove();
								});
								
								$("#period_begin").bind("focusout", function() {
									$.ajax({
					    				url: "/dev/typo3/ajax.php?ajaxID=tx_jheprizedraw::check&data=" + $(this).val() + "&type=date",
					    				success: function(result) {
					    					if(result) {
												$("#period_begin").after("<span id=\"period_begin_error\">" + result + "<span>");
												$("#bt_execute").attr("value", "NEIN");
											} else {
												$("#bt_execute").attr("value", "Starten");
											}
										}
									});
						
									return false;
								});
					
								$("#period_begin").bind("focus", function() {
									$("#period_begin_error").remove();
								});
								
								$("#period_end").bind("focusout", function() {
									$.ajax({
					    				url: "/dev/typo3/ajax.php?ajaxID=tx_jheprizedraw::check&data=" + $(this).val() + "&type=date",
					    				success: function(result) {
					    					if(result) {
												$("#period_end").after("<span id=\"period_end_error\">" + result + "<span>");
												$("#bt_execute").attr("value", "NEIN");
											} else {
												$("#bt_execute").attr("value", "Starten");
											} 
										}
									});
						
									return false;
								});
					
								$("#period_end").bind("focus", function() {
									$("#period_end_error").remove();
								});
								
							});
								
							
								if ($("#bt_execute").val() == "Starten"){
									$("#bt_execute").removeAttr("disabled");
								}
						</script>	
					';
					
					
					switch((string)$this->MOD_SETTINGS['function']){
                    	case 1:

                    		$uid = $this->pageinfo['uid'];

                    		if($uid){
                    			$noFeUserRecords = $this->getNoOfRecordsPerPage('fe_users', $uid);
                    			$noAddressRecords = $this->getNoOfRecordsPerPage('tt_address', $uid);

	                    		$sumRelRecords = $noFeUserRecords + $noAddressRecords;
        	           		}


                    		if(!$uid) {
								$content = $LANG->getLL('error_root');
							} else if($sumRelRecords == 0){
									$content = $LANG->getLL('error_no_records');
								} else {
																
                        			$content .= '	<input type="hidden" id="errorCounter" />
  													<p>
    													<label for="no_of_records">' . $LANG->getLL('lbl_no_of_records') . '</label>
    													<input name="no_of_records" type="text" id="no_of_records" size="4" />
  													</p>';

                        			if(!$noFeUserRecords) {
                        				$content .=	'	<input name="adressOnly" type="hidden" id="adressOnly" value="true" />';
                        			} else if(!$noAddressRecords) {
                        				$content .=	'	<input name="feUserOnly" type="hidden" id="feUserOnly" value="true" />';
                        			} else {
                        				$content .=	'	<p>
    														<label for="record_type">' . $LANG->getLL('lbl_record_type') . '</label>
    														<select name="record_type" id="record_type">
      															<option value="" selected="selected">' . $LANG->getLL('lbl_select') . '</option>
    															<option value="fe_user">' . $LANG->getLL('lbl_fe_user') . '</option>
      															<option value="tt_address">' . $LANG->getLL('lbl_tt_address') . '</option>
      															<option value="both">' . $LANG->getLL('lbl_both') . '</option>
	    													</select>
  														</p>';
                        			}

									$content .= '	<p>
  														<label class="header">' . $LANG->getLL('lbl_period') . '</label>
  													</p>
  													<p>
    													<label for="period_begin">' . $LANG->getLL('lbl_period_begin') . '</label>
    													<input type="text" name="period_begin" id="period_begin" />
  													</p>
  													<p>
    													<label for="period_end">' . $LANG->getLL('lbl_period_end') . '</label>
    													<input type="text" name="period_end" id="period_end" />
	  												</p>
  													<p>
    													<input type="submit" name="bt_execute" id="bt_execute" disabled="disabled" value="' . $LANG->getLL('lbl_bt_execute') . '" />
  													</p>
												';
								}

							$content.='<br />This is the GET/POST vars sent to the script:<br />'.
											'GET:'.t3lib_div::view_array($_GET).'<br />'.
											'POST:'.t3lib_div::view_array($_POST).'<br />'.
											'';
                	        
                	        $this->content.=$this->doc->section('',$content,0,1);

						break;
                        case 2:
                        	if(!$this->pageinfo['uid']) {
								$content = $LANG->getLL('error_root');
							} else {
                        		$content.='';
                            	$content.='<br />This is the GET/POST vars sent to the script:<br />'.
										'GET:'.t3lib_div::view_array($_GET).'<br />'.
										'POST:'.t3lib_div::view_array($_POST).'<br />'.
										'';
							}
                            $this->content.=$this->doc->section('',$content,0,1);
                        break;
					}
				}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$table: ...
	 * @param	[type]		$pid: ...
	 * @return	[type]		...
	 */
				function getNoOfRecordsPerPage($table, $pid) {

					$resRecords = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(*)', $table, 'pid = ' . $pid . '' );
	                $resRecordsArr = $GLOBALS['TYPO3_DB']->sql_fetch_row($resRecords) or die (mysql_error());

	                return $resRecordsArr[0];

				}
			}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jhe_prizedraw/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jhe_prizedraw/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_jheprizedraw_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>