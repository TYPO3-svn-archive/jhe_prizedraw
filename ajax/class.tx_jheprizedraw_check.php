<?php
/***************************************************************
*  Copyright notice
*
*  c) 2010 Jari-Hermann Ernst <jari-hermann.ernst@bad-gmbh.de>
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

$GLOBALS['LANG']->includeLLFile('EXT:jhe_prizedraw/mod1/locallang.xml');

require_once(PATH_t3lib . 'class.t3lib_scbase.php');
require_once(PATH_t3lib . 'class.t3lib_befunc.php');
require_once(PATH_t3lib . 'class.t3lib_div.php');
require_once(PATH_t3lib . 'class.t3lib_db.php');

class tx_jheprizedraw_check {
	//global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
	
	/**
	 * Main Methode
	 *
	 * @return	string
	 */
	public function main() {
		$data = t3lib_div::_GET('data');
		$type = t3lib_div::_GET('type');
		$result = '';

		switch($type) {
			case 'number':
				if (!$data){
					$result = $LANG->getLL('err_noValue');
				} else if (!is_numeric($data)) {
					$result = $GLOBALS['LANG']->getLL('err_noNumber');
				}
				break;
			case 'select':
				if (!$data){
					$result = $GLOBALS['LANG']->getLL('err_noSelection');
				}
				break;
			case 'date':
				$date = explode('-', $data);
				$day = $date[0];
				$month = $date[1];
				$year = $date[2];
				if ($data && !preg_match('/^[0123]?\d\-[01]?\d\-\d{4}$/', $data)){
					$result = $GLOBALS['LANG']->getLL('err_wrongDateFormat');
				} else if ($data && !checkdate($month, $day, $year)) {
					$result = $GLOBALS['LANG']->getLL('err_wrongDate');
				}
				break;
			case '':
				$result = $GLOBALS['LANG']->getLL('err_fatal');
				break;
		}

		return $result;
	}
}

$output = t3lib_div::makeInstance('tx_jheprizedraw_check');
echo $output->main();

?>