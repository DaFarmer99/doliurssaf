<?php
/*
 * Copyright (C) 2021 Denieul Guillaume <guillaume.denieul@ordilogique.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   urssaf     Module URSSAF
 *  \brief      URSSAF module descriptor.
 *
 *  \file       htdocs/urssaf/core/modules/modURSSAF.class.php
 *  \ingroup    urssaf
 *  \brief      Description and activation file for module URSSAF
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 *  Description and activation class for module URSSAF
 */
class moddoliurssaf extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;
		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 888889; // TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve an id number for your module

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'doliurssaf';

		// Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
		// It is used to group modules by family in module setup page
		$this->family = "financial";

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';

		// Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));
		// Module label (no space allowed), used if translation string 'ModuleURSSAFName' not found (URSSAF is name of module).
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// Module description, used if translation string 'ModuleURSSAFDesc' not found (URSSAF is name of module).
		$this->description = "Module simplifiant la déclaration URSSAF trimestrielle";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "Module simplifiant la déclaration URSSAF trimestrielle. Ce module contient deux tableaux (un mensuel et un trimestriel).";

		// Author
		$this->editor_name = 'Guillaume DENIEUL';
		$this->editor_url = 'https://www.ordilogique.fr';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'
		$this->version = '2024.11';
		// Url to the file with your last numberversion of this module
		//$this->url_last_version = 'http://www.example.com/versionmodule.txt';

		// Key used in llx_const table to save module status enabled/disabled (where URSSAF is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		// To use a supported fa-xxx css style of font awesome, use this->picto='xxx'
		//$this->picto = 'urssaf';
		$this->picto='doliurssaf@doliurssaf';
		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 0,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 0,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 0,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
			'css' => array(
			//	    '/urssaf/css/urssaf.css',
			),
			// Set this to relative path of js file if module must load a js on all pages
			'js' => array(
				//   '/urssaf/js/urssaf.js.php',
			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			'hooks' => array(
				//   'data' => array(
				//       'hookcontext1',
				//       'hookcontext2',
				//   ),
				//   'entity' => '0',
			),
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/urssaf/temp","/urssaf/subdir");
		$this->dirs = array("/doliurssaf/temp");

		// Config pages. Put here list of php page, stored into urssaf/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@doliurssaf");

		// Dependencies
		// A condition to hide module
		$this->hidden = false;
		// List of module class names as string that must be enabled if this module is enabled. Example: array('always1'=>'modModuleToEnable1','always2'=>'modModuleToEnable2', 'FR1'=>'modModuleToEnableFR'...)
		$this->depends = array();
		$this->requiredby = array(); // List of module class names as string to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
		$this->conflictwith = array(); // List of module class names as string this module is in conflict with. Example: array('modModuleToDisable1', ...)

		// The language file dedicated to your module
		$this->langfiles = array("doliurssaf@doliurssaf");

		// Prerequisites
		$this->phpmin = array(5, 6); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(11, -3); // Minimum version of Dolibarr required by module

		// Messages at activation
		$this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','ES'='textes'...)
		$this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','ES'='textes'...)
		//$this->automatic_activation = array('FR'=>'URSSAFWasAutomaticallyActivatedBecauseOfYourCountryChoice');
		//$this->always_enabled = true;								// If true, can't be disabled

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(1 => array('URSSAF_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
		//                             2 => array('URSSAF_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
		// );
		$this->const = array();

		// Some keys to add into the overwriting translation tables
		/*$this->overwrite_translation = array(
			'en_US:ParentCompany'=>'Parent company or reseller',
			'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
		)*/

		if (!isset($conf->doliurssaf) || !isset($conf->doliurssaf->enabled)) {
			$conf->doliurssaf = new stdClass();
			$conf->doliurssaf->enabled = 0;
		}

		// Array to add new pages in new tabs
		//$this->tabs = array();
		$this->tabs = array();

		// Dictionaries
		$this->dictionaries = array();
	
		// Boxes/Widgets
		// Add here list of php file(s) stored in urssaf/core/boxes that contains a class to show a widget.
		$this->boxes = array(
		);

		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		$this->cronjobs = array(
	
		);


		// Permissions provided by this module
		$this->rights = array();
		$r = 0;
		// Add here entries to declare new permissions
	
		// Main menu entries to add
		$this->menu = array();
		$r = 0;
		// Add here entries to declare new menus
		/* BEGIN MODULEBUILDER TOPMENU */
		$this->menu[$r++] = array(
			'fk_menu'=>'', // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'top', // This is a Top menu entry
			'titre'=>'URSSAF',
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'doliurssaf',
			'leftmenu'=>'',
			'url'=>'/doliurssaf/doliurssafindex.php',
			'langs'=>'doliurssaf@doliurssaf', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000 + $r,
			'enabled'=>'$conf->doliurssaf->enabled', // Define condition to show or hide menu entry. Use '$conf->urssaf->enabled' if entry must be visible if module is enabled.
			'perms'=>'1', // Use 'perms'=>'$user->rights->urssaf->test->read' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2, // 0=Menu for internal users, 1=external users, 2=both
		);
		$r = 1;
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;

		$result = $this->_load_tables('/doliurssaf/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}

		// Permissions
		$this->remove($options);

		$sql = array();
		$sql = array_merge($sql, array(
"DROP TABLE IF EXISTS ".MAIN_DB_PREFIX."custom_urssaf",
					"CREATE TABLE ".MAIN_DB_PREFIX."custom_urssaf (`rowid` int(11) NOT NULL,`dates` datetime NOT NULL DEFAULT current_timestamp(),`year` year(4) NOT NULL,`quarter` int(1) NOT NULL,`tx_518` float NOT NULL,`tx_508` float NOT NULL,`tx_520` float NOT NULL,`tx_510` float NOT NULL,`tx_572` float NOT NULL,`tx_060` float NOT NULL,`tx_061` float NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
					"INSERT INTO ".MAIN_DB_PREFIX."custom_urssaf (`rowid`, `dates`, `year`, `quarter`, `tx_518`, `tx_508`, `tx_520`, `tx_510`, `tx_572`, `tx_060`, `tx_061`) VALUES
					(1, '2024-11-06 14:21:41', '2024', 1, 21.2, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
					(2, '2024-11-06 14:21:41', '2024', 2, 21.2, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
		(3, '2024-11-06 14:21:41', '2024', 3, 21.2, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
		(4, '2024-11-06 14:21:41', '2024', 4, 21.2, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
		(5, '2024-11-06 14:21:41', '2023', 1, 21.2, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
		(6, '2024-11-06 14:21:41', '2023', 2, 21.2, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
		(7, '2024-11-06 14:21:41', '2023', 3, 21.2, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
		(8, '2024-11-06 14:21:41', '2023', 4, 21.2, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
		(10, '2024-11-06 14:21:41', '2022', 1, 22, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
		(11, '2024-11-06 14:21:41', '2022', 2, 22, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
		(12, '2024-11-06 14:21:41', '2022', 3, 22, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
		(13, '2024-11-06 14:21:41', '2022', 4, 21.2, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
		(14, '2024-11-06 14:21:41', '2021', 1, 23.7, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(15, '2024-11-06 14:21:41', '2021', 2, 23.7, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(16, '2024-11-06 14:21:41', '2021', 3, 23.7, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(17, '2024-11-06 14:21:41', '2021', 4, 23.7, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(18, '2024-11-06 14:21:41', '2020', 1, 23.7, 12.3, 0, 1, 0.3, 0.22, 0),
		(19, '2024-11-06 14:21:41', '2020', 2, 23.7, 12.3, 0, 1, 0.3, 0.22, 0),
		(20, '2024-11-06 14:21:41', '2020', 3, 23.7, 12.3, 0, 1, 0.3, 0.22, 0),
		(21, '2024-11-06 14:21:41', '2020', 4, 23.7, 12.3, 0, 1, 0.3, 0.22, 0),
		(22, '2024-11-06 14:21:41', '2019', 1, 23.7, 12.3, 0, 1, 0.3, 0.22, 0),
		(23, '2024-11-06 14:21:41', '2019', 2, 23.7, 12.3, 0, 1, 0.3, 0.22, 0),
		(24, '2024-11-06 14:21:41', '2019', 3, 23.7, 12.3, 0, 1, 0.3, 0.22, 0),
		(25, '2024-11-06 14:21:41', '2019', 4, 23.7, 12.3, 0, 1, 0.3, 0.22, 0),
		(26, '2024-11-06 14:21:41', '2018', 1, 23.7, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(27, '2024-11-06 14:21:41', '2018', 2, 22, 12.3, 0, 1, 0.3, 0.22, 0),
		(28, '2024-11-06 14:21:41', '2018', 3, 22, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(29, '2024-11-06 14:21:41', '2018', 4, 23.7, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(30, '2024-11-06 14:21:41', '2017', 1, 24.4, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(31, '2024-11-06 14:21:41', '2017', 2, 24.4, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(32, '2024-11-06 14:21:41', '2017', 3, 24.4, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(33, '2024-11-06 14:21:41', '2017', 4, 24.4, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(34, '2024-11-06 14:21:41', '2016', 1, 24.8, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(35, '2024-11-06 14:21:41', '2016', 2, 24.8, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(36, '2024-11-06 14:21:41', '2016', 3, 24.8, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(37, '2024-11-06 14:21:41', '2016', 4, 24.8, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(38, '2024-11-06 14:21:41', '2015', 1, 24.6, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(39, '2024-11-06 14:21:41', '2015', 2, 24.6, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(40, '2024-11-06 14:21:41', '2015', 3, 24.6, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(41, '2024-11-06 14:21:41', '2015', 4, 24.6, 12.3, 0, 1, 0.3, 0.22, 0.48),
		(42, '2024-11-06 14:21:41', '2014', 1, 26.3, 12.3, 0, 1, 0.3, 0.22, 0),
		(43, '2024-11-06 14:21:41', '2014', 2, 26.3, 12.3, 0, 1, 0.3, 0.22, 0),
		(44, '2024-11-06 14:21:41', '2014', 3, 26.3, 12.3, 0, 1, 0.3, 0.22, 0),
		(45, '2024-11-06 14:21:41', '2014', 4, 26.3, 12.3, 0, 1, 0.3, 0.22, 0),
		(46, '2024-11-06 14:21:41', '2013', 1, 26.3, 12.3, 0, 1, 0.3, 0.22, 0),
		(47, '2024-11-06 14:21:41', '2013', 2, 26.3, 12.3, 0, 1, 0.3, 0.22, 0),
		(48, '2024-11-06 14:21:41', '2013', 3, 26.3, 12.3, 0, 1, 0.3, 0.22, 0),
		(49, '2024-11-06 14:21:41', '2013', 4, 26.3, 12.3, 0, 1, 0.3, 0.22, 0),
		(50, '2024-11-06 14:21:41', '2012', 1, 23, 12.3, 0, 1, 0.3, 0.22, 0),
		(51, '2024-11-06 14:21:41', '2012', 2, 23, 12.3, 0, 1, 0.3, 0.22, 0),
		(52, '2024-11-06 14:21:41', '2012', 3, 23, 12.3, 0, 1, 0.3, 0.22, 0),
		(53, '2024-11-06 14:21:41', '2012', 4, 23, 12.3, 0, 1, 0.3, 0.22, 0),
		(54, '2024-11-06 14:21:41', '2011', 1, 23, 12.3, 0, 1, 0.3, 0.22, 0),
		(55, '2024-11-06 14:21:41', '2011', 2, 23, 12.3, 0, 1, 0.3, 0.22, 0),
		(56, '2024-11-06 14:21:41', '2011', 3, 23, 12.3, 0, 1, 0.3, 0.22, 0),
		(57, '2024-11-06 14:21:41', '2011', 4, 23, 12.3, 0, 1, 0.3, 0.22, 0),
		(58, '2024-11-06 14:21:41', '2010', 1, 23, 12.3, 0, 1, 0, 0.22, 0),
		(59, '2024-11-06 14:21:41', '2010', 2, 23, 12.3, 0, 1, 0, 0.22, 0),
		(60, '2024-11-06 14:21:41', '2010', 3, 23, 12.3, 0, 1, 0, 0.22, 0),
		(61, '2024-11-06 14:21:41', '2010', 4, 23, 12.3, 0, 1, 0, 0.22, 0),
		(62, '2024-11-06 14:21:41', '2009', 1, 23, 12.3, 0, 1, 0, 0.22, 0),
		(63, '2024-11-06 14:21:41', '2009', 2, 23, 12.3, 0, 1, 0, 0.22, 0),
		(64, '2024-11-06 14:21:41', '2009', 3, 23, 12.3, 0, 1, 0, 0.22, 0),
		(65, '2024-11-06 14:21:41', '2009', 4, 23, 12.3, 0, 1, 0, 0.22, 0),
		(66, '2024-11-06 14:21:41', '2008', 1, 23, 12.3, 0, 1, 0, 0.22, 0),
		(67, '2024-11-06 14:21:41', '2008', 2, 23, 12.3, 0, 1, 0, 0.22, 0),
		(68, '2024-11-06 14:21:41', '2008', 3, 23, 12.3, 0, 1, 0, 0.22, 0),
		(69, '2024-11-06 14:21:41', '2008', 4, 23, 12.3, 0, 1, 0, 0.22, 0);",
		"ALTER TABLE `".MAIN_DB_PREFIX."custom_urssaf`  ADD PRIMARY KEY (`rowid`);",
		"ALTER TABLE `".MAIN_DB_PREFIX."custom_urssaf`  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;"));
		
		
		// Document templates
		$moduledir = 'doliurssaf';
		$myTmpObjects = array();
		

		foreach ($myTmpObjects as $myTmpObjectKey => $myTmpObjectArray) {
			
			if ($myTmpObjectArray['includerefgeneration']) {
				$src = DOL_DOCUMENT_ROOT.'/install/doctemplates/doliurssaf/template_tests.odt';
				$dirodt = DOL_DATA_ROOT.'/doctemplates/doliurssaf';
				$dest = $dirodt.'/template_tests.odt';

				if (file_exists($src) && !file_exists($dest)) {
					require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
					dol_mkdir($dirodt);
					$result = dol_copy($src, $dest, 0, 0);
					if ($result < 0) {
						$langs->load("errors");
						$this->error = $langs->trans('ErrorFailToCopyFile', $src, $dest);
						return 0;
					}
				}

				$sql = array_merge($sql, array(
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'standard_".strtolower($myTmpObjectKey)."' AND type = '".strtolower($myTmpObjectKey)."' AND entity = ".$conf->entity,
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('standard_".strtolower($myTmpObjectKey)."','".strtolower($myTmpObjectKey)."',".$conf->entity.")",
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'generic_".strtolower($myTmpObjectKey)."_odt' AND type = '".strtolower($myTmpObjectKey)."' AND entity = ".$conf->entity,
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('generic_".strtolower($myTmpObjectKey)."_odt', '".strtolower($myTmpObjectKey)."', ".$conf->entity.")"
				));
			}
		}

		return $this->_init($sql, $options);
		
		// Création table nécessaire pour les taux
	}


	public function remove($options = '')
	{
		$query="DROP TABLE IF EXISTS `".MAIN_DB_PREFIX."custom_urssaf`;";
		
		$sql = array();
		$sql = array_merge($sql, array(
		"DROP TABLE IF EXISTS `".MAIN_DB_PREFIX."custom_urssaf`;"));
		return $this->_remove($sql, $options);
	}
}
