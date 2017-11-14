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
	'juiTabStart'		=> '{type_legend},type;{juiTab_legend},juiTabHeadline,juiTabAlias,juiTabShowDropdown;{image_legend},juiTabImg;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop',
	'juiTabSeparator'	=> '{type_legend},type;{juiTab_legend},juiTabHeadline,juiTabAlias;{image_legend},juiTabImg;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests;{invisible_legend:hide},invisible,start,stop',
	'juiTabStop'		=> '{type_legend},type;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests;{invisible_legend:hide},invisible,start,stop',
));


/*
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['juiTabShowDropdown'] = 'juiTabDropdownLabel';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['juiTabImg'] = 'singleSRC,alt,title,size,imagemargin,imageUrl,fullsize,caption,floating';


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
	'addImage' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['juiTabImg'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'singleSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'mandatory'=>true, 'tl_class'=>'clr'),
			'load_callback' => array
			(
				array('tl_content', 'setSingleSrcFlags')
			),
			'save_callback' => array
			(
				array('tl_content', 'storeFileMetaInformation')
			),
			'sql'                     => "binary(16) NULL"
		),
		'alt' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['alt'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['title'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'size' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['size'],
			'exclude'                 => true,
			'inputType'               => 'imageSize',
			'options'                 => System::getImageSizes(),
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'imagemargin' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['imagemargin'],
			'exclude'                 => true,
			'inputType'               => 'trbl',
			'options'                 => $GLOBALS['TL_CSS_UNITS'],
			'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'imageUrl' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['imageUrl'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'url', 'decodeEntities'=>true, 'maxlength'=>255, 'fieldType'=>'radio', 'filesOnly'=>true, 'tl_class'=>'w50 wizard'),
			'wizard' => array
			(
				array('tl_content', 'pagePicker')
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'fullsize' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['fullsize'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'caption' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['caption'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'allowHtml'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'floating' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['floating'],
			'default'                 => 'above',
			'exclude'                 => true,
			'inputType'               => 'radioTable',
			'options'                 => array('above', 'left', 'right', 'below'),
			'eval'                    => array('cols'=>4, 'tl_class'=>'w50'),
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'sql'                     => "varchar(32) NOT NULL default ''"
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
