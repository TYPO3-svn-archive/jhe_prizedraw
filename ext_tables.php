<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModulePath('web_txjheprizedrawM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	t3lib_extMgm::addModule('web', 'txjheprizedrawM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}

$tempColumns = array (
	'tx_jheprizedraw_prize_draw_winner' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:jhe_prizedraw/locallang_db.xml:fe_users.tx_jheprizedraw_prize_draw_winner',		
		'config' => array (
			'type'     => 'input',
			'size'     => '12',
			'max'      => '20',
			'eval'     => 'datetime',
			'checkbox' => '0',
			'default'  => '0'
		)
	),
);


t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('fe_users','tx_jheprizedraw_prize_draw_winner;;;;1-1-1');

$tempColumns = array (
	'tx_jheprizedraw_prize_draw_winner' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:jhe_prizedraw/locallang_db.xml:tt_address.tx_jheprizedraw_prize_draw_winner',		
		'config' => array (
			'type'     => 'input',
			'size'     => '12',
			'max'      => '20',
			'eval'     => 'datetime',
			'checkbox' => '0',
			'default'  => '0'
		)
	),
);


t3lib_div::loadTCA('tt_address');
t3lib_extMgm::addTCAcolumns('tt_address',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tt_address','tx_jheprizedraw_prize_draw_winner;;;;1-1-1');
?>