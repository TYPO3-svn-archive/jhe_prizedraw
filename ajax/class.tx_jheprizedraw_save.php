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

require_once(PATH_t3lib . 'class.t3lib_scbase.php');
require_once(PATH_t3lib . 'class.t3lib_befunc.php');
require_once(PATH_t3lib . 'class.t3lib_div.php');
require_once(PATH_t3lib . 'class.t3lib_db.php');

class tx_jheprizedraw_save {

	/**
	 * Main Methode
	 *
	 * @return	string
	 */
	public function main() {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		//_GET-data to variables
		$savedata = substr(t3lib_div::_GET('savedata'),0, -2);
		$savedataArray = explode('::', $savedata);

		foreach($savedataArray as $val){
			$singleArray = explode('|', $val);
			$record = $singleArray[0];
			$table = $singleArray[1];

			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				$table,
				'uid = ' . $record,
				array('tx_jheprizedraw_prize_draw_winner' => time())
			);

		}
		$result = $LANG->getLL('res_saveSuccess');

		return $result;
	}
}

$output = t3lib_div::makeInstance('tx_jheprizedraw_save');
echo $output->main();

?>