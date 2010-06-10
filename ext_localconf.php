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

if (!defined('TYPO3_MODE')) die ('Access denied.');

// ajaxID Registrierung
$TYPO3_CONF_VARS['BE']['AJAX']['tx_jheprizedraw::check']= t3lib_extMgm::extPath($_EXTKEY) . 'ajax/class.tx_jheprizedraw_check.php:tx_jheprizedraw_check->main';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_jheprizedraw::submit']= t3lib_extMgm::extPath($_EXTKEY) . 'ajax/class.tx_jheprizedraw_submit.php:tx_jheprizedraw_submit->main';
?>