<?php
include('administration/config.php') ;
 
//on teste l'existance de $_POST['pseudo'] et $_POST['message']
if (isset($_POST['pseudo']) AND isset($_POST['message']) AND ($nbre_erreur==0)) {
	if ($_POST['pseudo'] != NULL AND $_POST['message'] != NULL) {
		$mail_visible = ($_POST['mail_visible'] == oui) ;
		if (get_magic_quotes_gpc()==1) {
			$message = mysql_real_escape_string(stripslashes($_POST['message'])) ;
			$auteur = mysql_real_escape_string(stripslashes($_POST['pseudo'])) ;
			$mail = mysql_real_escape_string(stripslashes($_POST['mail'])) ;
			$localisation = mysql_real_escape_string(stripslashes($_POST['localisation'])) ;
		}
		else{
			$message = mysql_real_escape_string($_POST['message']) ;
			$auteur = mysql_real_escape_string($_POST['pseudo']) ;
			$mail = mysql_real_escape_string($_POST['mail']) ;
			$localisation = mysql_real_escape_string($_POST['localisation']) ;
		}
		// préparation de la requete  
		$sql_1 = 'INSERT INTO message (auteur, texte, mail, mail_visible, date, localisation) VALUES ("'.$auteur.'","'.$message.'","'.$mail.'","'.$mail_visible.'",NOW(),"'.$localisation.'") ;' ;

		// on lance la requête (mysql_query) 
		
		mysql_query($sql_1) ;
		if(mysql_error()) {
			$nbre_erreur = 1 ;
			$erreur = 'Erreur de la requ&ecirc;te' . $sql . '<br />' . mysql_error() ;
		}
	}
}

//on vérifie si $_GET['page']  existe (si la personnne veut afficher une autre page)
if (isset($_GET['page'])) {
	$page = $_GET['page'] ;
}
else {
	$page = 1 ;
}

//on compte le nombre de messages présents dans la base
if ($nbre_erreur == 0) {
	$sql_2 = 'SELECT COUNT(*) AS nbre_entrees FROM message' ;
	$retour_sql_2 = @mysql_query($sql_2) ;
	if(mysql_error()) {
		$nbre_erreur = 1 ;
		$erreur = 'Erreur de la requ&ecirc;te : ' . $sql_2 . '<br />' . mysql_error() ;
	}
	else {
		$donnee_sql_2 = mysql_fetch_array($retour_sql_2) ;
		$nombre_messages = $donnee_sql_2['nbre_entrees'] ;
	}
}

// préparation de la requete  
$sql_3 = 'SELECT * FROM message ORDER BY id DESC LIMIT '. (($page - 1) * $nombreDeMessagesParPage) .', '.($nombreDeMessagesParPage) ;

// on lance la requête (mysql_query) et on impose un message d'erreur si la requête ne se passe pas bien (or die)  
if ($nbre_erreur == 0) {
	$retour_sql_3 = @mysql_query($sql_3) ;
	if(mysql_error()) {
		$nbre_erreur = 1 ;
		$erreur = 'Erreur de la requ&ecirc;te : ' . $sql_3 . '<br />' . mysql_error() ;
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>livre d'or</title>
	</head>
	<body>

	<h1>Bienvenu sur mon livre d'or</h1>

	<p>Vous pouvez ajouter un message.</p>
	<p><?php echo $nombre_messages ?> messages dans le livre d'or.</p>
	<form method="post" action="index.php"><p>
	pseudo : <input type="text" name="pseudo" /><br />
	localisation : <input type="text" name="localisation" /><br />
	mail : <input type="text" name="mail" /><br />
	mail visible : <input type="radio" name="mail_visible" value="oui" /> Oui
	<input type="radio" name="mail_visible" value="non" checked="checked" /> Non <br />
	message : <br /><textarea name="message" rows="8" cols="45"></textarea><br />
	<input type="submit" value="Envoyer" />
	</p></form>

<?php
echo '<p>page : ' , $page , '</p>
<hr />
' ;

if($nbre_erreur == 0) {
// on va scanner tous les tuples un par un
	while ($donnee_sql_3 = mysql_fetch_array($retour_sql_3)) {

// on affiche le nom de l'auteur de sujet 
		echo '<p>' ,htmlspecialchars($donnee_sql_3['auteur']);

// on affiche le mail si la personne l'autorise
		if($donnee_sql_3['mail_visible']) {
			echo ' (' , $donnee_sql_3['mail'] , ') ' ;
		}

// on affiche la date du message 
		echo ' le ' , $donnee_sql_3['date'] , '
';

//On affiche la localisation si elle est rempli
		if ($donnee_sql_3['localisation'] != '') {
			echo ' de ' , $donnee_sql_3['localisation'] ;
		}

		echo '</p><p>' , nl2br($donnee_sql_3['texte']) , '
</p>
' ;

		if ($donnee_sql_3['reponse_webmaster']!='') {
			echo '<p>R&eacute;ponse du Webmaster :</p><p>' , $donnee_sql_3['reponse_webmaster'] , '</p>' ;
		}
	echo '<hr />' ;
	}

	$i = 1;
// Lien vers les pages du livre d'or si on a plus de messages que le nombre maximum par page
	if($nombre_messages > $nombreDeMessagesParPage) {
		echo '<p>' ;
		do {
			echo '<a href="index.php?page=' , $i.'">' , $i , '</a> ' ;
			$i = $i + 1 ;
		} while ($nombre_messages > (($i - 1) * $nombreDeMessagesParPage)) ;
		echo '</p>' ;
	}
}
else {
	echo '<p>Il y a une erreur : <br />' , $erreur , '</p>' ;
}
?>
	<p><a href="administration/administration.php">Page d'administration</a></p>
	</body>
</html>
<?php
if($connexion) {
	if($base) {// on libère l'espace mémoire alloué pour ces requêtes
		if (is_resource($retour_sql_2)) {
			mysql_free_result($retour_sql_2) ;
		}
		if (is_resource($retour_sql_3)) {
			mysql_free_result($retour_sql_3) ;
		}
	}
// on ferme la connexion à la base de données. 
	mysql_close() ;
}
?>