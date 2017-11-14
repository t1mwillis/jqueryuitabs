<?php

/**
 * jQuery UI Tabs Widget for Contao Open Source CMS
 *
 * @copyright wangaz. GbR 2015 - 2016
 * @author wangaz. GbR <hallo@wangaz.com>
 * @link https://wangaz.com
 * @license http://creativecommons.org/licenses/by-sa/4.0/ CC BY-SA 4.0
 */


/*
 * Config
 */
$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = function($dc) 
{
		if ($_POST || Input::get('act') != 'edit')
			return;
		
		$objUser = \BackendUser::getInstance();
		
		if ( ! $objUser->hasAccess('themes', 'modules') ||  ! $objUser->hasAccess('layout', 'themes'))
			return;
		
		$objCte = ContentModel::findByPk($dc->id);
		
		if ($objCte === null)
			return;
		
		switch ($objCte->type)
		{
			case 'juiTabStart':
			case 'juiTabSeparator':
			case 'juiTabStop':
				Message::addInfo(sprintf($GLOBALS['TL_LANG']['tl_content']['includeTemplate'], 'j_ui_tabs'));
				break;
		}
};


/*
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'juiTabShowDropdown';
 
array_insert($GLOBALS['TL_DCA']['tl_content']['palettes'], 0, array(
	'juiTabStart'		=> '{type_legend},type;{juiTab_legend},juiTabHeadline,juiTabAlias,juiTabShowDropdown;{image_legend},addImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop',
	'juiTabSeparator'	=> '{type_legend},type;{juiTab_legend},juiTabHeadline,juiTabAlias;{image_legend},addImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests;{invisible_legend:hide},invisible,start,stop',
	'juiTabStop'		=> '{type_legend},type;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests;{invisible_legend:hide},invisible,start,stop',
));


/*
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['juiTabShowDropdown'] = 'juiTabDropdownLabel';


/*
 * Fields
 */
array_insert($GLOBALS['TL_DCA']['tl_content']['fields'], 0, array(
	'juiTabHeadline' => array(
		'label'			=> &$GLOBALS['TL_LANG']['tl_content']['juiTabHeadline'],
		'exclude'		=> true,
		'inputType'		=> 'inputUnit',
		'options'		=> array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'),
		'eval'			=> array('maxlength' => 255, 'allowHtml' => true, 'mandatory' => true, 'tl_class' => 'w50'),
		'sql'			=> "varchar(255) NOT NULL default ''",
	),
	'juiTabAlias' => array(
		'label'			=> &$GLOBALS['TL_LANG']['tl_content']['juiTabAlias'],
		'exclude'		=> true,
		'inputType'		=> 'text',
		'eval'			=> array('rgxp' => 'alias', 'maxlength' => 128, 'tl_class' => 'w50'),
		'save_callback'	=> array(function($varValue, DataContainer $dc) 
		{
			if ($varValue == '') {
				$arrHeadline = deserialize($dc->activeRecord->juiTabHeadline);
				$varValue = standardize(StringUtil::restoreBasicEntities($arrHeadline['value']));
				$varValue = preg_replace('/^id-/', '', $varValue);
			}
				
			return $varValue;
		}),
		'sql'			=> "varchar(128) NOT NULL default ''",
	),
	'juiTabShowDropdown' => array(
		'label'			=> &$GLOBALS['TL_LANG']['tl_content']['juiTabShowDropdown'],
		'exclude'		=> true,
		'inputType'		=> 'checkbox',
		'eval'			=> array('submitOnChange' => true, 'tl_class' => 'w50 m12'),
		'sql'			=> "char(1) NOT NULL default ''",
	),
	'juiTabDropdownLabel' => array(
		'label'			=> &$GLOBALS['TL_LANG']['tl_content']['juiTabDropdownLabel'],
		'exclude'		=> true,
		'inputType'		=> 'text',
		'eval'			=> array('maxlength' => 256, 'allowHtml' => true, 'tl_class' => 'w50'),
		'sql'			=> "varchar(256) NOT NULL default ''",
	),
	)
);
