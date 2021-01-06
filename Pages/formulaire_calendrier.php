<!-- Ici se situe le formulaire permettant de terminer le calendrier et les formulaires d'ajout/modification d'ouvrier.
    Les dernières informations à rentrées pour finaliser le calendrier sont celles des périodes de vacances, 
    il peut y en avoir plusieurs comme pas du tout. (Dans ce cas cliquer sur le bouton "Pas de vacances")
    -->

<div class="text-left m-4">
    <a href="index.php?page=1" class="btn btn-outline-primary"><i class="far fa-arrow-alt-circle-left"></i> Retour au tableau des ouvriers</a>
</div>

<?php
    if(isset($_GET['Vacances'])){
?>
        <section class="container">
                <form method='POST' action="./Pages/creation_calendrier.php" class="px-3 my-4 mx-auto text-secondary w-100">

                    <h4 class="text-dark mb-3">Dates des vacances :</h4>

                    <div id="form_vacances">
                        <div class="d-flex align-items-center justify-content-center flex-wrap my-2" id="inputs_vacances">
                            <div class="m-2">
                                <label for="debut_vacances">Début des vacances :</label><br/>
                                <input type="date" name="debut_vacances[]" required class="form-control"
                                    min="<?= date("Y")."-".date("m")."-".date("d");?>" max="<?= (date("Y")+1)."-".(date("m")+1)."-".date("d");?>">
                                    <!-- yyyy-mm-dd -->
                            </div>
                            <div class="m-2">
                                <label for="fin_vacances">Fin des vacances :</label><br/>
                                <input type="date" name="fin_vacances[]" required class="form-control"
                                    min="<?= date("Y")."-".date("m")."-".date("d");?>" max="<?= (date("Y")+1)."-".(date("m")+1)."-".date("d");?>">
                            </div>
                        </div>  
                    </div>
                    <div class="text-center" id="ajout_vacances">
                        <a href="#">+ Ajouter des vacances</a>  
                    </div>
                    
                    <div class="d-flex justify-content-center align-self-center">
                        <a href="./Pages/creation_calendrier.php" class="btn btn-secondary m-3">Pas de vacances</a>  
                        <button type="submit" class="btn btn-primary m-3">Valider</button>  
                    </div>
                </form>
        </section>
<?php
    }else{
        //--- Préparation MODIFICATION nom d'ouvrier ---//
        if(isset($_GET['Modification'])){
            $identifiant = htmlspecialchars(trim($_GET['Modification']));

            $query = "  SELECT  Prenom
                        FROM    BDD_ouvriers
                        WHERE   Identifiant = :identifiant";
            
            $req = $bdd->prepare($query);
            $req->bindValue("identifiant", $identifiant, PDO::PARAM_INT);
            $req->execute() or die(print_r($bdd->errorInfo()));
            $donnees = $req->fetch();

            $prenom_ouvrier = $donnees['Prenom'];
        }
?>
        <!-- //--- Formulaire de MODIFICATION ou AJOUT d'un nom d'ouvrier ---// -->
        <section class="container">
                <form method='POST' action="index.php?page=1" class="px-3 my-4 mx-auto text-secondary w-100">

                    <h4 class="text-dark mb-3"><u>
                        <?php if(isset($_GET['Modification'])){ echo "Modification"; }else{ echo "Ajout"; } ?>
                    d'un ouvrier :</u></h4>

                    <div class="form-group">
                        <label for="prenom_ouvrier">Prénom de l'ouvrier :</label><br/>
                        <input type="text" name="prenom_ouvrier" required class="form-control"   value="<?php 
                                                                                                            if(isset($_GET['Modification'])){
                                                                                                                echo $prenom_ouvrier;
                                                                                                            }else{ 
                                                                                                                echo ""; }
                                                                                                        ?>">
                    </div>  
<?php
                
            if(isset($_GET['Modification'])){
                echo '<input type="hidden" name="modification" value="'.$identifiant.'">';
            }else{
                echo '<input type="hidden" name="ajout">';
            }

?>
                    <button type="submit" class="btn btn-primary d-block mx-auto my-2">Valider</button>  
                </form>
        </section>
<?php
    }
?>
<!-- Le script permet d'ajouter (ou supprimer) 2 inputs (début et fin de vacances) permettant d'insérer autant de vacances que souhaité. -->
<script>
    var boutonAjoutVacances = document.getElementById('ajout_vacances');

    boutonAjoutVacances.addEventListener("click", function(){

        var elementACloner  = document.getElementById('inputs_vacances'),
            divPourInserer  = document.getElementById('form_vacances'),
            elementCloner   = elementACloner.cloneNode(true),
            divSupprimer    = document.createElement("div"),
            lienSupprimer   = document.createElement("a");

        elementCloner.className += " border-top border-primary";

        divSupprimer.className  = "d-flex justify-content-center align-items-center mx-2 mt-1";

        lienSupprimer.className = "text-decoration-none text-primary";
        lienSupprimer.href      = "#";
        lienSupprimer.innerHTML = "- Supprimer";

        lienSupprimer.addEventListener("click", function(){
            var divSupprimer   = this.parentNode,
                inputsVacances = divSupprimer.parentNode;

            inputsVacances.parentNode.removeChild(inputsVacances);
        });

        divSupprimer.appendChild(lienSupprimer);
        elementCloner.appendChild(divSupprimer);
        divPourInserer.appendChild(elementCloner);
    });
</script>