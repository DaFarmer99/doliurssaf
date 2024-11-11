<?php
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


function print_entete( int $vue, array $tab_tx)
{
	if ($vue == 1)
		$indic="Trimestriel";
	else
		$indic="Mensuel";
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th class="right"></th><th class="center">CA<br><b>'.$indic.'</b></th>
		<th class="right">CA<br>Serv</th>
		<th class="right">CA<br>Prod</th>
		<th class="right">Serv(BIC)<br><font size=-2><b>518</b> - <i>'.$tab_tx['1']['tx_518'].' %</i></font></th>
		<th class="right">Prod(BIC)<br><font size=-2><b>508</b> - <i>'.$tab_tx['1']['tx_508'].' %</i></font></th>
		<th class="right">IR Serv(BIC)<br><font size=-2><b>520</b> - <i>'.$tab_tx['1']['tx_520'].' %</i></font></th>
		<th class="right">IR Prod(BIC)<br><font size=-2><b>510</b> - <i>'.$tab_tx['1']['tx_510'].' %</i></font></th>
		<th class="right">Forma.(CMA)<br><font size=-2><b>572</b> - <i> '.$tab_tx['1']['tx_572'].' %</i></font></th>
		<th class="right">Prod(CMA)<br><font size=-2><b>060</b> - <i>'.$tab_tx['1']['tx_060'].' %</i></font></th>
		<th class="right">Serv(CMA)<br><font size=-2><b>061</b> - <i>'.$tab_tx['1']['tx_061'].' %</i></font></th>
		<th class="right">Total Taxes</th><th class="right">Achats</th>
		<th class="right">CA Net</th></tr>';
}

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

print load_fiche_titre($langs->trans("Déclaration URSSAF"), '', 'doliurssaf@doliurssaf');

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
$vue_form='<br><table><tr><td><b>Vue</b></td><td><b>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td><td><b>'.$vue_texte.'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?vue='.$vue_alter.'&year='.$year.'">'.$alt_vue_texte.'</a><br></td></tr>';
$year_form='<tr><td><b>Selection de l\'année </td><td><b>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td><td><a href="'.$_SERVER['PHP_SELF'].'?vue='.$vue.'&year='.$yearm1.'">&lt;&lt;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$year.'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?vue='.$vue.'&year='.$yearp1.'">&gt;&gt;</a></td></tr></table><br><br>';

print $vue_form;
print $year_form;
print '<div class="fichecenter">';


/*****************************************************/
/* Initialisation de la table des taux par trimestre */
/*****************************************************/
	$tab_tx= array();
	$trimestre = 4;
	while ($trimestre > 0)
	{
		$tab_tx[$trimestre]= array();
		// recup des taux de l'année et du trimestre
		$sqltx = "SELECT tx_508, tx_518, tx_510, tx_520, tx_572, tx_060, tx_061";
		$sqltx.= " FROM ".MAIN_DB_PREFIX."custom_urssaf";
		$sqltx.= " WHERE periode ='".$year."-".$trimestre."'";
		$resql_tx = $db->query($sqltx);
		$nb_tx = $db->num_rows($resql_tx);
		
		// on a trouvé le taux renseigné dans la table
		if ($nb_tx > 0)
		{
			$obj = $db->fetch_object($resql_tx);
			$tab_tx[$trimestre]['tx_508']= $obj->tx_508;
			$tab_tx[$trimestre]['tx_518']= $obj->tx_518;
			$tab_tx[$trimestre]['tx_510']= $obj->tx_510;
			$tab_tx[$trimestre]['tx_520']= $obj->tx_520;
			$tab_tx[$trimestre]['tx_572']= $obj->tx_572;
			$tab_tx[$trimestre]['tx_060']= $obj->tx_060;
			$tab_tx[$trimestre]['tx_061']= $obj->tx_061;
			//print "Trim : ".$trimestre."->518: ".$obj->tx_518."\n<br>";
		}
		// on récupère le taux le plus proche dans le passé
		else
		{
			for ($annee = $year; $annee > '2007'; $annee--)
			{
				$trimestre3 = $trimestre;
				if ($annee < $year) $trimestre3 = 4;
				for($trimestre2= $trimestre3; $trimestre2 > 0 ; $trimestre2--)
				{
					$sqltx = "SELECT periode, tx_508, tx_518, tx_510, tx_520, tx_572, tx_060, tx_061";
					$sqltx.= " FROM ".MAIN_DB_PREFIX."custom_urssaf";
					$sqltx.= " WHERE periode ='".$annee."-".$trimestre2."'";
					$resql_tx = $db->query($sqltx);
					$nb_tx = $db->num_rows($resql_tx);
					
					if ($nb_tx > 0) break;
				} 
				if ($nb_tx > 0) break;
			}
			$obj = $db->fetch_object($resql_tx);
			$tab_tx[$trimestre]['tx_508']= $obj->tx_508;
			$tab_tx[$trimestre]['tx_518']= $obj->tx_518;
			$tab_tx[$trimestre]['tx_510']= $obj->tx_510;
			$tab_tx[$trimestre]['tx_520']= $obj->tx_520;
			$tab_tx[$trimestre]['tx_572']= $obj->tx_572;
			$tab_tx[$trimestre]['tx_060']= $obj->tx_060;
			$tab_tx[$trimestre]['tx_061']= $obj->tx_061;
			//print "Trim : ".$trimestre."->518: ".$obj->tx_518."\n<br>";
		}
		$trimestre--;
	}
	
// recup des paiements et cumul
$sql = "SELECT DISTINCT llx_paiement.`rowid`, llx_paiement.datep as datep, llx_paiement.amount as amount, llx_facturedet.`product_type` as type";
$sql.= " FROM llx_paiement RIGHT JOIN llx_paiement_facture ON llx_paiement_facture.`fk_paiement` = llx_paiement.`rowid` RIGHT JOIN llx_facturedet ON llx_facturedet.`fk_facture` = llx_paiement_facture.`fk_facture`";
$sql.= " WHERE llx_paiement.datep between '".$year."/01/01 00:00:00' and '".$year."/12/31 23:59:59'";

$resql = $db->query($sql);

if ($resql)
{
	$trim_serv[] = 0;
	$trim_prod[] = 0;
	$mens_serv[] = 0;
	$mens_prod[] = 0;
	for ($i=1;$i<13;$i++)
	{
		$mens_serv[$i] = 0;
		$mens_prod[$i] = 0;
		$mens_four[$i] = 0;
	}
	for ($i=1;$i<5;$i++)
	{
		$trim_serv[$i] = 0;
		$trim_prod[$i] = 0;
		$trim_four[$i] = 0;
	}
		
	$num = $db->num_rows($resql);
	echo "Nombre de lignes de règlements ".$num;
	
	// Boucle sur la liste des règlements de la période
	$i = 0;
	while ($i < $num)
	{
		$obj = $db->fetch_object($resql);
		$month=date("n", strtotime($obj->datep));
		$montant= $obj->amount;
		
		//Cumul des services
		if ($obj->type == 1)
		{
			switch($month)
			{
				case "01":
				case "02":
				case "03":
					$trim_serv[1] += $montant;
					break;
				case "04":
				case "05":
				case "06":
					$trim_serv[2] += $montant;
					break;
				case "07":
				case "08":
				case "09":
					$trim_serv[3] += $montant;
					break;
				case "10":
				case "11":
				case "12":
					$trim_serv[4] += $montant;
					break;
			}
			$mens_serv[$month] += $montant;
		}
		//Cumul des produits
		else
		{	
			switch($month)
			{
				case "01":
				case "02":
				case "03":
					$trim_prod[1] += $montant;
					break;
				case "04":
				case "05":
				case "06":
					$trim_prod[2] += $montant;
					break;
				case "07":
				case "08":
				case "09":
					$trim_prod[3] += $montant;
					break;
				case "10":
				case "11":
				case "12":
					$trim_prod[4] += $montant;
					break;
			}
			$mens_prod[$month] += $montant;
		}
		$i++;
	}

	/*Cumul des dépenses fournisseurs */
	$sql_four = "SELECT datep , amount";
	$sql_four.= " FROM ".MAIN_DB_PREFIX."paiementfourn";
	$sql_four.= " WHERE datep between '".$year."/01/01 00:00:00' and '".$year."/12/31 23:59:59'";
	$resql_four = $db->query($sql_four);
		

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
					$trim_four[1] += $obj_four->amount;
					break;
				case "04":
				case "05":
				case "06":
					$trim_four[2] += $obj_four->amount;
					break;
				case "07":
				case "08":
				case "09":
					$trim_four[3] += $obj_four->amount;
					break;
				case "10":
				case "11":
				case "12":
					$trim_four[4] += $obj_four->amount;
					break;	
			}
			$mens_four[$month] += $obj_four->amount;
			$i++;
		}
	}
	$tot_an_CA_serv=0.0;
	$tot_an_CA_prod=0.0;
	$tot_an_CA=0.0;
	$tot_an_tax518=0.0;
	$tot_an_tax508=0.0;
	$tot_an_tax520=0.0;
	$tot_an_tax510=0.0;
	$tot_an_tax572=0.0;
	$tot_an_tax060=0.0;
	$tot_an_tax061=0.0;
	$tot_an_four=0.0;
		

/*******************************************************************************************/
/* Affichage des résultats sous deux formes différentes : mensuelle ou trimestrielle       */
/*******************************************************************************************/


	print_entete($vue, $tab_tx);
	// vue mensuelle
	if($vue == 2)
	{
		for($i=1; $i<13;$i++)
		{
			$quarter = 0;
			switch($i)
			{
				case 1:
				case 2:
				case 3:
					$quarter = 1;
					break;						
				case 4:
				case 5:
				case 6:
					$quarter = 2;
					break;						
				case 7:
				case 8:
				case 9:
					$quarter = 3;
					break;
				case 10:
				case 11:
				case 12:
					$quarter = 4;
					break;
			}
				
			$tax518=($mens_serv[$i]*$tab_tx[$quarter]['tx_518']/100);
			$tax520=($mens_serv[$i]*$tab_tx[$quarter]['tx_520']/100);
			$tax572=$mens_serv[$i]*$tab_tx[$quarter]['tx_572']/100;
			$tax061=$mens_serv[$i]*$tab_tx[$quarter]['tx_061']/100;
			$tax508=($mens_prod[$i]*$tab_tx[$quarter]['tx_508']/100);
			$tax510=($mens_prod[$i]*$tab_tx[$quarter]['tx_510']/100);
			$tax060=$mens_prod[$i]*$tab_tx[$quarter]['tx_060']/100;
			$tot_an_tax518+=$tax518;
			$tot_an_tax520+=$tax520;
			$tot_an_tax572+=$tax572;
			$tot_an_tax061+=$tax061;
			$tot_an_tax508+=$tax508;
			$tot_an_tax510+=$tax510;
			$tot_an_tax060+=$tax060;
			$tot_an_CA_serv+=$mens_serv[$i];
			$tot_an_CA_prod+=$mens_prod[$i];
			$tot_an_four+=$mens_four[$i];
			print '<tr class="oddeven">
			<td class="left">'.$month_names[$i-1].'</td>
			<td colspan="1" class="right"><b>'.price($mens_serv[$i]+$mens_prod[$i]).'</b></td>
			<td class="right">'.price($mens_serv[$i]).'</td>
			<td class="right">'.price($mens_prod[$i]).'</td>
			<td class="right">'.round($tax518, 2).'</td>
			<td class="right">'.round($tax508, 2).'</td>
			<td class="right">'.round($tax520, 2).'</td>
			<td class="right">'.round($tax510, 2).'</td>
			<td class="right">'.round($tax572, 2).'</td>
			<td class="right">'.round($tax060, 2).'</td>
			<td class="right">'.round($tax061, 2).'</td>
			<td class="right"><b>'.price(round($tax518+$tax508+$tax520+$tax510+$tax572+$tax060+$tax061, 2)).'</b></td>
			<td class="right">'.price($mens_four[$i]).'</td>
			<td class="right"><i>'.price(round($mens_serv[$i]+$mens_prod[$i]-$tax518-$tax508-$tax520-$tax510-$tax572-$tax060-$tax061-$mens_four[$i], 2)).'</i></td></tr>';
		}
	}
	else
	{
		for($i=1; $i<5; $i++)
		{
			$tax518=round($trim_serv[$i]*$tab_tx[$i]['tx_518']/100);
			$tax520=round($trim_serv[$i]*$tab_tx[$i]['tx_520']/100);
			$tax572=round($trim_serv[$i]*$tab_tx[$i]['tx_572']/100);
			$tax061=round($trim_serv[$i]*$tab_tx[$i]['tx_061']/100);
			$tax508=round($trim_prod[$i]*$tab_tx[$i]['tx_508']/100);
			$tax510=round($trim_prod[$i]*$tab_tx[$i]['tx_510']/100);
			$tax060=round($trim_prod[$i]*$tab_tx[$i]['tx_060']/100);					
			$tot_an_tax518+=$tax518;
			$tot_an_tax508+=$tax508;
			$tot_an_tax520+=$tax520;
			$tot_an_tax520+=$tax510;
			$tot_an_tax572+=$tax572;
			$tot_an_tax060+=$tax060;
			$tot_an_tax061+=$tax061;
			$tot_an_CA_serv+=$trim_serv[$i];
			$tot_an_CA_prod+=$trim_prod[$i];
			$tot_an_four+=$trim_four[$i];
			print '<tr class="right">
			<td>'.$i.'</td>
			<td class="right"><b>'.price($trim_serv[$i]+$trim_prod[$i]).'</b></td>
			<td class="right">'.price($trim_serv[$i]).'</td>
			<td class="right">'.price($trim_prod[$i]).'</td>
			<td class="right">'.round($tax518).'</td>
			<td class="right">'.round($tax508).'</td>
			<td class="right">'.round($tax520).'</td>
			<td class="right">'.round($tax510).'</td>
			<td class="right">'.round($tax572).'</td>
			<td class="right">'.round($tax060).'</td>
			<td class="right">'.round($tax061).'</td>
			<td class="right"><b>'.round($tax518+$tax508+$tax520+$tax510+$tax572+$tax060+$tax061).'</b></td>
			<td class="right">'.price($trim_four[$i]).'</td>
			<td class="right"><i>'.price(round($trim_serv[$i]+$trim_prod[$i]-$tax518-$tax508-$tax520-$tax510-$tax572-$tax060-$tax061-$trim_four[$i], 2)).'</i></td></tr>';
		}
	}

	print '<tr class="left">
		<td><b>Total</b></td>
		<td colspan="1" class="right"><b>'.price($tot_an_CA_serv+$tot_an_CA_prod).'</b></td>
		<td class="right">'.price($tot_an_CA_serv).'</td>
		<td class="right">'.price($tot_an_CA_prod).'</td>
		<td class="right">'.round($tot_an_tax518).'</td>
		<td class="right">'.round($tot_an_tax508).'</td>
		<td class="right">'.round($tot_an_tax520).'</td>
		<td class="right">'.round($tot_an_tax510).'</td>
		<td class="right">'.round($tot_an_tax572).'</td>
		<td class="right">'.round($tot_an_tax060).'</td>
		<td class="right">'.round($tot_an_tax061).'</td>
		<td class="right"><b>'.round($tot_an_tax518+$tot_an_tax508+$tot_an_tax520+$tot_an_tax510+$tot_an_tax572+$tot_an_tax060+$tot_an_tax061).'</b></td>
		<td class="right">'.price($tot_an_four).'</td>
		<td class="right"><i>'.price(round($tot_an_CA_serv+$tot_an_CA_prod-$tot_an_tax518-$tot_an_tax508-$tot_an_tax520-$tot_an_tax510-$tot_an_tax572-$tot_an_tax060-$tot_an_tax061-$tot_an_four, 2)).'</i></td></tr>';
	print '</table>';	

	$db->free($resql);
	$db->free($resql_four);
	$db->free($resql_tx);
}
else
{
	dol_print_error($db);
}

$NBMAX = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;
$max = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;

print '</div>';

// End of page
llxFooter();
$db->close();