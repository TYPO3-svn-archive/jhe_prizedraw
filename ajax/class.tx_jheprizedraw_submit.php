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

class tx_jheprizedraw_submit {

	/**
	 * Main Methode
	 *
	 * @return string
	 */
	public function main() { 
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		 
		$no_of_records = t3lib_div::_GET('no_of_records');
		$record_type = t3lib_div::_GET('record_type');
		$period_begin = t3lib_div::_GET('period_begin');
		$period_end = t3lib_div::_GET('period_end');
		$uid = t3lib_div::_GET('uid');
				
		$error = "";
		
		$htmlOutput = "<h3>Ergebnis:</h3>
						<table>
							<thead>
    							<tr>
      								<th>Name</th>
      								<th>Adresse</th>
      								<th>PLZ, Ort</th>
      								<th>eMail</th>
      								<th>Checkbox</th>
      								<th>Typ</th>
								</tr>
  							</thead>
  							<tbody>";		
		
		$resA = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*)', 
			'fe_users', 
			'deleted = 0 AND disable = 0 AND tx_jheprizedraw_prize_draw_winner = 0 AND pid = ' . $uid . ''
		);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_row($resA)){
			$maxFeusers = $row['0'];
		}

		$resB = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*)', 
			'tt_address', 
			'deleted = 0 AND hidden = 0 AND tx_jheprizedraw_prize_draw_winner = 0 AND pid = ' . $uid . ''
		);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_row($resB)){
			$maxTtaddress = $row['0'];
		}

		$maxSum = ($maxFeusers + $maxTtaddress);		
		
		switch($record_type) {
			case 'fe_user':
				if($maxFeusers >= $no_of_records){
					$getFeusers = $no_of_records;
					$getTt_address = 0;
				} else {
					$error = "Es sind zu wenig fe_users-Datensätze vorhanden!";
				}
				break;
			case 'tt_address':
				if($maxTtaddress >=$no_of_records){
					$getTt_address = $no_of_records;
					$getFeusers = 0;
				} else {
					$error = "Es sind zu wenig tt_address-Datensätze vorhanden!";
				}
				break;
			case 'both':
				
				if($maxSum >= $no_of_records) {
					$rand = rand(1,$no_of_records);
					
					if($maxFeusers >= $rand){
						$getFeusers = $rand;
						$getTt_address = $no_of_records - $rand; 
					} else {
						$getFeusers = $maxFeusers;
						$getTt_address = $no_of_records - $maxFeusers; 
					}
					
				} else {
					$error = "Es sind insgesamt zu wenig Datensätze vorhanden!";
				}
				
				break;
		}

		
		
		
		$resFeusers = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'name, address, zip, city, email', 
			'fe_users', 
			'deleted = 0 AND disable = 0 AND tx_jheprizedraw_prize_draw_winner = 0 AND pid = ' . $uid . '',
			'',
			'RAND()',
			$getFeusers
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resFeusers)) {
			$htmlOutput .= "<tr>
      							<td>". $row['name'] ."</td>
      							<td>". $row['address'] ."</td>
      							<td>". $row['zip'] ." ". $row['city'] ."</td>
      							<td>". $row['email'] ."</td>
      							<td>Checkbox</td>
      							<td>fe_users</td>
							</tr>"; 
		}
		
		
		
		
		$resTtaddress = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'name, address, zip, city, email', 
			'tt_address', 
			'deleted = 0 AND hidden = 0 AND tx_jheprizedraw_prize_draw_winner = 0 AND pid = ' . $uid . '',
			'',
			'RAND()',
			$getTt_address
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resTtaddress)) {
			$htmlOutput .= "<tr>
      							<td>". $row['name'] ."</td>
      							<td>". $row['address'] ."</td>
      							<td>". $row['zip'] ." ". $row['city'] ."</td>
      							<td>". $row['email'] ."</td>
      							<td>Checkbox</td>
      							<td>tt_address</td>
							</tr>"; 
		}
				
		$htmlOutput .= "</tbody></table>";
		if(!$error) {
			$result = $htmlOutput;
		} else {
			$result = $error;
		}
		
		return $result;
	}
	
	public function getMaxRecords($table, $uid){
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*)', 
			$table, 
			'deleted = 0 AND hidden = 0 AND tx_jheprizedraw_prize_draw_winner = 0 AND pid = ' . $uid . ''
		);
		$result = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
	}
}

$output = t3lib_div::makeInstance('tx_jheprizedraw_submit');
echo $output->main();

?>