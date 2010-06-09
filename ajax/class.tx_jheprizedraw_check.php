<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Julian Kleinhans <typo3@kj187.de>
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

class tx_jheprizedraw_check {

	/**
	 * Main Methode
	 *
	 * @return string
	 */
	public function main() {      
		$data = $_GET['data'];
		$type = $_GET['type'];
		$result = '';
		
		switch($type) {
			case 'number':
				if (!$data){
					$result = "Bitte watt eingeben!";
				} else if (!is_numeric($data)) {
					$result = "Dat is doch keine Zahl!";
				}
				break;
			case 'select':
				if (!$data){
					$result = "Bitte watt auswählen!";
				} 
				break;
			case 'date':
				$date = explode("-", $data);
				$day = $date[0];
				$month = $date[1];
				$year = $date[2];
				if ($data && !preg_match("/^[0123]?\d\-[01]?\d\-\d{4}$/", $data)){
					$result = "Falsches Datumsformat!";
				} else if ($data && !checkdate($month, $day, $year)) {
					$result = "Datum existiert nicht!";
				} 
				break;
			case '':
				$result = 'O-o! No data-type!';
				break;
		}
			
		return $result;
	}
}

$output = t3lib_div::makeInstance('tx_jheprizedraw_check');
echo $output->main();

?>