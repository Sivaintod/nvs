<?php
session_start();
require_once("../fonctions.php");
require_once("f_carte.php");
require_once("f_combat.php");
require_once("f_action.php");

$mysqli = db_connexion();

include ('../nb_online.php');

// recuperation config jeu
$sql = "SELECT disponible FROM config_jeu";
$res = $mysqli->query($sql);
$t_dispo = $res->fetch_assoc();
$dispo = $t_dispo["disponible"];

if($dispo){

	$id_perso = $_SESSION["id_perso"];
	
	$carte = "carte";
	$X_MAX = X_MAX;
	$Y_MAX = Y_MAX;
	
	// Traitement action construction batiment
	if(isset($_POST['image_bat'])){
		$ok = construire_bat($mysqli, $_POST['image_bat'], $id_perso, $carte);
		
		if($ok){
			// header (retour a la page de jeu)
			header("location:jouer.php");
		}
	}
	else {
		// traitement action construction batiment
		// passage par le champ cache pour IE
		if(isset($_POST['hid_image_bat'])){
			$ok = construire_bat($mysqli, $_POST['hid_image_bat'], $id_perso, $carte);
			
			if($ok){
				// header (retour a la page de jeu)
				header("location:jouer.php");
			}
		}
	}
	
	?>
	<html>
	<head>
	<title>Nord VS Sud</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="description.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	<?php
	
	// Traitement action cible perso et soi-meme
	if(isset($_POST['action_cible_ref']) || isset($_POST['select_objet_soin'])){
		
		if(isset($_POST['select_objet_soin'])){
			$t_cib_ref = $_POST['select_objet_soin'];
			$t_cib_ref2 = explode(',',$t_cib_ref);
			$id_objet_s = $t_cib_ref2[0];
			$id_cible = $t_cib_ref2[1];
			$id_action = $t_cib_ref2[2];
		}
		else {
			$t_cib_ref = $_POST['action_cible_ref'];
			$t_cib_ref2 = explode(',',$t_cib_ref);
			$id_cible = $t_cib_ref2[0];
			$id_action = $t_cib_ref2[1];
		}
		
		if($id_action == '11' || $id_action == '12' || $id_action == '13' || $id_action == '14' || $id_action == '15' || 
		   $id_action == '16' || $id_action == '22' || $id_action == '23' || $id_action == '24' || $id_action == '25' ||
		   $id_action == '26' || $id_action == '27'){
		   // Soins pv
		   // Recuperation des objets que possede le perso pouvant ameliorer les soins
			$sql_s = "SELECT id_objet FROM perso_as_objet WHERE id_objet IN
					(select id_objet FROM objet WHERE type_objet ='SSP') AND id_perso='$id_perso'";
			$res_s = $mysqli->query($sql_s);
			$num_s = $res_s->num_rows;
				
			if((isset($_POST['select_objet_soin']) && $_POST['select_objet_soin'] != "" ) || $num_s == 0 || isset($id_objet_s)){
				if($num_s == 0 || $id_objet_s == "NO")
					$id_objet_soin = 0;
				else
					$id_objet_soin = $id_objet_s;
					
				action_soin($mysqli, $id_perso, $id_cible, $id_action,$id_objet_soin);
			}		
			else {
				if($num_s >= 1){
					// Affichage liste des objets qu'on peut utiliser pour ameliorer les soins
					echo "<form method='post' action='action.php'>";
					echo "<td align='center'><select name=\"select_objet_soin\">";
					echo "<option value=\"NO,$t_cib_ref\">-- AUCUN --</option>";
					while($t_s = $res_s->fetch_assoc()){
						$id_objet_s = $t_s['id_objet'];
							
						$sql_ss = "SELECT nom_objet FROM objet WHERE id_objet=$id_objet_s";
						$res_ss = $mysqli->query($sql_ss);
						$t_ss = $res_ss->fetch_assoc();
						$nom_objet = $t_ss['nom_objet'];
							
						echo "<option value=\"$id_objet_s,$t_cib_ref\">$nom_objet</option>";
					}
					echo "</select>&nbsp;<input type='submit' name='valid_objet_soin' value='valider' /><input type='hidden' name='hid_valid_objet_soin' value='valider' /></td>";
					echo "</form>";
				}
				else {
					echo "<center><font color='red'>Problème dans les données, veuillez contacter un administrateur.</font></center>";
				}
			}
		}
		if($id_action == '140' || $id_action == '141' || $id_action == '142' ){
			// Soins malus
			// Recuperation des objets que possede le perso pouvant ameliorer les soins
			$sql_s = "SELECT id_objet FROM perso_as_objet WHERE id_objet IN
					(select id_objet FROM objet WHERE type_objet ='SSP') AND id_perso='$id_perso'";
			$res_s = $mysqli->query($sql_s);
			$num_s = $res_s->num_rows;
				
			if((isset($_POST['select_objet_soin']) && $_POST['select_objet_soin'] != "" ) || $num_s == 0 || isset($id_objet_s)){
				if($num_s == 0 || $id_objet_s == "NO")
					$id_objet_soin = 0;
				else
					$id_objet_soin = $id_objet_s;
					
				action_soin_malus($mysqli, $id_perso, $id_cible, $id_action, $id_objet_soin);
			}		
			else {
				if($num_s >= 1){
					// Affichage liste des objets qu'on peut utiliser pour ameliorer les soins
					echo "<form method='post' action='action.php'>";
					echo "<td align='center'><select name=\"select_objet_soin\">";
					echo "<option value=\"NO,$t_cib_ref\">-- AUCUN --</option>";
					while($t_s = $res_s->fetch_assoc()){
						$id_objet_s = $t_s['id_objet'];
							
						$sql_ss = "SELECT nom_objet FROM objet WHERE id_objet=$id_objet_s";
						$res_ss = $mysqli->query($sql_ss);
						$t_ss = $res_ss->fetch_assoc();
						$nom_objet = $t_ss['nom_objet'];
							
						echo "<option value=\"$id_objet_s,$t_cib_ref\">$nom_objet</option>";
					}
					echo "</select>&nbsp;<input type='submit' name='valid_objet_soin' value='valider' /><input type='hidden' name='hid_valid_objet_soin' value='valider' /></td>";
					echo "</form>";
				}
				else {
					echo "<center><font color='red'>Problème dans les données, veuillez contacter un administrateur.</font></center>";
				}
			}
		}
		if($id_action == '17' || $id_action == '18' || $id_action == '19' || $id_action == '20' || $id_action == '21'){
			// Soins veterinaire
			echo "Soins vétérinaire";
		}
	}
	else {
		// traitement action cible perso et soi-meme
		// passage par le champ cache pour IE
		if(isset($_POST['hid_action_cible_ref']) || isset($_POST['select_objet_soin'])){
			if(isset($_POST['select_objet_soin'])){
				$t_cib_ref = $_POST['select_objet_soin'];
				$t_cib_ref2 = explode(',',$t_cib_ref);
				$id_objet_s = $t_cib_ref2[0];
				$id_cible = $t_cib_ref2[1];
				$id_action = $t_cib_ref2[2];
			}
			else {
				$t_cib_ref = $_POST['hid_action_cible_ref'];
				$t_cib_ref2 = explode(',',$t_cib_ref);
				$id_cible = $t_cib_ref2[0];
				$id_action = $t_cib_ref2[1];
			}
			
			if($id_action == '11' || $id_action == '12' || $id_action == '13' || $id_action == '14' || $id_action == '15' || 
			   $id_action == '16' || $id_action == '22' || $id_action == '23' || $id_action == '24' || $id_action == '25' ||
			   $id_action == '26' || $id_action == '27'){
				// Soins pv
				// Reparation des objets que possede le perso pouvant ameliorer les soins
				$sql_s = "SELECT id_objet FROM perso_as_objet WHERE id_objet IN
						(select id_objet FROM objet WHERE type_objet ='SSP') AND id_perso='$id_perso'";
				$res_s = $mysqli->query($sql_s);
				$num_s = $res_s->num_rows;
					
				if((isset($_POST['select_objet_soin']) && $_POST['select_objet_soin'] != "" ) || $num_s == 0 || isset($id_objet_s)){
					if($num_s == 0 || $id_objet_s == "NO")
						$id_objet_soin = 0;
					else
						$id_objet_soin = $id_objet_s;
						
					action_soin($mysqli, $id_perso, $id_cible, $id_action,$id_objet_soin);
				}		
				else {
					if($num_s >= 1){
						// Affichage liste des objets qu'on peut utiliser pour ameliorer les soins
						echo "<form method='post' action='action.php'>";
						echo "<td align='center'><select name=\"select_objet_soin\">";
						echo "<option value=\"NO,$t_cib_ref\">-- AUCUN --</option>";
						while($t_s = $res_s->fetch_assoc()){
							$id_objet_s = $t_s['id_objet'];
								
							$sql_ss = "SELECT nom_objet FROM objet WHERE id_objet=$id_objet_s";
							$res_ss = $mysqli->query($sql_ss);
							$t_ss = $res_ss->fetch_assoc();
							$nom_objet = $t_ss['nom_objet'];
								
							echo "<option value=\"$id_objet_s,$t_cib_ref\">$nom_objet</option>";
						}
						echo "</select>&nbsp;<input type='submit' name='valid_objet_soin' value='valider' /><input type='hidden' name='hid_valid_objet_soin' value='valider' /></td>";
						echo "</form>";
					}
					else {
						echo "<center><font color='red'>Problème dans les données, veuillez contacter un administrateur.</font></center>";
					}
				}
			}
			if($id_action == '140' || $id_action == '141' || $id_action == '142' ){
				// Soins malus
				// Recuperation des objets que possede le perso pouvant ameliorer les soins
				$sql_s = "SELECT id_objet FROM perso_as_objet WHERE id_objet IN
						(select id_objet FROM objet WHERE type_objet ='SSP') AND id_perso='$id_perso'";
				$res_s = $mysqli->query($sql_s);
				$num_s = $res_s->num_rows;
				
				if((isset($_POST['select_objet_soin']) && $_POST['select_objet_soin'] != "" ) || $num_s == 0 || isset($id_objet_s)){
					if($num_s == 0 || $id_objet_s == "NO")
						$id_objet_soin = 0;
					else
						$id_objet_soin = $id_objet_s;
						
					action_soin_malus($mysqli, $id_perso, $id_cible, $id_action, $id_objet_soin);
				}		
				else {
					if($num_s >= 1){
						// Affichage liste des objets qu'on peut utiliser pour ameliorer les soins
						echo "<form method='post' action='action.php'>";
						echo "<td align='center'><select name=\"select_objet_soin\">";
						echo "<option value=\"NO,$t_cib_ref\">-- AUCUN --</option>";
						while($t_s = $res_s->fetch_assoc()){
							$id_objet_s = $t_s['id_objet'];
								
							$sql_ss = "SELECT nom_objet FROM objet WHERE id_objet=$id_objet_s";
							$res_ss = $mysqli->query($sql_ss);
							$t_ss = $res_ss->fetch_assoc();
							$nom_objet = $t_ss['nom_objet'];
								
							echo "<option value=\"$id_objet_s,$t_cib_ref\">$nom_objet</option>";
						}
						echo "</select>&nbsp;<input type='submit' name='valid_objet_soin' value='valider' /><input type='hidden' name='hid_valid_objet_soin' value='valider' /></td>";
						echo "</form>";
					}
					else {
						echo "<center><font color='red'>Problème dans les données, veuillez contacter un administrateur.</font></center>";
					}
				}
			}
			if($id_action == '17' || $id_action == '18' || $id_action == '19' || $id_action == '20' || $id_action == '21'){
				// Soins veterinaire
				echo "Soins vétérinaire";
			}
		}
	}
	
	// Traitement action cible bat
	if(isset($_POST['action_cible_bat'])){
		
		$t_cib_bat = $_POST['action_cible_bat'];
		$t_cib_bat2 = explode(',',$t_cib_bat);
		$id_cible = $t_cib_bat2[0];
		$id_action = $t_cib_bat2[1];
		
		if($id_action == '76' || $id_action == '77' || $id_action == '78' || $id_action == '79'){
			// Reparer bat
			action_reparer_bat($mysqli, $id_perso, $id_cible, $id_action);
		}
		if($id_action == '80' || $id_action == '81' || $id_action == '82'){
			// Upgrade bat
			action_upgrade_bat($mysqli, $id_perso, $id_cible, $id_action);
		}
		if($id_action == '83' || $id_action == '84' || $id_action == '85'){
			// Upgrade bat Expert
			action_upgrade_expert_bat($mysqli, $id_perso, $id_cible, $id_action);
		}
	}
	else {
		// traitement action cible bat
		// passage par le champ cache pour IE
		if(isset($_POST['hid_action_cible_bat'])){
			
			$t_cib_bat = $_POST['hid_action_cible_bat'];
			$t_cib_bat2 = explode(',',$t_cib_bat);
			$id_cible = $t_cib_bat2[0];
			$id_action = $t_cib_bat2[1];
			
			if($id_action == '76' || $id_action == '77' || $id_action == '78' || $id_action == '79'){
				// Reparer bat
				action_reparer_bat($mysqli, $id_perso, $id_cible, $id_action);
			}
			if($id_action == '80' || $id_action == '81' || $id_action == '82'){
				// Upgrade bat
				action_upgrade_bat($mysqli, $id_perso, $id_cible, $id_action);
			}
			if($id_action == '83' || $id_action == '84' || $id_action == '85'){
				// Upgrade bat Expert
				action_upgrade_expert_bat($mysqli, $id_perso, $id_cible, $id_action);
			}
		}
	}
	
	// Saut
	if(isset($_POST['saut'])){
		$t_coord = $_POST['saut'];
		$t_coord2 = explode(',',$t_coord);
		$x_cible = $t_coord2[0];
		$y_cible = $t_coord2[1];
		$coutPa = $t_coord2[2];
		
		action_sauter($mysqli, $id_perso, $x_cible, $y_cible, $coutPa, $carte);
	}
	else {
		if(isset($_POST['hid_saut'])){
			$t_coord = $_POST['hid_saut'];
			$t_coord2 = explode(',',$t_coord);
			$x_cible = $t_coord2[0];
			$y_cible = $t_coord2[1];
			$coutPa = $t_coord2[2];
			
			action_sauter($mysqli, $id_perso, $x_cible, $y_cible, $coutPa, $carte);
		}
	}
	
	// Courir
	if(isset($_POST['courir'])){
		$t_coord = $_POST['courir'];
		$t_coord2 = explode(',',$t_coord);
		$direction = $t_coord2[0];
		$nb_points_action = $t_coord2[1];
		$coutPa_action = $t_coord2[2];
		
		action_courir($mysqli, $id_perso, $direction, $nb_points_action, $coutPa_action);
	}
	else {
		if(isset($_POST['hid_courir'])){
			$t_coord = $_POST['hid_courir'];
			$t_coord2 = explode(',',$t_coord);
			$direction = $t_coord2[0];
			$nb_points_action = $t_coord2[1];
			$coutPa_action = $t_coord2[2];
			
			action_courir($mysqli, $id_perso, $direction, $nb_points_action, $coutPa_action);
		}
	}
	
	
	// Chant
	if(isset($_POST['event_chant'])){
		$phrase = "a chanté ".addslashes($_POST['event_chant']);
		
		action_chanter_perso($mysqli, $id_perso, $phrase);
	}
	
	// Sculpture
	if(isset($_POST['event_scult'])){
		$phrase = "a sculpter ".$_POST['event_scult'];
		
		action_sculter_perso($mysqli, $id_perso, $phrase);
	}
	
	// Peinture
	if(isset($_POST['event_peind'])){
		$phrase = "a peind ".$_POST['event_peind'];
		
		action_peindre_perso($mysqli, $id_perso, $phrase);
	}
	
	// Danse
	if(isset($_POST['event_danse'])){
		$phrase = "a dansé ".$_POST['event_danse'];
		
		action_danser_perso($mysqli, $id_perso, $phrase);
	}
	
	// Deposer objet
	if(isset($_POST['valid_objet_depo']) && isset($_POST['id_objet_depo'])){
		$t_objet = $_POST['id_objet_depo'];
		$t2 = explode(',',$t_objet);
		$id_objet = $t2[0];
		$type_objet = $t2[1];
		$pv_objet = $t2[2];
		
		action_deposerObjet($mysqli, $id_perso, $type_objet, $id_objet, $pv_objet);
	}
	
	// Ramasser objet
	if(isset($_POST['valid_objet_ramasser']) && isset($_POST['id_objet_ramasser'])){
		$t_objet = $_POST['id_objet_ramasser'];
		$t2 = explode(',',$t_objet);
		$id_objet = $t2[0];
		$type_objet = $t2[1];
		$pv_objet = $t2[2];
		
		action_ramasserObjet($mysqli, $id_perso, $type_objet, $id_objet, $pv_objet);
	}
	
	// Don objet apres choix perso
	if(isset($_POST['select_perso_don']) && isset($_POST['valid_perso_don'])){
		
		$id_cible = $_POST['select_perso_don'];
		
		// verif perso chiffre et perso existe
		$verif_idPerso = preg_match("#^[0-9]*[0-9]$#i","$id_cible");
		
		if($verif_idPerso && $id_cible != "" && $id_cible != null){
		
			echo "<table border='1' align='center' width='50%'><tr><th colspan='4'>Objets à donner</th></tr>";
			echo "<tr><th>image</td><th>poid unitaire</td><th>nombre</th><th>donner ?</th></tr>";
							
			// Recuperation des objets / armes / armures que possede le perso
			// Or
			$compteur_or = 0;
			$sql_o0 = "SELECT or_perso FROM perso WHERE id_perso='$id_perso'";
			$res_o0 = $mysqli->query($sql_o0);
			$t_o0 = $res_o0->fetch_assoc();
			
			$or_perso = $t_o0['or_perso'];
			
			echo "<tr>";
			echo "<td align='center'><dl><dd><a href='#'><img src='../images/or.png' alt='or' height='30' width='30'/><span><b>or</b></span></a></dd></dl></td>";
			echo "<td align='center'>0</td>";
					
			echo "<form method='post' action='action.php'>";		
			echo "<td align='center'><select name=\"select_don_or\">";
			while ( $compteur_or <= $or_perso){
				echo "<option value=\"$compteur_or\">$compteur_or</option>";
				$compteur_or++;
			}
			echo "</select></td>";
				
			echo "<td align='center'><input type='submit' name='valid_objet_don' value='oui' /><input type='hidden' name='id_objet_don' value='-1,1,0,$id_cible' /></td>";
			echo "</form>";
			echo "</tr>";
				
				
			// Objets
			$sql_o = "SELECT DISTINCT id_objet FROM perso_as_objet WHERE id_perso='$id_perso' ORDER BY id_objet";
			$res_o = $mysqli->query($sql_o);
			
			while($t_o = $res_o->fetch_assoc()){
				
				$id_objet = $t_o["id_objet"];
					
				// recuperation des carac de l'objet
				$sql1_o = "SELECT nom_objet, poids_objet FROM objet WHERE id_objet='$id_objet'";
				$res1_o = $mysqli->query($sql1_o);
				$t1_o = $resl_o->fetch_assoc();
				$nom_o = $t1_o["nom_objet"];
				$poids_o = $t1_o["poids_objet"];
										
				// recuperation du nombre d'objet de ce type que possede le perso
				$sql2_o = "SELECT id_objet FROM perso_as_objet WHERE id_perso='$id_perso' AND id_objet='$id_objet'";
				$res2_o = $mysqli->query($sql2_o);
				$nb_o = $res2_o->num_rows();
										
				echo "<tr>";
				echo "<td align='center'><dl><dd><a href='#'><img src='../images/objet".$id_objet.".png' alt='$nom_o' height='50' width='50'/><span><b>".stripslashes($nom_o)."</b></span></a></dd></dl></td>";
				echo "<td align='center'>$poids_o</td>";
				echo "<td align='center'>$nb_o</td>";
				echo "<form method='post' action='action.php'>";
				echo "<td align='center'><input type='submit' name='valid_objet_don' value='oui' /><input type='hidden' name='id_objet_don' value='$id_objet,2,0,$id_cible' /></td>";
				echo "</form>";
				echo "</tr>";
			}
			
			// Armes non portes
			$sql_a1 = "SELECT DISTINCT id_arme, pv_arme FROM perso_as_arme WHERE id_perso='$id_perso' AND est_portee='0' ORDER BY id_arme";
			$res_a1 = $mysqli->query($sql_a1);
			
			while($t_a1 = $res_a1->fetch_assoc()){
				
				$id_arme = $t_a1["id_arme"];
				$pv_arme = $t_a1["pv_arme"];
									
				// recuperation des carac de l'arme
				$sql1_a1 = "SELECT nom_arme, poids_arme, image_arme FROM arme WHERE id_arme='$id_arme'";
				$res1_a1 = $mysqli->query($sql1_a1);
				$t1_a1 = $res1_a1->fetch_assoc();
				$nom_a1 = $t1_a1["nom_arme"];
				$poids_a1 = $t1_a1["poids_arme"];
				$image_arme = $t1_a1["image_arme"];
									
				// recuperation du nombre d'armes non equipes de ce type et ayant ce nombre de pv que possede le perso 
				$sql2_a1 = "SELECT id_arme FROM perso_as_arme WHERE id_perso='$id_perso' AND id_arme='$id_arme' AND est_portee='0' AND pv_arme='$pv_arme'";
				$res2_a1 = $mysqli->query($sql2_a1);
				$nb_a1 = $res2_a1->num_rows;
									
				echo "<tr>";
				echo "<td align='center'><dl><dd><a href='#'><img src='../images/armes/$image_arme' alt='$nom_a1' height='50' width='50'/><span><b>".stripslashes($nom_a1)."</b><br /><u>Pv :</u> ".$pv_arme."</span></a></dd></dl></td>";
				echo "<td align='center'>$poids_a1</td>";
				echo "<td align='center'>$nb_a1</td>";
				echo "<form method='post' action='action.php'>";
				echo "<td align='center'><input type='submit' name='valid_objet_don' value='oui' /><input type='hidden' name='id_objet_don' value='$id_arme,3,$pv_arme,$id_cible' /></td>";
				echo "</form>";
				echo "</tr>";
			}
									
			// Armures non portes
			$sql_a2 = "SELECT DISTINCT id_armure, pv_armure FROM perso_as_armure WHERE id_perso='$id_perso' AND est_portee='0' ORDER BY id_armure";
			$res_a2 = $mysqli->query($sql_a2);
			
			while($t_a2 = $res_a2->fetch_assoc()){
				
				$id_armure = $t_a2["id_armure"];
				$pv_armure = $t_a2["pv_armure"];
									
				// recuperation des carac de l'arme
				$sql1_a2 = "SELECT nom_armure, poids_armure, image_armure FROM armure WHERE id_armure='$id_armure'";
				$res1_a2 = $mysqli->query($sql1_a2);
				$t1_a2 = $res1_a2->fetch_assoc();
				$nom_a2 = $t1_a2["nom_armure"];
				$poids_a2 = $t1_a2["poids_armure"];
				$image_armure = $t1_a2["image_armure"];
									
				// recuperation du nombre d'armes non equipes de ce type que possede le perso 
				$sql2_a2 = "SELECT id_armure FROM perso_as_armure WHERE id_perso='$id_perso' AND id_armure='$id_armure' AND est_portee='0' AND pv_armure='$pv_armure'";
				$res2_a2 = $mysqli->query($sql2_a2);
				$nb_a2 = $res2_a2->num_rows;
									
				echo "<tr>";
				echo "<td align='center'><dl><dd><a href='#'><img src='../images/armures/$image_armure' alt='$nom_a2' height='50' width='50'/><span><b>".stripslashes($nom_a2)."</b><br /><u>Pv :</u> ".$pv_armure."</span></a></dd></dl></td>";
				echo "<td align='center'>$poids_a2</td>";
				echo "<td align='center'>$nb_a2</td>";
				echo "<form method='post' action='action.php'>";
				echo "<td align='center'><input type='submit' name='valid_objet_don' value='oui' /><input type='hidden' name='id_objet_don' value='$id_armure,4,$pv_armure,$id_cible' /></td>";
				echo "</form>";
				echo "</tr>";
			}
								
			echo "</table><br /><br />";
		}
		else {
			echo "<font color='red'>La cible n'est correcte.</font><br/>";
			echo "<center><a href='jouer.php'>[ retour ]</a></center>";
		}
	}
	
	// Don objet apres choix objet
	if(isset($_POST['valid_objet_don']) && isset($_POST['id_objet_don']) ){
		
		$quantite = 1;
		if(isset($_POST['select_don_or'])){
			$quantite = $_POST['select_don_or'];
		}
		$t_objet = $_POST['id_objet_don'];
		$t2 = explode(',',$t_objet);
		$id_objet = $t2[0];
		$type_objet = $t2[1];
		$pv_objet = $t2[2];
		$id_cible = $t2[3];
		
		action_don_objet($mysqli, $id_perso, $id_cible, $type_objet, $id_objet, $pv_objet, $quantite);
	}
	
	/////////////////////////
	// Traitement des actions
	if(isset($_POST['action'])){
		
		if(isset($_POST['liste_action']) && $_POST['liste_action'] != 'invalide' && $_POST['liste_action'] != 'PA'){
			
			// recuperation de l'id de l'action
			$id_action = $_POST['liste_action'];
			
			// verification que le perso possede bien l'action
			$sql_v = "SELECT action.id_action
					FROM perso_as_competence, competence_as_action, action 
					WHERE id_perso='$id_perso' 
					AND perso_as_competence.id_competence=competence_as_action.id_competence 
					AND competence_as_action.id_action=action.id_action
					AND perso_as_competence.nb_Points=action.nb_points
					AND action.id_action='$id_action'";
			$res_v = $mysqli->query($sql_v);
			$verif = $res_v->num_rows;
			
			if($verif || $id_action=='65' || $id_action=='110' || $id_action=='111' || $id_action=='139'){
			
				// recuperation des effet et du type d'action
				$sql = "SELECT * FROM action WHERE id_action='$id_action'";
				$res = $mysqli->query($sql);
				$t_ac = $res->fetch_assoc();
		
				$nom_action = $t_ac['nom_action'];
				$nb_points_action = $t_ac['nb_points'];
				$portee_action = $t_ac['portee_action'];
				$perceptionMin_action = $t_ac['perceptionMin_action'];
				$perceptionMax_action = $t_ac['perceptionMax_action'];
				$pvMin_action = $t_ac['pvMin_action'];
				$pvMax_action = $t_ac['pvMax_action'];
				$recupMin_action = $t_ac['recupMin_action'];
				$recupMax_action = $t_ac['recupMax_action'];
				$pmMin_action = $t_ac['pmMin_action'];
				$pmMax_action = $t_ac['pmMax_action'];
				$DefMin_action = $t_ac['DefMin_action'];
				$DefMax_action = $t_ac['DefMax_action'];
				$coutPa_action = $t_ac['coutPa_action'];
				$nbreTourMin = $t_ac['nbreTourMin'];
				$nbreTourMax = $t_ac['nbreTourMax'];
				$coutOr_action = $t_ac['coutOr_action'];
				$coutBois_action = $t_ac['coutBois_action'];
				$coutFer_action = $t_ac['coutFer_action'];
				$reflexive_action = $t_ac['reflexive_action'];
				$cible_action = $t_ac['cible_action'];
				$case_action = $t_ac['case_action'];
				$pnj_action = $t_ac['pnj_action'];
				
				$image_action = image_action($id_action);
				
				// action ayant pour cible juste son propre perso
				if($reflexive_action && !$cible_action){
					
					// traitement de l'action entrainement
					if($nom_action == 'Entrainement'){
						action_entrainement($mysqli, $id_perso);
					}
					
					// traitement de l'action dormir
					if($nom_action == 'dormir'){
						action_dormir($mysqli, $id_perso, $nb_points_action);
					}
					
					// traitement de l'action marche forcee
					if($nom_action == 'Marche forcée'){
						action_marcheForcee($mysqli, $id_perso, $nb_points_action,$coutPa_action);
					}
					
					// traitement de l'action courir
					if($nom_action == 'Courir'){
						if(!in_bat($id_perso)){
							
							//recuperation des coordonnees du perso
							$sql = "SELECT x_perso, y_perso, perception_perso, clan FROM perso WHERE id_perso='$id_perso'";
							$res = $mysqli->query($sql);
							$t_coord = $res->fetch_assoc();
										
							$x_perso = $t_coord['x_perso'];
							$y_perso = $t_coord['y_perso'];
							$perception_perso = $t_coord['perception_perso'];
							$clan_perso = $t_coord['clan'];
							
							// recuperation des donnees de la carte
							$sql = "SELECT x_carte, y_carte, fond_carte, occupee_carte, image_carte, idPerso_carte FROM $carte WHERE x_carte >= $x_perso - $perception_perso AND x_carte <= $x_perso + $perception_perso AND y_carte <= $y_perso + $perception_perso AND y_carte >= $y_perso - $perception_perso ORDER BY y_carte DESC, x_carte";
							$res = $mysqli->query($sql);
							$tab = $res->fetch_assoc(); 			
							
							//<!--Generation de la carte-->
							echo '<table border=0 align="center" cellspacing="0" cellpadding="0" style:no-padding>';
										
							echo "<tr><td>y \ x</td>";  //affichage des abscisses
							for ($i = $x_perso - $perception_perso; $i <= $x_perso + $perception_perso; $i++) {
								echo "<th width=40 height=40>$i</th>";
							}
							echo "</tr>";
										
							for ($y = $y_perso + $perception_perso; $y >= $y_perso - $perception_perso; $y--) {
								
								echo "<th>$y</th>";
								for ($x = $x_perso - $perception_perso; $x <= $x_perso + $perception_perso; $x++) {
									
									//les coordonnees sont dans les limites
									if ($x >= X_MIN && $y >= Y_MIN && $x <= $X_MAX && $y <= $Y_MAX) {
										
										if ($tab["occupee_carte"]){
											echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"><img border=0 src=\"../images_perso/".$tab["image_carte"]."\" width=40 height=40 \></td>";
										}
										else{
											//positionnement du fond
											$fond_carte = $tab["fond_carte"];
											if($x == ($x_perso - 1) && $y == ($y_perso + 1)){
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40> <input type=\"image\" name=\"courir\" value=\"1,$nb_points_action,$coutPa_action\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../fond_carte/fleche01.gif';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_courir\" value=\"1,$nb_points_action,$coutPa_action\" ></td>";
												echo "</form>";
											}
											else if($x == $x_perso && $y == ($y_perso + 1)){
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40> <input type=\"image\" name=\"courir\" value=\"2,$nb_points_action,$coutPa_action\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../fond_carte/fleche02.gif';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_courir\" value=\"2,$nb_points_action,$coutPa_action\" ></td>";
												echo "</form>";
											}
											else if($x == ($x_perso + 1) && $y == ($y_perso + 1)){
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40> <input type=\"image\" name=\"courir\" value=\"3,$nb_points_action,$coutPa_action\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../fond_carte/fleche03.gif';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_courir\" value=\"3,$nb_points_action,$coutPa_action\" ></td>";
												echo "</form>";
											}
											else if($x == ($x_perso - 1) && $y == $y_perso){
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40> <input type=\"image\" name=\"courir\" value=\"4,$nb_points_action,$coutPa_action\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../fond_carte/fleche04.gif';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_courir\" value=\"4,$nb_points_action,$coutPa_action\" ></td>";
												echo "</form>";
											}
											else if($x == ($x_perso + 1) && $y == $y_perso){
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40> <input type=\"image\" name=\"courir\" value=\"5,$nb_points_action,$coutPa_action\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../fond_carte/fleche05.gif';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_courir\" value=\"5,$nb_points_action,$coutPa_action\" ></td>";
												echo "</form>";
											}
											else if($x == ($x_perso - 1) && $y == ($y_perso - 1)){
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40> <input type=\"image\" name=\"courir\" value=\"6,$nb_points_action,$coutPa_action\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../fond_carte/fleche06.gif';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_courir\" value=\"6,$nb_points_action,$coutPa_action\" ></td>";
												echo "</form>";
											}
											else if($x == $x_perso && $y == ($y_perso - 1)){
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40> <input type=\"image\" name=\"courir\" value=\"7,$nb_points_action,$coutPa_action\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../fond_carte/fleche07.gif';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_courir\" value=\"7,$nb_points_action,$coutPa_action\" ></td>";
												echo "</form>";
											}
											else if($x == ($x_perso + 1) && $y == ($y_perso - 1)){
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40> <input type=\"image\" name=\"courir\" value=\"8,$nb_points_action,$coutPa_action\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../fond_carte/fleche08.gif';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_courir\" value=\"8,$nb_points_action,$coutPa_action\" ></td>";
												echo "</form>";
											}
											else{
												echo "<td width=40 height=40> <img border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 ></td>";
											}
										}
										$tab = $res->fetch_assoc();
									}
									else{
										//les coordonnees sont hors limites
										echo "<td width=40 height=40><img border=0 width=40 height=40 src=\"../fond_carte/decorO.jpg\"></td>";
									}
								}
								echo "</tr>";
							}
							echo "</table>";
							// fin de la generation de la carte
							
							// lien annuler
							echo "<br /><br /><center><a href='jouer.php'><b>[ annuler ]</b></a></center>";
						}
						else {
							echo "<center><font color='red'>Impossible de courir depuis un bâtiment, veuillez sortir pour effectuer cette action</font>";
							echo "<br /><a href='jouer.php'>[ retour ]</a></center>";
						}
					}
					
					// traitement de l'action sauter
					if($nom_action == 'Sauter'){
						if(!in_bat($id_perso)){
							
							//recuperation des coordonnees du perso
							$sql = "SELECT x_perso, y_perso, clan FROM perso WHERE id_perso='$id_perso'";
							$res = $mysqli->query($sql);
							$t_coord = $res->fetch_assoc();
										
							$x_perso = $t_coord['x_perso'];
							$y_perso = $t_coord['y_perso'];
							$clan_perso = $t_coord['clan'];
										
							$image_saut='../images/saut.gif';
							
							// recuperation des donnees de la carte
							$sql = "SELECT x_carte, y_carte, fond_carte, occupee_carte, image_carte, idPerso_carte FROM $carte WHERE x_carte >= $x_perso - 2 AND x_carte <= $x_perso + 2 AND y_carte <= $y_perso + 2 AND y_carte >= $y_perso - 2 ORDER BY y_carte DESC, x_carte";
							$res = $mysqli->query($sql);
							$tab = $res->fetch_assoc(); 			
							
							//<!--Generation de la carte-->
							echo '<table border=0 align="center" cellspacing="0" cellpadding="0" style:no-padding>';
										
							echo "<tr><td>y \ x</td>";  //affichage des abscisses
							for ($i = $x_perso - 2; $i <= $x_perso + 2; $i++) {
								echo "<th width=40 height=40>$i</th>";
							}
							echo "</tr>";
										
							for ($y = $y_perso + 2; $y >= $y_perso - 2; $y--) {
								
								echo "<th>$y</th>";
								for ($x = $x_perso - 2; $x <= $x_perso + 2; $x++) {
									
									//les coordonnees sont dans les limites
									if ($x >= X_MIN && $y >= Y_MIN && $x <= $X_MAX && $y <= $Y_MAX) {
										
										if ($tab["occupee_carte"]){
											echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"><img border=0 src=\"../images_perso/".$tab["image_carte"]."\" width=40 height=40 \></td>";
										}
										else{
											//positionnement du fond
											$fond_carte = $tab["fond_carte"];
											if($x == $x_perso && $y == ($y_perso + 2)){
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40> <input type=\"image\" name=\"saut\" value=\"$x,$y,$coutPa_action\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../images_perso/$image_saut';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_saut\" value=\"$x,$y,$coutPa_action\" ></td>";
												echo "</form>";
											}
											else if($x == ($x_perso - 2) && $y == $y_perso){
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40> <input type=\"image\" name=\"saut\" value=\"$x,$y,$coutPa_action\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../images_perso/$image_saut';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_saut\" value=\"$x,$y,$coutPa_action\" ></td>";
												echo "</form>";
											}
											else if($x == ($x_perso + 2) && $y == $y_perso){
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40> <input type=\"image\" name=\"saut\" value=\"$x,$y,$coutPa_action\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../images_perso/$image_saut';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_saut\" value=\"$x,$y,$coutPa_action\" ></td>";
												echo "</form>";
											}
											else if($x == $x_perso && $y == ($y_perso - 2)){
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40> <input type=\"image\" name=\"saut\" value=\"$x,$y,$coutPa_action\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../images_perso/$image_saut';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_saut\" value=\"$x,$y,$coutPa_action\" ></td>";
												echo "</form>";
											}
											else{
												echo "<td width=40 height=40> <img border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 ></td>";
											}
										}
										$tab = $res->fetch_assoc();
									}
									else{
										//les coordonnees sont hors limites
										echo "<td width=40 height=40><img border=0 width=40 height=40 src=\"../fond_carte/decorO.jpg\"></td>";
									}
								}
								echo "</tr>";
							}
							echo "</table>";
							// fin de la generation de la carte
							
							// lien annuler
							echo "<br /><br /><center><a href='jouer.php'><b>[ annuler ]</b></a></center>";
						}
						else {
							echo "<center><font color='red'>Impossible de sauter depuis un bâtiment, veuillez sortir pour effectuer cette action</font>";
							echo "<br /><a href='jouer.php'>[ retour ]</a></center>";
						}
					}
					
					// traitement de l'action chanter
					if($nom_action == 'Chanter'){
						if($nb_points_action == 1){
							action_chanter($mysqli, $id_perso,$id_action);
						}
						else {
							echo "<center>Personnalisation de l'événement</center>";
							echo "<form method=\"post\" action=\"action.php\">";
							echo "a chanté <input type=\"text\" name=\"event_chant\"><input type=\"submit\" value=\"ok\">";
							echo "</form>";
						}
					}
					
					// traitement de l'action Danser
					if($nom_action == 'Danser'){
						if($nb_points_action == 1){
							action_danser($mysqli, $id_perso, $id_action);
						}
						else {
							echo "<center>Personnalisation de l'événement</center>";
							echo "<form method=\"post\" action=\"action.php\">";
							echo "a chanté <input type=\"text\" name=\"event_danse\"><input type=\"submit\" value=\"ok\">";
							echo "</form>";
						}
					}
					
					// traitement de l'action Peindre
					if($nom_action == 'Peindre'){
						if($nb_points_action == 1){
							action_peindre($mysqli, $id_perso, $id_action);
						}
						else {
							echo "<center>Personnalisation de l'événement</center>";
							echo "<form method=\"post\" action=\"action.php\">";
							echo "a chanté <input type=\"text\" name=\"event_peind\"><input type=\"submit\" value=\"ok\">";
							echo "</form>";
						}
					}
					
					// traitement de l'action Sculpter
					if($nom_action == 'Sculpter'){
						if($nb_points_action == 1){
							action_sculter($mysqli, $id_perso, $id_action);
						}
						else {
							echo "<center>Personnalisation de l'événement</center>";
							echo "<form method=\"post\" action=\"action.php\">";
							echo "a chanté <input type=\"text\" name=\"event_scult\"><input type=\"submit\" value=\"ok\">";
							echo "</form>";
						}
					}
				}
				
				if($pnj_action){
					// header (retour a la page de jeu)
					header("location:jouer.php?erreur=competence");
				}
				
		?>
		<SCRIPT LANGUAGE="JavaScript" SRC="javascript/infobulle.js"></script>
		<SCRIPT language="JavaScript">
		InitBulle("#000000","#f4f4f4","000000",1);
		// InitBulle(couleur de texte, couleur de fond, couleur de contour taille contour)
		</SCRIPT>
		<?php		
		
				// action ayant pour cible un perso
				if($cible_action){
					
					// action pouvant cibler son propre perso
					if($reflexive_action){
						
						//recuperation des coordonnees du perso
						$sql = "SELECT x_perso, y_perso, clan FROM perso WHERE id_perso='$id_perso'";
						$res = $mysqli->query($sql);
						$t_coord = $res->fetch_assoc();
						
						$x_perso = $t_coord['x_perso'];
						$y_perso = $t_coord['y_perso'];
						$clan_perso = $t_coord['clan'];
						
						// recuperation des donnees de la carte
						$sql = "SELECT x_carte, y_carte, fond_carte, occupee_carte, image_carte, idPerso_carte FROM $carte WHERE x_carte >= $x_perso - 1 AND x_carte <= $x_perso + 1 AND y_carte <= $y_perso + 1 AND y_carte >= $y_perso - 1 ORDER BY y_carte DESC, x_carte";
						$res = $mysqli->query($sql);
						$tab = $res->fetch_assoc(); 
						
						//<!--Generation de la carte-->
						echo '<table border=0 align="center" cellspacing="0" cellpadding="0" style:no-padding>';
						
						echo "<tr><td>y \ x</td>";  //affichage des abscisses
						for ($i = $x_perso - 1; $i <= $x_perso + 1; $i++) {
							echo "<th width=40 height=40>$i</th>";
						}
						echo "</tr>";
						
						for ($y = $y_perso + 1; $y >= $y_perso - 1; $y--) {
							echo "<th>$y</th>";
							for ($x = $x_perso - 1; $x <= $x_perso + 1; $x++) {
								
								//les coordonnees sont dans les limites
								if ($x >= X_MIN && $y >= Y_MIN && $x <= $X_MAX && $y <= $Y_MAX) {
									
									if ($tab["occupee_carte"]){
										
										$image_perso = $tab["image_carte"];
										$id_perso_carte = $tab["idPerso_carte"];
										
										if($id_perso_carte < 10000 && isset($id_perso_carte)){
											
											// recuperation des infos du perso
											$sql_perso_carte = "SELECT nom_perso, clan FROM perso WHERE id_perso=$id_perso_carte";
											$res_perso_carte = $mysqli->query($sql_perso_carte);
											$t_perso_carte = $res_perso_carte->fetch_assoc();
											
											$nom_perso_carte = $t_perso_carte["nom_perso"];
											$clan_perso_carte = $t_perso_carte["clan"];
											if($clan_perso_carte == $clan_perso){
												$clan_pc = 'blue';
											}
											else {
												$clan_pc = 'red';
											}
										
											if($nom_action == "Apaiser"){
												$action = "+ Apaiser +";
											}
											else {
												$action = "+ Soigner +";
											}
										
											echo "<form method=\"post\" action=\"action.php\" >";
											echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"> <input type=\"image\" name=\"action_cible_ref\" value=\"$id_perso_carte,$id_action\" border=0 src=\"../images_perso/".$image_perso."\" width=40 height=40 onMouseOver=\"this.src='../images/$image_action';AffBulle('<tr><td>$action</td></tr><tr><td><font color=$clan_pc>$nom_perso_carte</font> [$id_perso_carte]</td></tr>');\" onMouseOut=\"this.src='../images_perso/$image_perso';HideBulle();\" ><input type=\"hidden\" name=\"hid_action_cible_ref\" value=\"$id_perso_carte,$id_action\" ></td>";
											echo "</form>";
										}
										else {
											echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"><img border=0 src=\"../images_perso/".$tab["image_carte"]."\" width=40 height=40 \></td>";
										}
									}
									else{
										//positionnement du fond
										$fond_carte = $tab["fond_carte"];
										
										echo "<td width=40 height=40> <img border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 ></td>";
									}
									$tab = $res->fetch_assoc();
								}
								else //les coordonnees sont hors limites
									echo "<td width=40 height=40><img border=0 width=40 height=40 src=\"../fond_carte/decorO.jpg\"></td>";
							}
							echo "</tr>";
						}
						echo "</table>";
						// fin de la generation de la carte
						
						// lien annuler
						echo "<br /><br /><center><a href='jouer.php'><b>[ annuler ]</b></a></center>";
					}
					
					// action ne pouvant pas cibler son propre perso
					else {
						//recuperation des coordonnees du perso
						$sql = "SELECT x_perso, y_perso, clan FROM perso WHERE id_perso='$id_perso'";
						$res = $mysqli->query($sql);
						$t_coord = $res->fetch_assoc();
						
						$x_perso = $t_coord['x_perso'];
						$y_perso = $t_coord['y_perso'];
						$clan_perso = $t_coord['clan'];
						
						// Donner objet
						if($nom_action == 'Donner objet'){
							
							echo "<table border='1' align='center' width='50%'><tr><th colspan='4'>Personnage à qui donner l'objet</th></tr>";
							echo "<tr>";
							
							// Recuperation des persos au CaC
							$sql_c = "SELECT idPerso_carte FROM $carte WHERE x_carte<=$x_perso+1 AND x_carte>=$x_perso-1 AND y_carte>=$y_perso-1 AND y_carte<=$y_perso+1 AND occupee_carte='1' AND idPerso_carte!='$id_perso' AND idPerso_carte < 10000";
							$res_c = $mysqli->query($sql_c);
							
							while($t_c = $res_c->fetch_assoc()){
								
								$id_cible = $t_c['idPerso_carte'];
								
								// Recuperation infos cible
								$sql_cible = "SELECT nom_perso, clan FROM perso WHERE id_perso='$id_cible'";
								$res_cible = $mysqli->query($sql_cible);
								$t_cible = $res_cible->fetch_assoc();
								$nom_cible = $t_cible['nom_perso'];
								$camp_cible = $t_cible['clan'];
								
								// recuperation de la couleur du camp
								$couleur_clan_cible = couleur_clan($camp_cible);
								
								echo "<form method='post' action='action.php'>";
								echo "<td align='center'><select name=\"select_perso_don\">";
								echo "<option style=\"color:$couleur_clan_cible\" value=\"$id_cible\">$nom_cible</option>";
								echo "</select>&nbsp;<input type='submit' name='valid_perso_don' value='valider' /><input type='hidden' name='hid_valid_perso_don' value='valider' /></td>";
								echo "";
								echo "</form>";
							}
							
							echo "</tr></table>";
							
							
						}
						else {
							// Soins							
							// recuperation des donnees de la carte
							$sql = "SELECT x_carte, y_carte, fond_carte, occupee_carte, image_carte, idPerso_carte FROM $carte WHERE x_carte >= $x_perso - 1 AND x_carte <= $x_perso + 1 AND y_carte <= $y_perso + 1 AND y_carte >= $y_perso - 1 ORDER BY y_carte DESC, x_carte";
							$res = $mysqli->query($sql);
							$tab = $res->fetch_assoc(); 
							
							//<!--Generation de la carte-->
							echo '<table border=0 align="center" cellspacing="0" cellpadding="0" style:no-padding>';
							
							echo "<tr><td>y \ x</td>";  //affichage des abscisses
							for ($i = $x_perso - 1; $i <= $x_perso + 1; $i++) {
								echo "<th width=40 height=40>$i</th>";
							}
							echo "</tr>";
							
							for ($y = $y_perso + 1; $y >= $y_perso - 1; $y--) {
								echo "<th>$y</th>";
								for ($x = $x_perso - 1; $x <= $x_perso + 1; $x++) {
									
									//les coordonnees sont dans les limites
									if ($x >= X_MIN && $y >= Y_MIN && $x <= $X_MAX && $y <= $Y_MAX) {
										
										if ($tab["occupee_carte"]){
											
											$image_perso = $tab["image_carte"];
											$id_perso_carte = $tab["idPerso_carte"];
											
											if($id_perso_carte < 10000 && isset($id_perso_carte) && $id_perso_carte != $id_perso){
												
												// recuperation des infos du perso
												$sql_perso_carte = "SELECT nom_perso, clan FROM perso WHERE id_perso=$id_perso_carte";
												$res_perso_carte = $mysqli->query($sql_perso_carte);
												$t_perso_carte = $res_perso_carte->fetch_assoc();
												
												$nom_perso_carte = $t_perso_carte["nom_perso"];
												$clan_perso_carte = $t_perso_carte["clan"];
												
												if($clan_perso_carte == $clan_perso){
													$clan_pc = 'blue';
												}
												else {
													$clan_pc = 'red';
												}
											
												echo "<form method=\"post\" action=\"action.php\" >";
												echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"> 
														<input type=\"image\" name=\"action_cible\" value=\"$id_perso_carte,$id_action\" border=0 src=\"../images_perso/".$image_perso."\" width=40 height=40 onMouseOver=\"this.src='../images/$image_action';AffBulle('<tr><td>+ Soigner +</td></tr><tr><td><font color=$clan_pc>$nom_perso_carte</font> [$id_perso_carte]</td></tr>');\" onMouseOut=\"this.src='../images_perso/$image_perso';HideBulle();\" >
														<input type=\"hidden\" name=\"hid_action_cible\" value=\"$id_perso_carte,$id_action\" >
													  </td>";
												echo "</form>";
											}
											else {
												// PNJ
												if($id_perso_carte < 50000 && isset($id_perso_carte) && $id_perso_carte != $id_perso){
													echo "<form method=\"post\" action=\"action.php\" >";
													echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"> <input type=\"image\" name=\"action_cible\" value=\"$id_perso_carte,$id_action\" border=0 src=\"../images_perso/".$image_perso."\" width=40 height=40 onMouseOver=\"this.src='../images/$image_action';AffBulle('<tr><td>+ Soigner +</td></tr><tr><td>$nom_pnj [$id_perso_carte]</td></tr>');\" onMouseOut=\"this.src='../images_perso/$image_pnj';HideBulle();\" ><input type=\"hidden\" name=\"hid_action_cible\" value=\"$id_perso_carte,$id_action\" ></td>";
													echo "</form>";
												}
												else {
													echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"><img border=0 src=\"../images_perso/".$tab["image_carte"]."\" width=40 height=40 \></td>";
												}
											}
										}
										else{
											//positionnement du fond
											$fond_carte = $tab["fond_carte"];
											
											echo "<td width=40 height=40> <img border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 ></td>";
										}
										$tab = $res->fetch_assoc();
									}
									else //les coordonnees sont hors limites
										echo "<td width=40 height=40><img border=0 width=40 height=40 src=\"../fond_carte/decorO.jpg\"></td>";
								}
								echo "</tr>";
							}
							echo "</table>";
							// fin de la generation de la carte
						}
						
						// lien annuler
						echo "<br /><br /><center><a href='jouer.php'><b>[ annuler ]</b></a></center>";
					}
				}
				
				// action ayant pour cible une case
				if($case_action){
					
					if(!in_bat($id_perso)){
						
						// action pouvant affecter les cases aux alentours du perso
						if($portee_action){
							
							if ($nom_action=='Construire - fort' || $nom_action=='Construire - fortin' || $nom_action == 'Construire - hopital'
								|| $nom_action == 'Construire - entrepot d\'armes' || $nom_action == 'Construire - tour de garde' || $nom_action == 'Construire - tour de visu'
								|| $nom_action == 'Construire - pont' || $nom_action == 'Construire - barricade' || $nom_action == 'Construire - route'){
								
								// recuperation du batiment
								$sql = "SELECT batiment.id_batiment, batiment.nom_batiment, clan 
										FROM action_as_batiment, batiment, perso
										WHERE id_action='$id_action'
										AND id_perso='$id_perso'
										AND batiment.id_batiment=action_as_batiment.id_batiment";
								$res = $mysqli->query($sql);
								$num_bat = $res->num_rows;
								
								if($num_bat){
									
									$t_bat = $res->fetch_assoc();
									$id_bat = $t_bat['id_batiment'];
									$nom_batiment = $t_bat['nom_batiment'];
									$camp_batiment = $t_bat['clan'];
									
									if($camp_batiment == '1'){
										$camp_b = 'b';
									}
									if($camp_batiment == '2'){
										$camp_b = 'r';
									}
									$image_bat = "b".$id_bat."".$camp_b.".png";
								}
								
								echo "<center><img src=\"../images_perso/$image_bat\" alt=\"$nom_batiment\" /></center>";
								echo "<center>$nom_batiment</center>";
							
								//recuperation des coordonnees du perso
								$sql = "SELECT x_perso, y_perso FROM perso WHERE id_perso='$id_perso'";
								$res = $mysqli->query($sql);
								$t_coord = $res->fetch_assoc();
								
								$x_perso = $t_coord['x_perso'];
								$y_perso = $t_coord['y_perso'];
								
								// recuperation des donnees de la carte
								$sql = "SELECT x_carte, y_carte, fond_carte, occupee_carte, image_carte, idPerso_carte FROM $carte WHERE x_carte >= $x_perso - 1 AND x_carte <= $x_perso + 1 AND y_carte <= $y_perso + 1 AND y_carte >= $y_perso - 1 ORDER BY y_carte DESC, x_carte";
								$res = $mysqli->query($sql);
								$tab = $res->fetch_assoc(); 
								
								//<!--Generation de la carte-->
								echo '<table border=0 align="center" cellspacing="0" cellpadding="0" style:no-padding>';
								
								echo "<tr><td>y \ x</td>";  //affichage des abscisses
								for ($i = $x_perso - 1; $i <= $x_perso + 1; $i++) {
									echo "<th width=40 height=40>$i</th>";
								}
								echo "</tr>";
								
								for ($y = $y_perso + 1; $y >= $y_perso - 1; $y--) {
									echo "<th>$y</th>";
									for ($x = $x_perso - 1; $x <= $x_perso + 1; $x++) {
										
										//les coordonnees sont dans les limites
										if ($x >= X_MIN && $y >= Y_MIN && $x <= $X_MAX && $y <= $Y_MAX) {
											
											if ($tab["occupee_carte"]){
												echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"><img border=0 src=\"../images_perso/".$tab["image_carte"]."\" width=40 height=40 \></td>";
											}
											else{
												echo "<form method=\"post\" action=\"action.php\" >";
											
												//positionnement du fond
												$fond_carte = $tab["fond_carte"];
												
												//barricade, tours, batiments => constructibles sur plaine seulement
												if($id_bat == '1' || $id_bat == '2' || $id_bat == '3' || $id_bat == '6' || $id_bat == '7' || $id_bat == '8' || $id_bat == '9'){
													if($fond_carte == '1.gif'){
														echo "<td width=40 height=40> <input type=\"image\" name=\"image_bat\" value=\"$x,$y,$id_bat\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../images_perso/$image_bat';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_image_bat\" value=\"$x,$y,$id_bat\" ></td>";
													}
													else {
														echo "<td width=40 height=40> <img border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 ></td>";
													}
												}
												// ponts => constructibles sur eau seulement
												else if($id_bat == '5'){
													if($fond_carte == '8.gif' || $fond_carte == '9.gif'){
														echo "<td width=40 height=40> <input type=\"image\" name=\"image_bat\" value=\"$x,$y,$id_bat\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../images_perso/$image_bat';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_image_bat\" value=\"$x,$y,$id_bat\" ></td>";
													}
													else {
														echo "<td width=40 height=40> <img border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 ></td>";
													}
												}
												// routes => constructibles sur tout terrain sauf eau
												else if($id_bat == '4'){
													if($fond_carte != '8.gif' && $fond_carte != '9.gif'){
														echo "<td width=40 height=40> <input type=\"image\" name=\"image_bat\" value=\"$x,$y,$id_bat\" border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 onMouseOver=\"this.src='../images_perso/$image_bat';\" onMouseOut=\"this.src='../fond_carte/$fond_carte';\" ><input type=\"hidden\" name=\"hid_image_bat\" value=\"$x,$y,$id_bat\" ></td>";
													}
													else {
														echo "<td width=40 height=40> <img border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 ></td>";
													}
												}
												else {
													echo "<td width=40 height=40> <img border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 ></td>";
												}
												echo "</form>";
											}
											$tab = $res->fetch_assoc();
										}
										else //les coordonnees sont hors limites
											echo "<td width=40 height=40><img border=0 width=40 height=40 src=\"../fond_carte/decorO.jpg\"></td>";
									}
									echo "</tr>";
								}
								echo "</table>";
								// fin de la generation de la carte
								
								// lien annuler
								echo "<br /><br /><center><a href='jouer.php'><b>[ annuler ]</b></a></center>";
							}
							
							// reparer batiment
							if($nom_action == 'Réparer bâtiment'){
								
								echo "<center><h2>$nom_action</h2></center>";
							
								//recuperation des coordonnees du perso
								$sql = "SELECT x_perso, y_perso, clan FROM perso WHERE id_perso='$id_perso'";
								$res = $mysqli->query($sql);
								$t_coord = $res->fetch_assoc();
								
								$x_perso = $t_coord['x_perso'];
								$y_perso = $t_coord['y_perso'];
								$clan_perso = $t_coord['clan'];
								
								// recuperation des donnees de la carte
								$sql = "SELECT x_carte, y_carte, fond_carte, occupee_carte, image_carte, idPerso_carte FROM $carte WHERE x_carte >= $x_perso - 1 AND x_carte <= $x_perso + 1 AND y_carte <= $y_perso + 1 AND y_carte >= $y_perso - 1 ORDER BY y_carte DESC, x_carte";
								$res = $mysqli->query($sql);
								$tab = $res->fetch_assoc(); 
								
								//<!--Generation de la carte-->
								echo '<table border=0 align="center" cellspacing="0" cellpadding="0" style:no-padding>';
								
								echo "<tr><td>y \ x</td>";  //affichage des abscisses
								for ($i = $x_perso - 1; $i <= $x_perso + 1; $i++) {
									echo "<th width=40 height=40>$i</th>";
								}
								echo "</tr>";
								
								for ($y = $y_perso + 1; $y >= $y_perso - 1; $y--) {
									echo "<th>$y</th>";
									for ($x = $x_perso - 1; $x <= $x_perso + 1; $x++) {
										
										//les coordonnees sont dans les limites
										if ($x >= X_MIN && $y >= Y_MIN && $x <= $X_MAX && $y <= $Y_MAX) {
											
											if ($tab["occupee_carte"]){
												
												$image_bat = $tab["image_carte"];
												$id_bat_carte = $tab["idPerso_carte"];
												if($id_bat_carte > 50000 && isset($id_bat_carte)){
													
													// recuperation des infos du batiment
													$sql_bat_carte = "SELECT nom_batiment, nom_instance, camp_instance, pv_instance, pvMax_instance FROM batiment, instance_batiment WHERE id_instanceBat=$id_bat_carte AND batiment.id_batiment = instance_batiment.id_batiment";
													$res_bat_carte = $mysqli->query($sql_bat_carte);
													$t_bat_carte = $res_bat_carte->fetch_assoc();
													
													$nom_bat_carte = $t_bat_carte["nom_batiment"];
													$clan_bat_carte = $t_bat_carte["camp_instance"];
													
													if($clan_bat_carte == $clan_perso){
														$clan_pc = 'blue';
													}
													else {
														$clan_pc = 'red';
													}
												
													echo "<form method=\"post\" action=\"action.php\" >";
													echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"> <input type=\"image\" name=\"action_cible_bat\" value=\"$id_bat_carte,$id_action\" border=0 src=\"../images_perso/".$image_bat."\" width=40 height=40 onMouseOver=\"this.src='../images/$image_action';AffBulle('<tr><td>+ Reparer +</td></tr><tr><td><font color=$clan_pc>$nom_bat_carte</font> [$id_bat_carte]</td></tr>');\" onMouseOut=\"this.src='../images_perso/$image_bat';HideBulle();\" ><input type=\"hidden\" name=\"hid_action_cible_bat\" value=\"$id_bat_carte,$id_action\" ></td>";
													echo "</form>";
												}
												else {
													echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"><img border=0 src=\"../images_perso/".$tab["image_carte"]."\" width=40 height=40 \></td>";
												}
											}
											else{
												//positionnement du fond
												$fond_carte = $tab["fond_carte"];
												
												echo "<td width=40 height=40> <img border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 ></td>";
											}
											$tab = $res->fetch_assoc();
										}
										else //les coordonnees sont hors limites
											echo "<td width=40 height=40><img border=0 width=40 height=40 src=\"../fond_carte/decorO.jpg\"></td>";
									}
									echo "</tr>";
								}
								echo "</table>";
								// fin de la generation de la carte
								
								// lien annuler
								echo "<br /><br /><center><a href='jouer.php'><b>[ annuler ]</b></a></center>";
							}
							
							// upgrade batiment
							if($nom_action == 'Upgrade bâtiment'){
								
								echo "<center><h2>$nom_action</h2></center>";
							
								//recuperation des infos du perso
								$sql = "SELECT x_perso, y_perso, clan FROM perso WHERE id_perso='$id_perso'";
								$res = $mysqli->query($sql);
								$t_coord = $res->fetch_assoc();
								
								$x_perso = $t_coord['x_perso'];
								$y_perso = $t_coord['y_perso'];
								$clan_perso = $t_coord['clan'];
								
								// recuperation des donnees de la carte
								$sql = "SELECT x_carte, y_carte, fond_carte, occupee_carte, image_carte, idPerso_carte FROM $carte WHERE x_carte >= $x_perso - 1 AND x_carte <= $x_perso + 1 AND y_carte <= $y_perso + 1 AND y_carte >= $y_perso - 1 ORDER BY y_carte DESC, x_carte";
								$res = $mysqli->query($sql);
								$tab = $res->fetch_assoc(); 
								
								//<!--Generation de la carte-->
								echo '<table border=0 align="center" cellspacing="0" cellpadding="0" style:no-padding>';
								
								echo "<tr><td>y \ x</td>";  //affichage des abscisses
								for ($i = $x_perso - 1; $i <= $x_perso + 1; $i++) {
									echo "<th width=40 height=40>$i</th>";
								}
								echo "</tr>";
								
								for ($y = $y_perso + 1; $y >= $y_perso - 1; $y--) {
									echo "<th>$y</th>";
									for ($x = $x_perso - 1; $x <= $x_perso + 1; $x++) {
										
										//les coordonnees sont dans les limites
										if ($x >= X_MIN && $y >= Y_MIN && $x <= $X_MAX && $y <= $Y_MAX) {
											
											if ($tab["occupee_carte"]){
											
												$image_bat = $tab["image_carte"];
												$id_bat_carte = $tab["idPerso_carte"];
												
												if($id_bat_carte > 50000 && isset($id_bat_carte)){
												
													// recuperation des infos du batiment
													$sql_bat_carte = "SELECT nom_batiment, nom_instance, camp_instance, pv_instance, pvMax_instance FROM batiment, instance_batiment WHERE id_instanceBat=$id_bat_carte AND batiment.id_batiment = instance_batiment.id_batiment";
													$res_bat_carte = $mysqli->query($sql_bat_carte);
													$t_bat_carte = $res_bat_carte->fetch_assoc();
													$nom_bat_carte = $t_bat_carte["nom_batiment"];
													$clan_bat_carte = $t_bat_carte["camp_instance"];
													
													if($clan_bat_carte == $clan_perso){
														$clan_pc = 'blue';
														echo "<form method=\"post\" action=\"action.php\" >";
														echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"> <input type=\"image\" name=\"action_cible_bat\" value=\"$id_bat_carte,$id_action\" border=0 src=\"../images_perso/".$image_bat."\" width=40 height=40 onMouseOver=\"this.src='../images/$image_action';AffBulle('<tr><td>+ Upgrader au niveau sup�rieur +</td></tr><tr><td><font color=$clan_pc>$nom_bat_carte</font> [$id_bat_carte]</td></tr>');\" onMouseOut=\"this.src='../images_perso/$image_bat';HideBulle();\" ><input type=\"hidden\" name=\"hid_action_cible_bat\" value=\"$id_bat_carte,$id_action\" ></td>";
														echo "</form>";
													}
													else {
														echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"><img border=0 src=\"../images_perso/".$tab["image_carte"]."\" width=40 height=40 \></td>";
													}
												}
												else {
													echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"><img border=0 src=\"../images_perso/".$tab["image_carte"]."\" width=40 height=40 \></td>";
												}
											}
											else{
												//positionnement du fond
												$fond_carte = $tab["fond_carte"];
												
												echo "<td width=40 height=40> <img border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 ></td>";
											}
											$tab = $res->fetch_assoc();
										}
										else //les coordonnees sont hors limites
											echo "<td width=40 height=40><img border=0 width=40 height=40 src=\"../fond_carte/decorO.jpg\"></td>";
									}
									echo "</tr>";
								}
								echo "</table>";
								// fin de la generation de la carte
								
								// lien annuler
								echo "<br /><br /><center><a href='jouer.php'><b>[ annuler ]</b></a></center>";
							}
							
							// upgrade batiment expert
							if($nom_action == 'Upgrade bâtiment Expert'){
								echo "<center><h2>$nom_action</h2></center>";
							
								//recuperation des infos du perso
								$sql = "SELECT x_perso, y_perso, clan FROM perso WHERE id_perso='$id_perso'";
								$res = $mysqli->query($sql);
								$t_coord = $res->fetch_assoc();
								
								$x_perso = $t_coord['x_perso'];
								$y_perso = $t_coord['y_perso'];
								$clan_perso = $t_coord['clan'];
								
								// recuperation des donnees de la carte
								$sql = "SELECT x_carte, y_carte, fond_carte, occupee_carte, image_carte, idPerso_carte FROM $carte WHERE x_carte >= $x_perso - 1 AND x_carte <= $x_perso + 1 AND y_carte <= $y_perso + 1 AND y_carte >= $y_perso - 1 ORDER BY y_carte DESC, x_carte";
								$res = $mysqli->query($sql);
								$tab = $res->fetch_assoc(); 
								
								//<!--Generation de la carte-->
								echo '<table border=0 align="center" cellspacing="0" cellpadding="0" style:no-padding>';
								
								echo "<tr><td>y \ x</td>";  //affichage des abscisses
								for ($i = $x_perso - 1; $i <= $x_perso + 1; $i++) {
									echo "<th width=40 height=40>$i</th>";
								}
								echo "</tr>";
								
								for ($y = $y_perso + 1; $y >= $y_perso - 1; $y--) {
									echo "<th>$y</th>";
									for ($x = $x_perso - 1; $x <= $x_perso + 1; $x++) {
										if ($x >= X_MIN && $y >= Y_MIN && $x <= $X_MAX && $y <= $Y_MAX) { //les coordonnees sont dans les limites
											if ($tab["occupee_carte"]){
											
												$image_bat = $tab["image_carte"];
												$id_bat_carte = $tab["idPerso_carte"];
												
												if($id_bat_carte > 50000 && isset($id_bat_carte)){
												
													// recuperation des infos du batiment
													$sql_bat_carte = "SELECT nom_batiment, nom_instance, camp_instance, pv_instance, pvMax_instance FROM batiment, instance_batiment WHERE id_instanceBat=$id_bat_carte AND batiment.id_batiment = instance_batiment.id_batiment";
													$res_bat_carte = $mysqli->query($sql_bat_carte);
													$t_bat_carte = $res_bat_carte->fetch_assoc();
													$nom_bat_carte = $t_bat_carte["nom_batiment"];
													$clan_bat_carte = $t_bat_carte["camp_instance"];
													
													if($clan_bat_carte == $clan_perso){
														$clan_pc = 'blue';
														echo "<form method=\"post\" action=\"action.php\" >";
														echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"> <input type=\"image\" name=\"action_cible_bat\" value=\"$id_bat_carte,$id_action\" border=0 src=\"../images_perso/".$image_bat."\" width=40 height=40 onMouseOver=\"this.src='../images/$image_action';AffBulle('<tr><td>+ Reparer +</td></tr><tr><td><font color=$clan_pc>$nom_bat_carte</font> [$id_bat_carte]</td></tr>');\" onMouseOut=\"this.src='../images_perso/$image_bat';HideBulle();\" ><input type=\"hidden\" name=\"hid_action_cible_bat\" value=\"$id_bat_carte,$id_action\" ></td>";
														echo "</form>";
													}
													else {
														echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"><img border=0 src=\"../images_perso/".$tab["image_carte"]."\" width=40 height=40 \></td>";
													}
												}
												else {
													echo "<td width=40 height=40 background=\"../fond_carte/".$tab["fond_carte"]."\"><img border=0 src=\"../images_perso/".$tab["image_carte"]."\" width=40 height=40 \></td>";
												}
											}
											else{
												//positionnement du fond
												$fond_carte = $tab["fond_carte"];
												
												echo "<td width=40 height=40> <img border=0 src=\"../fond_carte/$fond_carte\" width=40 height=40 ></td>";
											}
											$tab = $res->fetch_assoc();
										}
										else //les coordonnees sont hors limites
											echo "<td width=40 height=40><img border=0 width=40 height=40 src=\"../fond_carte/decorO.jpg\"></td>";
									}
									echo "</tr>";
								}
								echo "</table>";
								// fin de la generation de la carte
								
								// lien annuler
								echo "<br /><br /><center><a href='jouer.php'><b>[ annuler ]</b></a></center>";
							}
						}
						// action a faire sur la case courante du perso
						else {
							
							//couper du bois
							if($nom_action=='Couper du bois'){
								action_couper_bois($mysqli, $id_perso, $id_action, $nb_points_action);
							}
							
							// saboter
							if($nom_action == 'Saboter'){
								action_saboter($mysqli, $id_perso, $id_action, $nb_points_action);
							}
							
							// planter arbre
							if($nom_action == 'Planter arbre'){
								action_planterArbre($mysqli, $id_perso, $id_action, $nb_points_action);
							}
							
							// deposer objet
							if($nom_action == 'Deposer objet'){
								
								// lien retour
								echo "<br /><center><a href='jouer.php'><b>[ retour ]</b></a></center><br />";
							
								echo "<table border='1' align='center' width='50%'><tr><th colspan='4'>Objets déposables</th></tr>";
								echo "<tr><th>image</td><th>poid unitaire</td><th>nombre</th><th>déposer à terre ?</th></tr>";
								
								// Recuperation des objets / armes / armures que possede le perso
								// Objets
								$sql_o = "SELECT DISTINCT id_objet FROM perso_as_objet WHERE id_perso='$id_perso' ORDER BY id_objet";
								$res_o = $mysqli->query($sql_o);
								
								while($t_o = $res_o->fetch_assoc()){
									
									$id_objet = $t_o["id_objet"];
									
									// recuperation des carac de l'objet
									$sql1_o = "SELECT nom_objet, poids_objet FROM objet WHERE id_objet='$id_objet'";
									$res1_o = $mysqli->query($sql1_o);
									$t1_o = $res1_o->fetch_assoc();
									$nom_o = $t1_o["nom_objet"];
									$poids_o = $t1_o["poids_objet"];
									
									// recuperation du nombre d'objet de ce type que possede le perso
									$sql2_o = "SELECT id_objet FROM perso_as_objet WHERE id_perso='$id_perso' AND id_objet='$id_objet'";
									$res2_o = $mysqli->query($sql2_o);
									$nb_o = $res2_o->num_rows;
									
									echo "<tr>";
									echo "<td align='center'><dl><dd><a href='#'><img src='../images/objet".$id_objet.".png' alt='$nom_o' height='50' width='50'/><span><b>".stripslashes($nom_o)."</b></span></a></dd></dl></td>";
									echo "<td align='center'>$poids_o</td>";
									echo "<td align='center'>$nb_o</td>";
									echo "<form method='post' action='action.php'>";
									echo "<td align='center'><input type='submit' name='valid_objet_depo' value='oui' /><input type='hidden' name='id_objet_depo' value='$id_objet,2,0' /></td>";
									echo "</form>";
									echo "</tr>";
								}
								
								// Armes non portes
								$sql_a1 = "SELECT DISTINCT id_arme, pv_arme FROM perso_as_arme WHERE id_perso='$id_perso' AND est_portee='0' ORDER BY id_arme";
								$res_a1 = $mysqli->query($sql_a1);
								
								while($t_a1 = $res_a1->fetch_assoc()){
									
									$id_arme = $t_a1["id_arme"];
									$pv_arme = $t_a1["pv_arme"];
									
									// recuperation des carac de l'arme
									$sql1_a1 = "SELECT nom_arme, poids_arme, image_arme FROM arme WHERE id_arme='$id_arme'";
									$res1_a1 = $mysqli->query($sql1_a1);
									$t1_a1 = $res1_a1->fetch_assoc();
									$nom_a1 = $t1_a1["nom_arme"];
									$poids_a1 = $t1_a1["poids_arme"];
									$image_arme = $t1_a1["image_arme"];
									
									// recuperation du nombre d'armes non equipes de ce type et ayant ce nombre de pv que possede le perso 
									$sql2_a1 = "SELECT id_arme FROM perso_as_arme WHERE id_perso='$id_perso' AND id_arme='$id_arme' AND est_portee='0' AND pv_arme='$pv_arme'";
									$res2_a1 = $mysqli->query($sql2_a1);
									$nb_a1 = $res2_a1->num_rows;
									
									echo "<tr>";
									echo "<td align='center'><dl><dd><a href='#'><img src='../images/armes/$image_arme' alt='$nom_a1' height='50' width='50'/><span><b>".stripslashes($nom_a1)."</b><br /><u>Pv :</u> ".$pv_arme."</span></a></dd></dl></td>";
									echo "<td align='center'>$poids_a1</td>";
									echo "<td align='center'>$nb_a1</td>";
									echo "<form method='post' action='action.php'>";
									echo "<td align='center'><input type='submit' name='valid_objet_depo' value='oui' /><input type='hidden' name='id_objet_depo' value='$id_arme,3,$pv_arme' /></td>";
									echo "</form>";
									echo "</tr>";
								}
								
								// Armures non portes
								$sql_a2 = "SELECT DISTINCT id_armure, pv_armure FROM perso_as_armure WHERE id_perso='$id_perso' AND est_portee='0' ORDER BY id_armure";
								$res_a2 = $mysqli->query($sql_a2);
								while($t_a2 = $res_a2->fetch_assoc()){
									
									$id_armure = $t_a2["id_armure"];
									$pv_armure = $t_a2["pv_armure"];
									
									// recuperation des carac de l'arme
									$sql1_a2 = "SELECT nom_armure, poids_armure, image_armure FROM armure WHERE id_armure='$id_armure'";
									$res1_a2 = $mysqli->query($sql1_a2);
									$t1_a2 = $res1_a2->fetch_assoc();
									$nom_a2 = $t1_a2["nom_armure"];
									$poids_a2 = $t1_a2["poids_armure"];
									$image_armure = $t1_a2["image_armure"];
									
									// recuperation du nombre d'armes non equipes de ce type que possede le perso 
									$sql2_a2 = "SELECT id_armure FROM perso_as_armure WHERE id_perso='$id_perso' AND id_armure='$id_armure' AND est_portee='0' AND pv_armure='$pv_armure'";
									$res2_a2 = $mysqli->query($sql2_a2);
									$nb_a2 = $res2_a2->num_rows;
									
									echo "<tr>";
									echo "<td align='center'><dl><dd><a href='#'><img src='../images/armures/$image_armure' alt='$nom_a2' height='50' width='50'/><span><b>".stripslashes($nom_a2)."</b><br /><u>Pv :</u> ".$pv_armure."</span></a></dd></dl></td>";
									echo "<td align='center'>$poids_a2</td>";
									echo "<td align='center'>$nb_a2</td>";
									echo "<form method='post' action='action.php'>";
									echo "<td align='center'><input type='submit' name='valid_objet_depo' value='oui' /><input type='hidden' name='id_objet_depo' value='$id_armure,4,$pv_armure' /></td>";
									echo "</form>";
									echo "</tr>";
								}
								
								echo "</table><br /><br /><br /><br /><br /><br />";
							}
							
							// Ramasser objet
							if($nom_action == 'Ramasser objet'){
								
								// lien retour
								echo "<br /><center><a href='jouer.php'><b>[ retour ]</b></a></center><br />";
							
								echo "<table border='1' align='center' width='50%'><tr><th colspan='4'>Objets ramassable</th></tr>";
								echo "<tr><th>image</td><th>poid unitaire</td><th>nombre</th><th>ramasser ?</th></tr>";
								
								// Recuperation des objets a terre
								$sql = "SELECT type_objet, id_objet, nb_objet, pv_objet FROM objet_in_carte, perso WHERE id_perso='$id_perso' AND x_carte=x_perso AND y_carte=y_perso";
								$res = $mysqli->query($sql);
								$nb = $res->num_rows;
								
								if($nb){
									
									// Il y a des objets a ramasser
									while($t = $res->fetch_assoc()){
										
										$type_objet = $t["type_objet"];
										$id_objet = $t["id_objet"];
										$nb_objet = $t["nb_objet"];
										$pv_objet = $t["pv_objet"];
										
										// Récupération des infos sur les objets à terre
										// Objets
										if($type_objet == '2'){
											
											$sql_o = "SELECT nom_objet, poids_objet, description_objet FROM objet WHERE id_objet='$id_objet'";
											$res_o = $mysqli->query($sql_o);
											$t_o = $res_o->fetch_assoc();
											
											$nom_objet = $t_o["nom_objet"];
											$poids_objet = $t_o["poids_objet"];
											$description_objet = $t_o["description_objet"];
											
											echo "<tr>";
											echo "<td align='center'><dl><dd><a href='#'><img src='../images/objet".$id_objet.".png' alt='$nom_objet' height='50' width='50'/><span><b>".stripslashes($nom_objet)."</b><br />".stripslashes($description_objet)."</span></a></dd></dl></td>";
											echo "<td align='center'>$poids_objet</td>";
											echo "<td align='center'>$nb_objet</td>";
											echo "<form method='post' action='action.php'>";
											echo "<td align='center'><input type='submit' name='valid_objet_ramasser' value='oui' /><input type='hidden' name='id_objet_ramasser' value='$id_objet,$type_objet,$pv_objet' /></td>";
											echo "</form>";
											echo "</tr>";
										}
										// Armes
										if($type_objet == '3'){
											
											$sql_a1 = "SELECT nom_arme, porteeMin_arme, porteeMax_arme, image_arme, additionMin_degats, additionMax_degats, multiplicateurMin_degats, multiplicateurMax_degats, degatMin_arme, degatMax_arme, degatZone_arme, poids_arme, description_arme FROM arme
													   WHERE id_arme='$id_objet'";
											$res_a1 = $mysqli->query($sql_a1);
											$t_a1 = $res_a1->fetch_assoc();
											
											$nom_objet = $t_a1["nom_arme"];
											$poids_objet = $t_a1["poids_arme"];
											$description_objet = $t_a1["description_arme"];
											
											// image de l'arme
											$image_arme = $t_a1["image_arme"];
											
											// Portee de l'arme
											$porteeMin_arme = $t_a1["porteeMin_arme"];
											$porteeMax_arme = $t_a1["porteeMax_arme"];
											$portee_arme = $porteeMin_arme." - ".$porteeMax_arme;
											
											// degats de l'arme
											$additionMin_degats = $t_a1["additionMin_degats"];
											$additionMax_degats = $t_a1["additionMax_degats"];
											$multiplicateurMin_degats = $t_a1["multiplicateurMin_degats"];
											$multiplicateurMax_degats = $t_a1["multiplicateurMax_degats"];
											$degatMin_arme = $t_a1["degatMin_arme"];
											$degatMax_arme = $t_a1["degatMax_arme"];
											
											if($degatMin_arme && $degatMax_arme){
												$degats_arme = $degatMin_arme." - ".$degatMax_arme;
											}
											else {
												$degats_arme = "D";
												if($multiplicateurMin_degats != 1)
													$degats_arme += "*".$multiplicateurMin_degats;
												$degats_arme += " + ".$additionMin_degats." - D";
												if($multiplicateurMax_degats != 1)
													$degats_arme += "*".$multiplicateurMax_degats;
												$degats_arme += " + ".$additionMax_degats;
											}
											
											echo "<tr>";
											echo "<td align='center'><dl><dd><a href='#'><img src='../images/armes/".$image_arme."' alt='$nom_objet' height='50' width='50'/><span><b>".stripslashes($nom_objet)."</b><br /><u>Pv :</u> ".$pv_objet."<br /><u>Portee :</u> ".$portee_arme."<br /><u>Degats :</u> ".$degats_arme."<br />".stripslashes($description_objet)."</span></a></dd></dl></td>";
											echo "<td align='center'>$poids_objet</td>";
											echo "<td align='center'>$nb_objet</td>";
											echo "<form method='post' action='action.php'>";
											echo "<td align='center'><input type='submit' name='valid_objet_ramasser' value='oui' /><input type='hidden' name='id_objet_ramasser' value='$id_objet,$type_objet,$pv_objet' /></td>";
											echo "</form>";
											echo "</tr>";
										}
										
										//Armures
										if($type_objet == '4'){
											
											$sql_a2 = "SELECT nom_armure, poids_armure, description_armure, image_armure, bonusDefense_armure FROM armure WHERE id_armure='$id_objet'";
											$res_a2 = $mysqli->query($sql_a2);
											$t_a2 = $res_a2->fetch_assoc();
											
											$nom_objet = $t_a2["nom_armure"];
											$poids_objet = $t_a2["poids_armure"];
											$description_objet = $t_a2["description_armure"];
											
											// image armure
											$image_armure = $t_a2["image_armure"];
											
											// defense armure
											$defense_armure = $t_a2["bonusDefense_armure"];
											
											echo "<tr>";
											echo "<td align='center'><dl><dd><a href='#'><img src='../images/armures/".$image_armure."' alt='$nom_objet' height='50' width='50'/><span><b>".stripslashes($nom_objet)."</b><br /><u>Pv :</u> ".$pv_objet."<br /><u>Defense :</u> ".$defense_armure."<br />".stripslashes($description_objet)."</span></a></dd></dl></td>";
											echo "<td align='center'>$poids_objet</td>";
											echo "<td align='center'>$nb_objet</td>";
											echo "<form method='post' action='action.php'>";
											echo "<td align='center'><input type='submit' name='valid_objet_ramasser' value='oui' /><input type='hidden' name='id_objet_ramasser' value='$id_objet,$type_objet,$pv_objet' /></td>";
											echo "</form>";
											echo "</tr>";
										}
									}
								}
								else {
									echo "<tr><td colspan='4'><i>Il n'y a aucun objet par terre</i></td></tr>";
								}
								echo "</table><br /><br /><br /><br /><br /><br />";
							}
						}
					}
					else {
						echo "<center><font color='red'>Impossible d'effectuer cette action depuis un bâtiment, veuillez sortir pour effectuer cette action</font>";
						echo "<br /><a href='jouer.php'>[ retour ]</a></center>";
					}
				}				
			}
			else {
				// triche
				// -- TODO --
				
				// redirection
				header("location:jouer.php");
			}
		}
		else {
			if(isset($_POST['liste_action']) && $_POST['liste_action'] == 'PA'){
				echo "Pas assez de PA";
				echo "<center><a href='jouer.php'>[retour]</a></center>";
			}
			if(isset($_POST['liste_action']) && $_POST['liste_action'] == 'invalide'){
				echo "Invalide";
				echo "<center><a href='jouer.php'>[retour]</a></center>";
			}
		}
	}
	
	?>
<?php
}
else {
	// logout
	$_SESSION = array(); // On écrase le tableau de session
	session_destroy(); // On détruit la session
	
	header("Location: index2.php");
}
?>