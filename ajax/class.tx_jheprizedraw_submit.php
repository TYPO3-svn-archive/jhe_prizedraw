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
require_once(PATH_t3lib . 'class.t3lib_flashmessage.php');
require_once(PATH_tslib . 'class.tslib_content.php');

class tx_jheprizedraw_submit {

	/**
	 * Main Methode
	 *
	 * @return	string
	 */
	public function main() {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		//_GET-data to variables
		$no_of_records = t3lib_div::_GET('no_of_records');
		$record_type = t3lib_div::_GET('record_type');
		$period_begin = explode('-', t3lib_div::_GET('period_begin'));
		$period_end = explode('-', t3lib_div::_GET('period_end'));
		$uid = t3lib_div::_GET('uid');

		$tstampBegin = mktime(0,0,0,$period_begin[1],$period_begin[0],$period_begin[2]);
		$tstampEnd = mktime(23,59,59,$period_end[1],$period_end[0],$period_end[2]);

		$error = '';

		//Begin HTML output
		$htmlOutput = '
			<script type="text/javascript">
				$(document).ready(function() {
					$("#checkAll").click(function(){
						var checked_status = this.checked;
						$("input[name=update]").each(function(){
							this.checked = checked_status;
						});
					});

					$("#bt_save").click(function() {
						var checkedRecords = "";
						var values = $("input[name=update]").serializeArray();
						$.each(values, function(i, values){
							checkedRecords += $(this).val() + "::";
						});

						$("#ajaxloader").show();
						$.ajax({
					    	url: "/dev/typo3/ajax.php?ajaxID=tx_jheprizedraw::save&savedata=" + checkedRecords + "",
							success: function(result) {
								$("#ajaxloader").hide();
								$("#result").html(result);
							}
						});
						return false;
					});
				});
			</script>
			<h3>Ergebnis:</h3>
			<table border="0" width="100%">
				<thead>
					<tr>
						<th>Name</th>
						<th>Adresse</th>
						<th>PLZ, Ort</th>
						<th>eMail</th>
						<th class="centered"><input type="checkbox" name="checkAll" id="checkAll" /></th>
					</tr>
				</thead>
				<tbody>';

		$maxFeusers = $this->getMaxRecords('fe_users', 'disable', $uid, 'crdate', $tstampBegin, $tstampEnd);
		$maxTtaddress = $this->getMaxRecords('tt_address', 'hidden', $uid, 'tstamp', $tstampBegin, $tstampEnd);
		$maxSum = ($maxFeusers + $maxTtaddress);

		switch($record_type) {
			case 'fe_user':
				if($maxFeusers >= $no_of_records){
					$getFeusers = $no_of_records;
					$getTt_address = 0;
				} else {
					$error = $GLOBALS['LANG']->getLL('err_feUserRecords');
				}
				break;
			case 'tt_address':
				if($maxTtaddress >=$no_of_records){
					$getTt_address = $no_of_records;
					$getFeusers = 0;
				} else {
					$error = $GLOBALS['LANG']->getLL('err_ttAddressRecords');
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
					$error = $GLOBALS['LANG']->getLL('err_toFewRecords');
				}
				break;
		}

		$htmlOutput .= $this->getSQLDataToTable('fe_users', 'disable', $uid, $getFeusers, 'crdate', $tstampBegin, $tstampEnd);
		$htmlOutput .= $this->getSQLDataToTable('tt_address', 'hidden', $uid, $getTt_address, 'tstamp', $tstampBegin, $tstampEnd);

		$htmlOutput .= '</tbody></table><input type="submit" name="save" id="bt_save" value="Speichern" />';

		if(!$error) {
			$result = $htmlOutput;
		} else {
			$message = t3lib_div::makeInstance(
				't3lib_FlashMessage',
				$error,
				$GLOBALS['LANG']->getLL('err_headerInsert'),
				t3lib_FlashMessage::ERROR,
				FALSE
			);
			$result = $message->render();
		}

		return $result;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$table: ...
	 * @param	[type]		$hidden: ...
	 * @param	[type]		$uid: ...
	 * @param	[type]		$create: ...
	 * @param	[type]		$period_begin: ...
	 * @param	[type]		$period_end: ...
	 * @return	[type]		...
	 */
	public function getMaxRecords($table, $hidden, $uid, $create = '', $period_begin = '', $period_end = ''){
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		if(!$period_begin && !$period_end){ //both fields empty
			$sqlWhere = '';
		} else if($period_begin && !$period_end) { //period_begin filled, period_end empty
			$sqlWhere = ' AND ' . $create . ' >= ' . $period_begin . '';
		} else if (!$period_begin && $period_end) { //period_begin empty, period_end filled
			$sqlWhere = ' AND ' . $create . ' <= ' . $period_end . '';
		} else if($period_begin && $period_end) { //both fields filled
			$sqlWhere = ' AND ' . $create . ' >= ' . $period_begin . ' AND ' . $create . ' <= ' . $period_end . '';
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*)',
			$table,
			'deleted = 0 AND ' . $hidden . ' = 0 AND tx_jheprizedraw_prize_draw_winner = 0 AND pid = ' . $uid . $sqlWhere
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);

		return $row['0'];
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$table: ...
	 * @param	[type]		$hidden: ...
	 * @param	[type]		$uid: ...
	 * @param	[type]		$noOfRecords: ...
	 * @param	[type]		$create: ...
	 * @param	[type]		$tstampBegin: ...
	 * @param	[type]		$tstampEnd: ...
	 * @return	[type]		...
	 */
	public function getSQLDataToTable ($table, $hidden, $uid, $noOfRecords , $create = '', $tstampBegin = '', $tstampEnd = '') {

		if(!$tstampBegin && !$tstampEnd){
			$sqlWhereTime = '';
		} else if ($tstampBegin && !$tstampEnd) {
			$sqlWhereTime = ' AND ' . $create . ' >= ' . $tstampBegin;
		} else if (!$tstampBegin && $tstampEnd) {
			$sqlWhereTime = ' AND ' . $create . ' <= ' . $tstampEnd;
		} else if ($tstampBegin && $tstampEnd) {
			$sqlWhereTime = ' AND ' . $create . ' >= ' . $tstampBegin . ' AND ' . $create . ' <= ' . $tstampEnd;
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,name, address, zip, city, email',
			$table,
			'deleted = 0 AND ' . $hidden . ' = 0 AND tx_jheprizedraw_prize_draw_winner = 0 AND pid = ' . $uid . $sqlWhereTime . '',
			'',
			'RAND()',
			$noOfRecords
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$email = tslib_cObj::getMailTo($row['email'],$row['email']);

			$htmlOutput .= '
								<tr>
									<td>' . $row['name'] . '</td>
									<td>' . $row['address'] . '</td>
									<td>' . $row['zip'] . ' ' . $row['city'] . '</td>
									<td><a href="' . $email[0] . '">' . $email[1] . '</a></td>
									<td align="center"><input type="checkbox" name="update" id="update" value="' . $row['uid'] . '|' . $table . '" /></td>
								</tr>';
		}

		return $htmlOutput;
	}
}

$output = t3lib_div::makeInstance('tx_jheprizedraw_submit');
echo $output->main();

?>