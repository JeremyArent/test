<?php

/*
Suppression des factures valid�es depuis plus d'un an. 
Utilisation de proc�dure CRON qui ex�cute ce code chaque jour.
*/

// Connexion � notre base de donn�es
require('connexion/connexDB.php');

// On s�lectionne les commandes Valider depuis plus d'un an
$recup = "SELECT commande.*, client.*, livre.* FROM client, livre, commande WHERE client.id = commande.id_client AND livre.id = commande.id_livre
		   AND statut ='Valider' AND CURDATE() - commande.date > 365";
$req = mysql_query($recup);
// On r�cup�re tous les �lements n�cessaire
while($row = mysql_fetch_array($req)){
	$num_com = $row['num_com'];
	$id_client = $row['id_client'];
	$nom = $row['nom'];
	$prenom = $row['prenom'];
	$courriel = $row['courriel'];
	$adresse = $row['adresse'];
	$ville = $row['ville'];
	$cp = $row['code_postal'];
	$id_livre = $row['id_livre'];
	$titre = $row['titre'];
	$prix = $row['prix'];
	$qte = $row['qte'];
	$date = $row['date'];
	
	// On insert ces �l�ments dans la table Archive
	$nouvelinsert = "INSERT INTO archivage (id, num_com, id_client, nom, prenom, courriel, adresse, ville, cp, id_livre, article, prix, qte, date)
					 VALUE ('', '$num_com', '$id_client', '$nom', '$prenom', '$courriel', '$adresse', '$ville', '$cp', '$id_livre', '$titre', '$prix', '$qte', '$date')";
	$ok = mysql_query($nouvelinsert);
	$fait = "ok";
}

/* Si l'insertion s'est effectu�e, on supprime la ou les commandes */
if(isset($fait)){
	$supp = "DELETE FROM commande WHERE statut ='Valider' AND CURDATE() - commande.date > 365";
	$reqSupp = mysql_query($supp);
	//Envoie du mail � l'administrateur
	//Destinataire
	$to = "contact@bibliobook.p.ht";
	// Sujet
	$subject = 'Suppression du jour';
	 
	// Message
	$message = '
	<html>
			<head>
					<title>Test Cron</title>
			</head>
			<body>
					<table width="100%" border="0" cellspacing="0" cellpadding="5">
							<tr>
									<td align="center">
											<p>
													Voici les commandes supprim�es ce jour :
											</p>
											<p>
											
											</p>
									</td>
							</tr>
					</table>
			</body>
	</html>
	';
	 
	// Pour envoyer un mail HTML, l en-t�te Content-type doit �tre d�fini
	$headers = "MIME-Version: 1.0" . "\n";
	$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
	 
	// En-t�tes additionnels
	$headers .= 'From: Mail de test <no-reply@monsitedetest.com>' . "\r\n";
	 
	// Envoie
	$resultat = mail($to, $subject, $message, $headers);
}

?>