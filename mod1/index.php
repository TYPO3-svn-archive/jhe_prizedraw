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
 * Hint: use extdeveval to insert/update function index above.
 */


$LANG->includeLLFile('EXT:jhe_prizedraw/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
require_once(PATH_t3lib . 'class.t3lib_befunc.php');
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
						$this->doc->form='<form id="tx_jheprizedraw_form" action="" method="post" enctype="multipart/form-data">';

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

						//$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

						$this->doc->inDocStylesArray[] = t3lib_div::getURL(t3lib_extMgm::extPath('jhe_prizedraw').'res/css/be.css');
						
						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
						$this->content.=$this->doc->divider(5);


						// Render content:
						$this->moduleContent();


						// ShortCut
						/*if ($BE_USER->mayMakeShortcut())	{
							$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
						}*/

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
					
					
				//$listGroupNames = t3lib_BEfunc::TYPO3_copyRightNotice();
				//var_dump($listGroupNames);	
					
					
				//include_once(t3lib_extMgm::extPath('date2cal') . 'src/class.jscalendar.php');
				//var_dump(include_once(t3lib_extMgm::extPath('date2cal') . 'src/class.jscalendar.php'));
					
				
				//if (class_exists('JSCalender')){
				//	$JSCalendar = JSCalendar::getInstance();
				//} else {
				//	echo "Class JSCalender not found!";
				//}
				
				//
				//$this->markerArray['###FIELD###'] .= $JSCalendar->render($value);
				//if (($jsCode = $JSCalendar->getMainJS()) != '') {
				//	$GLOBALS['TSFE']->additionalHeaderData['powermail_date2cal'] = $jsCode;
				//}
					
				//var_dump(include_once(t3lib_extMgm::siteRelPath('date2cal') . '/src/class.jscalendar.php'));
										
					switch((string)$this->MOD_SETTINGS['function']){
                    	case 1:
                    		                   		                   		
                    		$uid = $this->pageinfo['uid'];
                    		
                    		if($uid){
                    			$resFeUserRecords = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(*)', 'fe_users', 'pid = ' . $uid . '' );
	                       		$resFeUserRecordsArr = $GLOBALS['TYPO3_DB']->sql_fetch_row($resFeUserRecords) or die (mysql_error());
        	            		$noFeUserRecords = $resFeUserRecordsArr[0];
            	        		
                	    		$resAddressRecords = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(*)', 'tt_address', 'pid = '. $uid .'');
                    			$resAddressRecordsArr = $GLOBALS['TYPO3_DB']->sql_fetch_row($resAddressRecords) or die (mysql_error());
                    			$noAddressRecords = $resAddressRecordsArr[0];
                    			
	                    		$sumRelRecords = $noFeUserRecords + $noAddressRecords;
        	           		}
                    		 

                    		if(!$uid) {
								$content = $LANG->getLL('error_root'); 
							} else if($sumRelRecords == 0){
									$content = $LANG->getLL('error_no_records');
								} else {
                        			$content .= '<form id="jet" name="jet" method="post" action="">
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
      															<option value=""selected="selected">' . $LANG->getLL('lbl_select') . '</option>
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
    													<input type="submit" name="bt_execute" id="bt_execute" value="' . $LANG->getLL('lbl_bt_execute') . '" />
  													</p>
												</form>';
							
									$content.='<br />This is the GET/POST vars sent to the script:<br />'.
											'GET:'.t3lib_div::view_array($_GET).'<br />'.
											'POST:'.t3lib_div::view_array($_POST).'<br />'.
											'';
								}
							
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