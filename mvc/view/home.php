<?php
$title = "";

/* ---Content--- */
ob_start();
?>
<div class="row">
	<div class="col-sm-2">
		<div class='row pt-4'>
			<h4>
				<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 align-bottom" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
				</svg>
				Connexion
			</h4>
			<?php
				if (isset($_GET['nouveau_tour']) && $_GET['nouveau_tour'] == 'ok'):
			?>
				<p class='alert alert-danger fw-bold' role="alert">
					<svg xmlns="http://www.w3.org/2000/svg" class="" width='26' height='26' fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					Nouveau tour
				</p>
			<?php
				endif;
			?>
			<form class='' action="login.php" method="post" name="login" id="login">
				<div class="mb-3">
					<label for="pseudo" class='form-label visually-hidden'>Pseudo</label>
					<input type="text" class="form-control form-control-sm" id="pseudo" name='pseudo' placeholder="Pseudo">
				</div>
				<div class="mb-3">
					<label for="pseudo" class='form-label visually-hidden'>Mot de Passe</label>
					<input type="password" class="form-control form-control-sm" id="password" name='password' placeholder="Mot de Passe">
				</div>
				<div class="mb-3">
					<label for="pseudo" class='form-label'>Etes-vous un robot ?</label>
					<div class=''>
						<a href='#' id='reload_captcha' class='mx-2'><img src="captcha.php" id='captcha'/></a>
						<input id='captcha_input' name="captcha" type="text" class="form-control form-control-sm mt-2" placeholder="Entrez le texte de l'image">
					</div>
				</div>
				<div class="mb-3">
					<input class='btn btn-light btn-sm' type="submit" name="Submit" value="Se connecter">
				</div>
				<div>
					<a href="mdp_perdu.php" class='text-light ml-2'>Mot de passe perdu ?</a>
				</div>
			</form>
		</div>
	</div>
	<div class="col-8">
		<div class='row'>
			<div class='col shadow bg-gray-300 rounded-3 p-4 mx-2 h-20'>
				<h4>
					<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 rotate-20deg align-bottom" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
					</svg>
					Inscription
				</h4>
				<p>
					Nombre de joueurs inscrit : <?= $nb_inscrit; ?> <br />
					Dernier inscrit : <?= couleur_nation($clan_last_inscrit, $pseudo_last_inscrit); ?>
				</p>
				<p>
					Persos actifs : <span class='text-primary fw-bold'>nordistes : <?= $nb_persos_nord_actifs; ?></span> / <span class='text-danger fw-bold'>sudistes : <?= $nb_persos_sud_actifs; ?></span>
				</p>
				<p>
					Vous voulez en découdre ?<br/>
					Engagez-vous soldat !<br/>
					<div class='mt-1'>
					<a href="inscription.php" class="btn btn-light">S'inscrire</b></a>
					</div>
				</p>
			</div>
			
			<div class='col shadow bg-gray-300 rounded-3 p-4 mx-2 h-20 news'>
				<h4>
					<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 rotate-20deg align-bottom" fill="none" viewBox="0 0 24 24" stroke="currentColor">
					  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
					</svg>
					Les nouvelles du front
				</h4>
				<?php if($res_news):?>
					<?php foreach($res_news as $new): ?>
						<p>
							<?php
								$d = new DateTime($new['date']);
								echo $d->format('d-m-Y');
							?>
							<br/>
							<?= $new['contenu'] ?><br/>
							-----
						</p>
					<?php endforeach ?>
				<?php else: ?>
				<p>
					Aucune nouvelle... espérons que nos soldats vont bien !
				</p>
				<?php endif; ?>
			</div>
			
		</div>
		<div class='row'>
			<div class='col shadow bg-gray-300 rounded-3 p-2 m-2'>
				<!-- texte à repenser éventuellement -->
				<h3>Synopsis</h3>
				<p>
					Amérique du Nord, printemps 1861. Bienvenue dans la lutte qui oppose le <span class='text-primary fw-bold'>Nord</span> et le <span class='text-danger fw-bold'>Sud</span>.<br />
					Nous sommes à la fin du 19ème siècle et depuis des années, les tensions montent entre l'armée de l'Union, commandée par <span class='text-primary fw-bold'>Abraham Lincoln</span>, et l'armée des Etats confédérés, commandée par <span class='text-danger fw-bold'>Jefferson Davis</span>.<br/>
					<br/>
					le 12 avril, La guerre est déclarée.<br/>
					Une attaque de l'armée des États confédérés sur le Fort Sumter, à Charleston (Caroline du Sud), lance les hostilités. Vous vous retrouvez malgré vous dans cette tourmente et devez choisir un camp.<br/>
					<span class='text-primary fw-bold'>Unionniste</span> ou <span class='text-danger fw-bold'>confédéré ?</span> La décision vous appartient.<br />
				</p>
				<p>
					Vous commencerez en tant que caporal et vous aurez sous vos ordres votre 1er grouillot.<br />
					Au fur et à mesure de vos actions, votre capacité à commander se révéleront. Votre montée en grade vous permettra d'avoir encore plus de grouillots sous vos ordres.<br />
					Mais pour cela, il vous faudra utiliser tous les moyens disponibles : Relief du terrain, protection des bâtiments, achats d'armes et d'objets ainsi que le train à vapeur pour survivre au milieu du camp adverse et des bêtes sauvages.<br /><br />
					Alors, quel camp allez-vous faire gagner ?
					</b>
				</p>
				<!-- texte à repenser et non affiché. Catégorie description du jeu -->
				<p class='d-none'>
					<b>Nord vs Sud</b> est un jeu de stratégie sur Internet largement multi-joueurs.<br />
					Chaque joueur commande un bataillon de quelques unités : <b>cavaliers, infanteries, soigneurs, artillerie, chiens militaires</b>.<br />
					<b>Deux camps</b> s'affrontent depuis la nuit des temps pour des motifs qui ont fini par être oubliés, <span class='text-primary fw-bold'>les bleus (Nord)</span> et <span class='text-danger fw-bold'>les rouges (Sud)</span>.<br />
					Ceux-ci, pour être plus efficaces, ont procédé au regroupement des bataillons en <b>compagnies</b> pouvant aller jusqu'à 80 unités.<br />
					Le Nord a une organisation plutôt hiérarchique avec un Comité Stratégique qui définit les ordres de mission, tandis que le Sud procède avec une plus grande autonomie des compagnies.<br />
					Certains joueurs restent indépendants, ne veulent pas profiter des avantages tactiques et économiques (achat d'équipements) qu'apportent l'enrôlement dans une compagnie, afin de conserver une liberté d'action.<br />
					Car un autre des avantages des compagnies est de réaliser des actions coordonnées, que leurs unités agissent avec simultanéité, pour attaquer les adversaires. Il faut en effet plusieurs attaques avant de réussir à « capturer » une unité.<br />
					Cette « capture » consiste à renvoyer l'unité remontée à bloc dans l'un des bâtiments de son camp.<br />
					Il lui faudra donc ensuite du temps pour rejoindre le front et retrouver le reste de son bataillon.<br />
					L'objectif du jeu est donc de repousser les adversaires en « capturant » leurs unités et de détruire leurs bâtiments, ce qui apporte des points de victoire au camp.<br />
					Dans certains cas il est également possible de capturer un bâtiment ennemi pour l'inclure dans son camp.<br />
					La bataille se termine lorsque un camp est parvenu à <b>1000 points de victoire</b>. Une autre bataille sera donc lancée sur la carte suivante décidée par l'état major du camp vainqueur.<br />
					La surface de jeu (carte) est assez vaste et chaque camp n'en connaît que les zones qu'il a pu visiter.	
				</p>
			</div>
		</div>
	</div>
	<aside class='col'>
			<a href="presentation.php" class='link-light'>Présentation du jeu</b></a>
			<hr />
			<a href="regles/regles.php" class='link-light'>Règles</b></a>
			<hr />
			<a href="faq.php" class='link-light'>FAQ</b></a>
			<hr />
			<a href="/forum" class='link-light'>Le Forum</b></a>
			<hr />
			<a href="jeu/classement.php" class='link-light'>Les classements</b></a>
			<hr />
			<a href="jeu/statistiques.php" class='link-light'>Les statistiques</b></a>
			<hr />
			<a href="credits.php" class='link-light'>Crédits</b></a>
	</aside>
</div>
		
<?php $content = ob_get_clean(); ?>

<?php require('layouts/guest.php'); ?>