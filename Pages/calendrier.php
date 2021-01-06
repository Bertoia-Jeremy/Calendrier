<!-- Ici se situe la page centrale pour créer un calendrier ainsi que les requête SQL d'ajout, modification ou suppression d'ouvrier,
    ainsi que le message confirmant l'ajout, la modification ou la suppression d'un ouvrier. -->

<div class="text-center my-3">
    <h1 class="text-primary m-0 p-0">Calendrier</h1>
</div>

<?php

    if(isset($_GET['Ajout']) OR isset($_GET['Modification']) OR isset($_GET['Vacances'])){

        include ('formulaire_calendrier.php');

    }elseif(isset($_GET['Suppression'])){

        //---- SUPPRESSION d'un ouvrier ----//
        $suppression = htmlspecialchars(trim($_GET['Suppression']));

        $query = 'DELETE FROM BDD_ouvriers WHERE Identifiant = :identifiant';

        $req = $bdd->prepare($query);
        $req->bindValue("identifiant", $suppression, PDO::PARAM_INT);
        $req->execute() or die(print_r($bdd->errorInfo()));

        echo '<script>window.location = "index.php?page=1&ok3";</script>';

    }elseif(isset($_POST['ajout']) AND isset($_POST['prenom_ouvrier'])){
        
        //---- AJOUT d'un ouvrier ----//
        $prenom_ouvrier = htmlspecialchars(trim($_POST['prenom_ouvrier']));

        $query = "  INSERT INTO BDD_ouvriers(   Identifiant,
                                                        Prenom) 
                    VALUES (:identifiant,
                            :prenom)";

        $req = $bdd->prepare($query);
        $req->bindValue("identifiant", time(),          PDO::PARAM_INT);
        $req->bindValue("prenom",      $prenom_ouvrier, PDO::PARAM_STR);
        $req->execute() or die(print_r($bdd->errorInfo()));

        echo '<script>window.location = "index.php?page=1&ok2";</script>';

    }elseif(isset($_POST['modification']) AND isset($_POST['prenom_ouvrier'])){
        
        //---- MODIFICATION d'un ouvrier ----//
        $prenom_ouvrier = htmlspecialchars(trim($_POST['prenom_ouvrier']));
        $identifiant    = htmlspecialchars(trim($_POST['modification']));

        $query = "  UPDATE  BDD_ouvriers  
                    SET     Prenom      = :prenom 
                    WHERE   Identifiant = :identifiant";

        $req = $bdd->prepare($query);
        $req->bindValue("prenom",      $prenom_ouvrier, PDO::PARAM_STR);
        $req->bindValue("identifiant", $identifiant,    PDO::PARAM_INT);
        $req->execute() or die(print_r($bdd->errorInfo()));

        echo '<script>window.location = "index.php?page=1&ok";</script>';

    }else{

        if(isset($_GET['ok'])){
            echo '  <div class="alert alert-success alert-dismissible fade show text-center px-0 w-50 mx-auto my-4" role="alert">
                        Modification de l\'ouvrier, effectuée.
                        <button type="button" class="close" data-dismiss="alert" aria-label=« Close »>
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';

        }elseif(isset($_GET['ok2'])){
            echo '  <div class="alert alert-success alert-dismissible fade show text-center px-0 w-50 mx-auto my-4" role="alert">
                        Ajout d\'ouvrier, effectué.
                        <button type="button" class="close" data-dismiss="alert" aria-label=« Close »>
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }elseif(isset($_GET['ok3'])){
            echo '  <div class="alert alert-success alert-dismissible fade show text-center px-0 w-50 mx-auto my-4" role="alert">
                        Suppression d\'ouvrier, effectué.
                        <button type="button" class="close" data-dismiss="alert" aria-label=« Close »>
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }

        include ('tableau_calendrier.php');
    }  
