<!-- Ici se situe le tableau affichant la liste des prénoms de tous les ouvriers à mettre dans le calendrier.
    Possibilité de les modifier, supprimer ou en ajouter un suplémentaire. 
    Lien pour la création du calendrier "Créer un calendrier"
    -->

<div class="row mx-0 mb-4">
    <div class="col-6 text-left">
        <a href="index.php?page=1&Ajout" class="btn btn-outline-primary mx-2 align-self-start"><i class="fas fa-plus-circle"></i> Ajouter un ouvrier</a>
    </div>
    <div class="col-6 text-right">
        <a href="index.php?page=1&Vacances" class="btn btn-secondary mx-2">Créer un calendrier</a>
    </div>
</div>

<?php
    $query = "  SELECT   Identifiant, Prenom
                FROM     BDD_ouvriers
                ORDER BY Prenom";

    $req = $bdd->prepare($query);
    $req->execute() or die(print_r($bdd->errorInfo()));

    $nom         = [];
    $identifiant = [];
    $prenom      = [];

    while($donnees = $req->fetch()){
        array_push($identifiant, $donnees['Identifiant']);
        array_push($prenom,      $donnees['Prenom']);
    }
    
    echo '<div class="table-responsive">
            <table class="table table-striped text-center">
            <thead>
                <tr>
                    <th scope="col">Prénom</th>
                    <th scope="col">Modifier</th>
                    <th scope="col">Supprimer</th>
                </tr>
            </thead>
            <tbody>';

    $nb_sous_page = count($identifiant);

    if($nb_sous_page < 1){
        echo '<tr>
                <td colspan="5">Aucun ouvrier n\'a été inséré ici.  </br><a class="btn btn-secondary" href="index.php?page=1&Ajout">Ajouter un ouvrier ?</a></td>
                </tr>'; 
    }else{

        for ($i=0; $i < $nb_sous_page; $i++) {
            echo '<tr scope="row">';    

            //ouvrier
            echo '  <td class="pt-4"><a class="text-dark" href="index.php?page=1&Modification='.$identifiant[$i].'">'.$prenom[$i].'</a></td>';

            //Modification
            echo   '<td>
                        <a href="index.php?page=1&Modification='.$identifiant[$i].'">
                            <img style="width: 40px; height:40px;" src="./Images/parametres.png" alt="Modifier" title="Modifier"/>
                        </a>
                    </td>';

            //Suppression
            echo   '<td>
                        <a type="button" data-toggle="modal" data-target="#suppr'.$identifiant[$i].'"> 
                            <img style="width: 40px; height:40px;" src="./Images/supprimer.png" alt="Supprimer" title="Supprimer" />
                        </a>
                        <!-- Modal de confirmation pour la suppression -->
                        <div class="modal fade" id="suppr'.$identifiant[$i].'" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title text-white text-uppercase">Confirmer la suppression</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mx-auto py-2">
                                            Souhaitez vous supprimer l\'ouvrier suivant :</br> 
                                            <span class="font-weight-bold">"'.$prenom[$i].'"</span> ?
                                        </p>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Annuler</button>
                                        <a type="button" href="index.php?page=1&Suppression='.$identifiant[$i].'" class="btn btn-danger">Supprimer</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>';
        }
    }
    echo '  </tbody>
        </table>
        </div>'; 
