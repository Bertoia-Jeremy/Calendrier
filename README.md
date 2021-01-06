# Calendrier - Nettoyage hebdomadaire
## Contexte
    - En Septembre 2020 j'ai effectué un stage dans une entreprise d'artisan où j'ai créé seul le site et son back-office.
    Voici le site : [eur-guillermain.fr](https://www.eurl-guillermain.fr/)
    - Au cours de ce stage le client m'a demandé une petite application qui faciliterai 
    le travail du chef d'équipe qui effectuait ses calendriers sur excell.
  
Demande du client :
* Automatiser la création d'un calendrier d'un an avec le nom d'un ouvrier chaque vendredi, 
recommencer les noms s'ils ont tous été utilisés.

Réalisation :

- Possibilité d'ajouter, modifier, supprimer le nom d'un ouvrier.

- Possibilité d'ajouter (indéfiniment) ou non des périodes de vacances.

* Création du calendrier avec : 
	- Jours fériés (Bleu).
	- Week-end (Marron/Rouge).
	- Vacances (Gris).
	- Nom d'un ouvrier chaque vendredi (en gras)


## Pour commencer

    - Modifier le fichier connect.php pour vous connecter à votre base de données.

    - Après avoir choisi le nom de la base de données, créer une table BDD_ouvriers.

    - Ajouter quelques ouvriers et vous pouvez maintenant créer votre calendrier sur votre PC.


## Aperçu du résultat
    
    Voir le PDF Calendrier.pdf


## Fabriqué avec
* [PHP](https://www.php.net/) - Langage Back-end
* [SQL](https://sql.sh/) - Langage Back-end
* [Javascript](https://developer.mozilla.org/fr/docs/Web/JavaScript) - Langage Front-end
* [Bootstrap](https://getbootstrap.com/docs/4.4/getting-started/introduction/) - Framework CSS (front-end)
* [HTML2PDF](https://www.html2pdf.fr/) - Librairie - Convertisseur de code HTML vers PDF
* [Visual Studio Code](https://code.visualstudio.com/) - Editeur de textes



