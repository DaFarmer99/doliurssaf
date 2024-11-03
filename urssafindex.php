<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
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
 *	\file       urssaf/urssafindex.php
 *	\ingroup    urssaf
 *	\brief      Home page of urssaf top menu
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

// Load translation files required by the page
$langs->loadLangs(array("urssaf@urssaf"));
$month_names = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
$action = GETPOST('action', 'aZ09');
$year=GETPOST("year",'int');
$vue=GETPOST("vue",'int');

if (empty($year))
{
	$year = strftime("%Y",dol_now());
}
if (empty($vue))
{
	$vue = 1;
}

$yearm1=$year-1;
$yearp1=$year+1;



$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}

$max = 5;

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("", $langs->trans("Déclaration URSSAF"));

print load_fiche_titre($langs->trans("Déclaration URSSAF"), '', 'urssaf.png@urssaf');

if ($vue == 1)
{
	$vue_texte = "Trimestrielle";
	$alt_vue_texte = "Mensuelle";
	$vue_alter = 2;
}
else
{
	$vue_texte = "Mensuelle";
	$alt_vue_texte = "Trimestrielle";
	$vue_alter = 1;
}
$vue_form='<br><table><tr><td><b>Vue</b></td><td><b>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td><td><b>'.$vue_texte.'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/dolibarr/custom/urssaf/urssafindex.php?vue='.$vue_alter.'&year='.$year.'">'.$alt_vue_texte.'</a><br></td></tr>';
$year_form='<tr><td><b>Selection de l\'année </td><td><b>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td><td><a href="/dolibarr/custom/urssaf/urssafindex.php?vue='.$vue.'&year='.$yearm1.'">&lt;&lt;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$year.'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/dolibarr/custom/urssaf/urssafindex.php?vue='.$vue.'&year='.$yearp1.'">&gt;&gt;</a></td></tr></table><br><br>';

print $vue_form;
print $year_form;
print '<div class="fichecenter"><div class="fichethirdleft">';



	$sql = "SELECT datep , amount";
	$sql.= " FROM ".MAIN_DB_PREFIX."paiement";
	$sql.= " WHERE datep between '".$year."/01/01 00:00:00' and '".$year."/12/31 23:59:59'";
	$resql = $db->query($sql);
	


	if ($resql)
	{
		$trim[] = 0;
		$mens[] = 0;
		

		
		$num = $db->num_rows($resql);

		
		if ($num > 0)
		{
			$i = 0;
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$month=date("n", strtotime($obj->datep));
				
				switch($month)
				{
					case "01":
					case "02":
					case "03":
						$trim[0] += $obj->amount;
						break;
					case "04":
					case "05":
					case "06":
						$trim[1] += $obj->amount;
						break;
					case "07":
					case "08":
					case "09":
						$trim[2] += $obj->amount;
						break;
					case "10":
					case "11":
					case "12":
						$trim[3] += $obj->amount;
						break;
					
				}
				$mens[$month] += $obj->amount;
				$i++;
			}
	
			
			$sql_four = "SELECT datep , amount";
			$sql_four.= " FROM ".MAIN_DB_PREFIX."paiementfourn";
			$sql_four.= " WHERE datep between '".$year."/01/01 00:00:00' and '".$year."/12/31 23:59:59'";
			$resql_four = $db->query($sql_four);
			
			
			$trim_four[] = 0;
			$mens_four[] = 0;	
			
			if ($resql_four)
			{
				$num_four = $db->num_rows($resql_four);	
				$i = 0;
				while ($i < $num_four)
				{
					$obj_four = $db->fetch_object($resql_four);
					$month=date("n", strtotime($obj_four->datep));
				
					switch($month)
					{
						case "01":
						case "02":
						case "03":
							$trim_four[0] += $obj_four->amount;
							break;
						case "04":
						case "05":
						case "06":
							$trim_four[1] += $obj_four->amount;
							break;
						case "07":
						case "08":
						case "09":
							$trim_four[2] += $obj_four->amount;
							break;
						case "10":
						case "11":
						case "12":
							$trim_four[3] += $obj_four->amount;
							break;	
					}
					$mens_four[$month] += $obj_four->amount;
					$i++;
				
				}
			}
			
			
			/**************************************************************************************************/
			/* Liste des taxes applicables au régime MICRO	NOUVEAUX TAUX OCTOBRE 2022     22.9				  */
			/**************************************************************************************************/
			/* 645 - Prestations de services (BIC) et versement liberatoire de l'impot sur le revenu  22,90 % */	
			/* 630 - Vente de marchandises (BIC)   et versement liberatoire de l'impot sur le revenu  13,80 % */	
			/* 684 - Prestations de services (BNC) et versement liberatoire de l'impot sur le revenu  24,20 % */	
			/* 572 - Formation artisan obligatoire													   0,30 % */	
			/* 060 - Taxe CMA vente																       0,22 % */	
			/* 061 - Taxe CMA prestations															   0,48 % */
			/**************************************************************************************************/
			/**************************************************************************************************/
			/* Liste des taxes applicables au régime MICRO			2024									  */
			/**************************************************************************************************/
			/* 518 - Prestations de services (BIC) 													  23,20%  */
			/* 520 - Versement liberatoire de l'impot sur le revenu (Prestations BIC) 					1,70% */
			/* 572 - Formation artisan obligatoire							   							0,30 % */	
			/* 061 - Taxe CMA prestations								  								0,48 % */
			/**************************************************************************************************/			
			switch(true)
			{
				case $year  == "2008":
				case $year  == "2009":
				case $year  == "2010":
				case $year  == "2011":
				case $year  == "2012":
				case $year  == "2013":
				case $year  == "2014":
				case $year  == "2015":
				case $year  == "2016":
					$tx1=25.1;
					$tx1_1=23.4;
					$tx1_2=1.7;
					$tx2=0.30;
					$tx3=0.00;
					break;
					
				case $year  == "2017":
					$tx1=24.4;
					$tx1_1=22.7;
					$tx1_2=1.7;
					$tx2=0.30;
					$tx3=0.48;
					break;
					
				case $year  == "2019":
				case $year  == "2020":
					$tx1=23.7;
					$tx1_1=22;
					$tx1_2=1.7;
					$tx2=0.30;
					$tx3=0.00;
					break;

				case $year  == "2018":
					switch($month)
					{
						case "01":
						case "02":
						case "03":
							$tx1=23.7;
							$tx1_1=22;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;						
						case "04":
						case "05":
						case "06":
							$tx1=22.0;
							$tx1_1=22;
							$tx1_2=0.0;
							$tx2=0.30;
							$tx3=0.00;
							break;						
						case "07":
						case "08":
						case "09":
							$tx1=22.0;
							$tx1_1=22;
							$tx1_2=0.0;
							$tx2=0.30;
							$tx3=0.48;
							break;
						case "10":
						case "11":
						case "12":
							$tx1=23.7;
							$tx1_1=22.0;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;
					}
					break;
					
				case $year  == "2021":
					$tx1=23.7;
					$tx1_1=22;
					$tx1_2=1.7;
					$tx2=0.30;
					$tx3=0.48;
					break;
					
				case $year  == "2022":
					switch($month)
					{
						case "01":
						case "02":
						case "03":
						case "04":
						case "05":
						case "06":
						case "07":
						case "08":
						case "09":
							$tx1=23.7;
							$tx1_1=22;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;
						case "10":
						case "11":
						case "12":
							$tx1=22.9;
							$tx1_1=21.2;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;
					}
					break;
					
				case $year  == "2023":
					$tx1=22.9;
					$tx1_1=21.2;
					$tx1_2=1.7;
					$tx2=0.30;
					$tx3=0.48;
					break;
				
				case $year  == "2024":
					switch($month)
					{
						case "01":
						case "02":
						case "03":
						case "04":
						case "05":
						case "06":
						case "07":
						case "08":
						case "09":
							$tx1=22.9;
							$tx1_1=21.2;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;
						case "10":
						case "11":
						case "12":
							$tx1=24.9;
							$tx1_1=23.2;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
						break;
					}
					break;

				case $year  > "2024":
						$tx1=24.9;
						$tx1_1=23.2;
						$tx1_2=1.7;
						$tx2=0.30;
						$tx3=0.48;
						break;
			}
			
			$tot_an_CA=0.0;
			$tot_an_tax1=0.0;
			$tot_an_tax2=0.0;
			$tot_an_tax3=0.0;
			$tot_an_four=0.0;
			
			print '<table class="noborder centpercent">';
			print '<tr class="liste_titre">';
			
			if($vue == 2)
			{
				print '<th class="right"></th><th class="center">CA<br>Mensuel</th><th class="right">Presta.(BIC)<br><font size=-2><i>'.$tx1.' %</i></font></th><th class="right">Forma.(CMA)<br><font size=-2><i>'.$tx2.' %</i></font></th><th class="right">Presta.(CMA)<br><font size=-2><i>'.$tx3.' %</i></font></th><th class="right">Total Taxes</th><th class="right">Achats</th><th class="right">CA Net</th></tr>';

				for($i=1; $i<13;$i++)
				{
					if ($year  == "2018")
					{
						switch($i)
						{
						case 1:
						case 2:
						case 3:
							$tx1=23.7;
							$tx1_1=22;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;						
						case 4:
						case 5:
						case 6:
							$tx1=22.0;
							$tx1_1=22;
							$tx1_2=0.0;
							$tx2=0.30;
							$tx3=0.00;
							break;						
						case 7:
						case 8:
						case 9:
							$tx1=22.0;
							$tx1_1=22;
							$tx1_2=0.0;
							$tx2=0.30;
							$tx3=0.48;
							break;
						case 10:
						case 11:
						case 12:
							$tx1=23.7;
							$tx1_1=22.0;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;
						}
					}
					if ($year  == "2022")
					{
						switch($i)
						{
						case 1:
						case 2:
						case 3:
						case 4:
						case 5:
						case 6:
						case 7:
						case 8:
						case 9:
							$tx1=23.7;
							$tx1_1=22;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;
						case 10:
						case 11:
						case 12:
							$tx1=22.9;
							$tx1_1=21.2;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;
						}
					}
					if ($year  == "2023")
					{
							$tx1=22.9;
							$tx1_1=21.2;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
					}
					if ($year  == "2024")
					{
							$tx1=23.7;
							$tx1_1=22;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
					}
					
					$tax1=round($mens[$i]*$tx1_1/100)+round($mens[$i]*$tx1_2/100);
					$tax2=round($mens[$i]*$tx2/100);
					$tax3=round($mens[$i]*$tx3/100);
					$tot_an_tax1+=$tax1;
					$tot_an_tax2+=$tax2;
					$tot_an_tax3+=$tax3;
					$tot_an_CA+=$mens[$i];
					$tot_an_four+=$mens_four[$i];
					print '<tr class="oddeven"><td class="left">'.$month_names[$i-1].'</td><td colspan="1" class="right"><b>'.price($mens[$i]).'</b></td><td class="right">'.round($tax1).'</td><td class="right">'.round($tax2).'</td><td class="right">'.round($tax3).'</td><td class="right"><b>'.round($tax1+$tax2+$tax3).'</b></td><td class="right">'.price($mens_four[$i]).'</td><td class="right"><i>'.price($mens[$i]-$tax1-$tax2-$tax3-$mens_four[$i]).'</i></td></tr>';
				}
			}
			else
			{
				print '<th class="right"></th><th class="center">CA<br>Trimestriel</th><th class="right">Presta.(BIC)<br><font size=-2><i>'.$tx1.' %</i></font></th><th class="right">Forma.(CMA)<br><font size=-2><i>'.$tx2.' %</i></font></th><th class="right">Presta.(CMA)<br><font size=-2><i>'.$tx3.' %</i></font></th><th class="right">Total Taxes</th><th class="right">Achats</th><th class="right">CA Net</th></tr>';

				for($i=0; $i<4; $i++)
				{
					if ($year  == "2018")
					{
						switch($i)
						{
						case 0:
							$tx1=23.7;
							$tx1_1=22;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;						
						case 1:
							$tx1=22.0;
							$tx1_1=22;
							$tx1_2=0.0;
							$tx2=0.30;
							$tx3=0.00;
							break;						
						case 2:
							$tx1=22.0;
							$tx1_1=22;
							$tx1_2=0.0;
							$tx2=0.30;
							$tx3=0.48;
							break;
						case 3:
							$tx1=23.7;
							$tx1_1=22.0;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;
						}
					}
					
					if ($year  == "2022")
					{
						switch($i)
						{
						case 0:
						case 1:
						case 2:
							$tx1=23.7;
							$tx1_1=22;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;
						case 3:
							$tx1=22.9;
							$tx1_1=21.2;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
							break;
						}
					}
					if ($year  == "2023")
					{
							$tx1=22.9;
							$tx1_1=21.2;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
					}
					if ($year  == "2024")
					{
							$tx1=22.9;
							$tx1_1=21.2;
							$tx1_2=1.7;
							$tx2=0.30;
							$tx3=0.48;
					}					
					$j=$i+1;
					$tax1=round($trim[$i]*$tx1_1/100)+round($trim[$i]*$tx1_2/100);
					$tax2=round($trim[$i]*$tx2/100);
					$tax3=round($trim[$i]*$tx3/100);
					$tot_an_tax1+=$tax1;
					$tot_an_tax2+=$tax2;
					$tot_an_tax3+=$tax3;
					$tot_an_CA+=$trim[$i];
					$tot_an_four+=$trim_four[$i];
					print '<tr class="right"><td>'.$j.'</td><td colspan="1" class="right"><b>'.price($trim[$i]).'</b></td><td class="right">'.round($tax1).'</td><td class="right">'.round($tax2).'</td><td class="right">'.round($tax3).'</td><td class="right"><b>'.round($tax1+$tax2+$tax3).'</b></td><td class="right">'.price($trim_four[$i]).'</td><td class="right"><i>'.price($trim[$i]-$tax1-$tax2-$tax3-$trim_four[$i]).'</i></td></tr>';
				}
			}
			
			print '<tr class="left"><td><b>Total</b></td><td colspan="1" class="right"><b>'.price($tot_an_CA).'</b></td><td class="right">'.round($tot_an_tax1).'</td><td class="right">'.round($tot_an_tax2).'</td><td class="right">'.round($tot_an_tax3).'</td><td class="right"><b>'.round($tot_an_tax1+$tot_an_tax2+$tot_an_tax3).'</b></td><td class="right">'.price($tot_an_four).'</td><td class="right"><i>'.price($tot_an_CA-$tot_an_tax1-$tot_an_tax2-$tot_an_tax3-$tot_an_four).'</i></td></tr>';
			print '</table>';

		}
		$db->free($resql);
		$db->free($resql_four);
	}
	else
	{
		dol_print_error($db);
	}

$NBMAX = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;
$max = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;

print '</div><div class="fichetwothirdright"><div class="ficheaddleft"></div></div></div>';

// End of page
llxFooter();
$db->close();
