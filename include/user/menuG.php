			<div class="slide togglebox <?php echo $hackOperaMini ?>">
				<div id="nav" class="<?php echo $hackOperaMini ?>">
					<ul class="first">

						<li><a href="<?php echo path_to_wiki; ?>doku.php?id=start" class="<?php echo $GLOBALS['menuG_start']; ?>" title="Accueil de Randonner léger">Accueil du site</a></li>

		<?php
		# Si visiteur NON identifié, j'affiche le formulaire d'identification dans le menu gauche
		if ($pun_user['group_id']==3 || $conf['group_id']==3 )
		{
		?>
						<li class="display"><strong>Identification</strong>
						<input id="menug-identification" type="checkbox" name="toggle" />
						<label for="menug-identification"></label>
						<div class="content-togglebox">
							<form id="login_menuG" method="post" action="<?php echo path_to_forum . 'login.php?action=in'; ?>" onsubmit="return process_form(this)">
								<input type="hidden" name="form_sent" value="1" />
								<input type="hidden" name="redirect_url" value="<?php echo 'https://' .$_SERVER['HTTP_HOST'] . htmlentities($_SERVER['REQUEST_URI']); ?>" />
								<input type="hidden" name="csrf_token" value="<?php echo pun_csrf_token() ?>" />

								<input placeholder="Nom d'utilisateur" class="fastlogin" id="username" type="text" name="req_username" size="10" />
								<input placeholder="Mot de passe" class="fastlogin" id="password" type="password" name="req_password" size="10" />
									<div class="save-pass">
									<input name="save_pass" id="save_pass" value="1" type="checkbox" /> 
									<label for="save_pass">Rester connecté</label>
									</div>
								<input type="submit" name="login" value="Connexion" />
								<p><a href="<?php echo path_to_forum; ?>register.php" title="Inscription">Inscription</a></p>
							</form>
						</div>
						</li>
		<?php
		} else {
		# Si visiteur EST identifié, je contextualise ses liens forum ET un lien de déconnexion
		if ($conf['id']==$pun_user['id']) {$conf['id']="";}
		$forum_discussions_suivies = "<li><a href=\"" . path_to_forum . "search.php?action=show_subscriptions&user_id=" . $conf['id'].$pun_user['id'] . "\" title=\"Discussions suivies\">Discussions suivies</a></li>" ;
		$forum_nouveaux_messages = "<li><a href=\"" . path_to_forum . "search.php?action=show_new\" title=\"Nouveaux messages\">Nouveaux messages</a></li>" ;
		$forum_nouveaux_messages .= "<li><a href=\"" . path_to_forum . "search.php?action=show_new_hors_ventes\" title=\"Nouveaux messages hors ventes\">Nouveaux (hors ventes)</a></li>" ;
		$forum_sans_reponses = "<li><a href=\"" . path_to_forum . "search.php?action=show_unanswered\" title=\"Messages sans réponse\">Messages sans réponse</a></li>" ;
		$forum_profil = "<li><a href=\"" . path_to_forum . "profile.php?id=".$conf['id'].$pun_user['id']."\" title=\"Profil\">Profil</a></li>";
#Déconnexion en prod
		$forum_deconnexion = "<li><a href=\"" . path_to_forum. "login.php?action=out&amp;id=".$conf['id'].$pun_user['id']."&amp;csrf_token=".pun_csrf_token()."\" title=\"Déconnexion\">Déconnexion</a></li>";
#Déconnexion sur dev
#		$forum_deconnexion = "<li><a href=\"" . path_to_forum. "login.php?action=out&amp;id=".$pun_user['id']."&amp;csrf_token=".pun_hash($pun_user['id'].pun_hash(get_remote_address()))."\" title=\"Déconnexion\">Déconnexion</a></li>";

		echo $forum_deconnexion ;
		}
		?>

						<li><a href="<?php echo path_to_wiki; ?>doku.php?id=presentation:sommaire" class="<?php echo $GLOBALS['menuG_presentation']; ?>" title="Découverte de la randonnée légère">Découverte</a>
						<input id="menug-wiki-debuter" type="checkbox" <?php if ($GLOBALS['menuG_presentation']=="active") { echo "checked='checked'"; } ?> name="toggle" />
						<label for="menug-wiki-debuter"></label>
						<div class="content-togglebox">
							<ul class="sub-menu">
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=presentation:debuter" title="Débuter le randonnée légère">Débuter</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=faq_de_la_randonnee_legere_mul" title="FAQ de la randonnée legère MUL">Faq</a></li>
							</ul>
						</div>
						</li>

						<li><a href="<?php echo path_to_wiki; ?>doku.php?id=association_rl" class="<?php echo $GLOBALS['menuG_association']; ?>" title="L'association Randonner léger">L'Association</a>
						<input id="menug-wiki-asso" type="checkbox" <?php if ($GLOBALS['menuG_association']=="active") { echo "checked='checked'"; } ?> name="toggle" />
						<label for="menug-wiki-asso"></label>
						<div class="content-togglebox">
							<ul class="sub-menu">
								<li class="highlight"><a href="<?php echo path_to_wiki; ?>doku.php?id=association_rl:adherer" title="Adhérer à l'association">Adhérer à l'association</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=association_rl:historique" title="Historique et fonctionnement">Historique et fonctionnement</a></li>
								<li class="highlight"><a href="<?php echo path_to_wiki; ?>doku.php?id=association_rl:projets:s-investir" title="S'investir dans l'association">S'investir</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=association_rl:historique:cdb" title="Camps de base">Camps de base</a></li>
							</ul>
						</div>
						</li>

						<li><a href="<?php echo path_to_forum; ?>" class="<?php echo $GLOBALS['menuG_forum']; ?>" title="Forum Randonner léger">Forum</a>
						<input id="menug-forum" type="checkbox" <?php if ($GLOBALS['menuG_forum']=="active") { echo "checked='checked'"; } ?> name="toggle" />
						<label for="menug-forum"></label>
						<div class="content-togglebox">
							<ul class="sub-menu">
								<?php echo $forum_discussions_suivies ; ?>
								<?php echo $forum_nouveaux_messages ; ?>
								<li><a href="<?php echo path_to_forum; ?>search.php?action=show_24h" title="Messages récents">Messages récents</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=les_sujets_cles" title="Les sujets clés">Les sujets clés</a></li>
							</ul>
						</div>
						</li>

						<li><a href="<?php echo path_to_wiki; ?>doku.php?id=accueil_du_wiki" class="<?php echo $GLOBALS['menuG_wiki_gene']; ?>" title="Wiki Randonner léger">Wiki</a>
						<input id="menug-wiki" type="checkbox" <?php if ($GLOBALS['menuG_wiki_gene']=="active") { echo "checked='checked'"; } ?> name="toggle" />
						<label for="menug-wiki"></label>
						<div class="content-togglebox">
							<ul class="sub-menu">
								<li><a href="<?php echo path_to_wiki; ?>doku.php?do=recent" title="Derniers changements">Derniers changements</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=index_thematique" title="Index thématique">Index thématique</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=topics_uniques" title="Liste des Topics Uniques">Liste des TU</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=sommaire_recits_par_massifs" title="Sommaire des récits classés par activités, massifs, pays ou régions">Récits</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=sommaire_fiches_conseil" title="Sommaire des fiches conseils">Conseils</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=sommaire_bricolage" title="Sommaire Bricolage">Bricolage</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=sommaire_selection_materiel" title="Sommaire sélection materiel">Sélection matériel</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=lexique" title="Lexique de la randonnée légère">Lexique</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=sommaire_culture" title="Sommaire culture">Culture/Ressources</a></li>
							</ul>
						</div>
						</li>

						<li class="display"><a href="<?php echo path_to_wiki; ?>doku.php?id=guide:sommaire" class="<?php echo $GLOBALS['menuG_guides']; ?>" title="Guide d'utilisation">Guide d'utilisation</a>
						<input id="menug-wiki-guide" type="checkbox" <?php if ($GLOBALS['menuG_guides']=="active") { echo "checked='checked'"; } ?> name="toggle" />
						<label for="menug-wiki-guide"></label>
						<div class="content-togglebox">
							<ul class="sub-menu">
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=guide:charte_forum" title="Charte du forum">Charte du forum</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=guide:notice_des_fonctionnalites_du_forum" title="Notice des fonctionnalités du forum">Notice du forum</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=guide:charte_wiki" title="Charte du Wiki">Charte du wiki</a></li>
								<li><a href="<?php echo path_to_wiki; ?>doku.php?id=guide:notice_du_wiki" title="Notice du wiki">Notice du wiki</a></li>
							</ul>
						</div>
						</li>
					</ul>
				</div><!-- .nav -->
			</div><!-- .slide --><!--

			--><div id="content">
			<div id="mobile-nav-overlay" onClick="CloseOtherMenu('left','forum','search');"></div>


