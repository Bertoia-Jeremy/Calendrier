<?php
    /* Cette page sert à préparer le calendrier avec les données recues telles que les vacances (POST) et les noms des ouvriers (BDD) pour ensuite
        faire un calendrier PDF grâce à la libraire Html2Pdf 
    */

    //---------------------------Préparation des données
    $jours_a_ecrire = array("D. ","L. ","Ma.","Me.","J. ","V. ","S. ");
	$mois_a_ecrire  = array("Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");

    $mois_actuel    = date("n"); //Mois sans les zéros initiaux
    $jour_a_verifier = mktime(0, 0, 0, date("n"), 1, date("Y"));//Le premier jour du mois actuel

    $mois_en_cours_de_verification = date("n");

    include ('../Pages/connect.php');
    $query = "SELECT Prenom FROM BDD_ouvriers";
    $req = $bdd->prepare($query);
    $req->execute() or die(print_r($bdd->errorInfo()));

    $tb_ouvriers = [];

    while($donnees = $req->fetch()){
        array_push($tb_ouvriers, $donnees['Prenom']);
    }
    $req = NULL;
    $tb_ouvriers_a_vider = $tb_ouvriers;

    //---- PRÉPARATION des vacances (s'il y en a) ----//
    if(isset($_POST['debut_vacances']) AND isset($_POST['fin_vacances'])){

        $debut_vacances = [];
        foreach($_POST['debut_vacances'] as $value){

            $date_debut = explode("-", htmlspecialchars(trim($value)));
            $timestamp_debut = mktime(0, 0, 0, $date_debut[1], $date_debut[2], $date_debut[0]);
            array_push($debut_vacances, $timestamp_debut);
        }

        $fin_vacances = [];
        foreach($_POST['fin_vacances'] as $value){

            $date_fin = explode("-", htmlspecialchars(trim($value)));
            $timestamp_fin = mktime(0, 0, 0, $date_fin[1], $date_fin[2], $date_fin[0]);
            array_push($fin_vacances, $timestamp_fin);
        }

    }
    
    //---- FONCTION Vérification des jours fériés ----//
    function jour_ferie($jour_a_verifier){
        
        $test_jour_ferie  = date("d", $jour_a_verifier);
        $test_mois_ferie  = date("m", $jour_a_verifier);
        $test_annee_ferie = date("Y", $jour_a_verifier);
        $EstFerie = 0;

        // Dates fériées fixes
        if($test_jour_ferie == 1  && $test_mois_ferie == 1){
            $EstFerie = '<span class="fetes">Jour de l\'an</span>'; 
            return $EstFerie;
        }// 1er janvier

        if($test_jour_ferie == 1  && $test_mois_ferie == 5){
            $EstFerie = '<span class="fetes">Fêtes du travail</span>';
            return $EstFerie;
        }// 1er mai

        if($test_jour_ferie == 8  && $test_mois_ferie == 5){
            $EstFerie = '<span class="fetes">Victoire 1945</span>';
            return $EstFerie;
        } // 8 mai

        if($test_jour_ferie == 14 && $test_mois_ferie == 7){
            $EstFerie = '<span class="fetes">Fête Nationale</span>';
            return $EstFerie;
        } // 14 juillet

        if($test_jour_ferie == 15 && $test_mois_ferie == 8){
            $EstFerie = '<span class="fetes">Assomption</span>';
            return $EstFerie;
        } // 15 aout

        if($test_jour_ferie == 1  && $test_mois_ferie == 11){
            $EstFerie = '<span class="fetes">Toussaint</span>';
            return $EstFerie;
        } // 1 novembre

        if($test_jour_ferie == 11 && $test_mois_ferie == 11){
            $EstFerie = '<span class="fetes">Armistice</span>';
            return $EstFerie;
        } // 11 novembre

        if($test_jour_ferie == 25 && $test_mois_ferie == 12){
            $EstFerie = '<span class="fetes">Noël</span>';
            return $EstFerie;
        } // 25 décembre



        // Fêtes religieuses mobiles
        // Pâques
        $paques     = easter_date($test_annee_ferie);
        $paques += 86400;
        $jour_fetes = date("d", $paques);
        $mois_fetes = date("m", $paques);

        if($jour_fetes == $test_jour_ferie && $mois_fetes == $test_mois_ferie){ 
            $EstFerie = '<span class="fetes">Pâques</span>';
            return $EstFerie;
        }

        // Lundi de Pâques
        $lundi_paques = mktime(date("H", $paques), date("i", $paques), date("s", $paques), date("m", $paques), date("d", $paques) +1, date("Y", $paques) );
        $jour_fetes = date("d", $lundi_paques);
        $mois_fetes = date("m", $lundi_paques);

        if($jour_fetes == $test_jour_ferie && $mois_fetes == $test_mois_ferie){ 
            $EstFerie = '<span class="fetes">Pâques</span>';
            return $EstFerie;
        }

        //Ascension
        $ascension = mktime(date("H", $paques), date("i", $paques), date("s", $paques), date("m", $paques), date("d", $paques) + 39, date("Y", $paques) );
        $jour_fetes = date("d", $ascension);
        $mois_fetes = date("m", $ascension);

        if($jour_fetes == $test_jour_ferie && $mois_fetes == $test_mois_ferie){ 
            $EstFerie = '<span class="fetes">Ascension</span>';
            return $EstFerie;
        }

        // Pentecôte
        $pentecote = mktime(date("H", $paques), date("i", $paques), date("s", $paques), date("m", $paques), date("d", $paques) + 49, date("Y", $paques) );
        $jour_fetes = date("d", $pentecote);
        $mois_fetes = date("m", $pentecote);

        if($jour_fetes == $test_jour_ferie && $mois_fetes == $test_mois_ferie){
            $EstFerie = '<span class="fetes">Pentecôte</span>';
            return $EstFerie;
        }

        return $EstFerie;
    }


    //---- PRÉPARATION de l'affichage ----//
    $array_annee = [];

    /* Cette boucle for a pour but de stocké tous les jours de l'année dans un array, on vérifie notamment :
        - Si on est dans la période des vacances de l'entreprise (ou autre) (si oui mettre en couleur et ne pas mettre de nom d'ouvrier)
        - Si c'est un jour férié (si oui mettre en couleur)
        - Si c'est le week end (si oui mettre en couleur) 
        - Si c'est un vendredi (si oui mettre le nom d'un ouvrier)
    */
    for ($k=0; $k < 12; $k++) { // On recommence pour les 12mois de l'année
        
        $td = date("n", $jour_a_verifier)-1;
        $array_mois  = [];
        
        while(date("n", $jour_a_verifier) == $mois_en_cours_de_verification){    

            for($i = 0; $i < 7; $i++){
                
                $tr = date("j", $jour_a_verifier)-1;               
                
                if(date("n", $jour_a_verifier) == $mois_en_cours_de_verification){ //Si on est toujours dans le bon mois
                    $ferie_ou_pas    = jour_ferie($jour_a_verifier);
                    $vacances_ou_pas = false;

                    if(isset($debut_vacances) AND isset($fin_vacances)){//Périodes de vacances récupérées dans les post (ou non)
                        $i = 0;

                        foreach($debut_vacances as $value){
                        
                            if($jour_a_verifier >= $value AND $jour_a_verifier <= $fin_vacances[$i]){
                                $vacances_ou_pas = true;
                                break;
                            }
                            $i++;
                        }
                    }

                    if($vacances_ou_pas){
                        
                        if($ferie_ou_pas){
                            $contenu_case = ".".$jours_a_ecrire[date("w", $jour_a_verifier)]." ".date("j", $jour_a_verifier)." ".$ferie_ou_pas;
                            //date('w')Jour de la semaine au format numérique	0 (pour dimanche) à 6 (pour samedi)

                        }else{
                            $contenu_case = ".".$jours_a_ecrire[date("w", $jour_a_verifier)]." ".date("j", $jour_a_verifier);
                        }
    
                    }else{

                        if($jours_a_ecrire[date("w", $jour_a_verifier)] == "V. "){//Ecrire celui qui fera le ménage
                            $tb_vide_ou_pas = count($tb_ouvriers_a_vider);

                            if($tb_vide_ou_pas){
                                $prenom = array_splice($tb_ouvriers_a_vider, 0,1);
                            }else{
                                $tb_ouvriers_a_vider = $tb_ouvriers;
                                $prenom = array_splice($tb_ouvriers_a_vider, 0,1);
                            }               

                            if($ferie_ou_pas){
                                $contenu_case = $jours_a_ecrire[date("w", $jour_a_verifier)]." ".date("j", $jour_a_verifier)." <span class='prenom'>".$prenom[0]."</span>".". ".$ferie_ou_pas;
                                //date('w')Jour de la semaine au format numérique	0 (pour dimanche) à 6 (pour samedi)
                            }else{
                                $contenu_case = $jours_a_ecrire[date("w", $jour_a_verifier)]." ".date("j", $jour_a_verifier)." <span class='prenom'>".$prenom[0]."</span>";
                            }

                        }elseif($ferie_ou_pas){
                            $contenu_case = $jours_a_ecrire[date("w", $jour_a_verifier)]." ".date("j", $jour_a_verifier).". ".$ferie_ou_pas;

                        }else{
                            $contenu_case = $jours_a_ecrire[date("w", $jour_a_verifier)]." ".date("j", $jour_a_verifier);
                        }
                    }
                    $array_mois[$tr]   = $contenu_case;//On accumule tous les jours du mois dans $array_mois
                    $jour_a_verifier += 86400;

                }

            }//Fin de la semaine

       }//Si la condition while ne tient plus, c'est que le mois a changé

        $array_annee[$td] = $array_mois;//Et on met le mois préparé dans l'année
        $mois_en_cours_de_verification = date("n", $jour_a_verifier);
    }
    
    ob_start();
?>

<style type="text/css">
    table{
        margin: auto;
        width: 100%;
    }

    thead{
        text-align: center;
        font-size: 20px;
    }

    table{
        border-collapse: collapse;
    }

    th{
        border: black solid 2px;
        padding: 4px 0px;
    }

    td{
        width: 8%;
        height: 21px;
        border: black solid 1px;
        font-size: 15px;
    }

    h1{
        color: #075582;
        text-align: center;
    }

    .week_end{
        background-color: #912610;
        color: white;
    }

    .prenom{
        font-weight: bold;
    }

    .ferie{
        background-color: #075582;
        color: white;
    }

    .fetes{
        font-size: 12px;
        color: white;
    }

    .vacances{
        background-color: gray;
    }

</style>

<page>
    <!-- Titre du tableau -->
    <h1>Entretien salle de pause</h1>

    <table>
        <thead>
            <tr><!-- Année + Année suivante en titre -->
                <th colspan="12"><?= date("Y")." / ".(date("Y")+1); ?></th>
            </tr>
            <tr><!-- En-tête des mois -->
                <?php
                    for($i=0, $j= date("n")-1; $i < 12; $i++){ //On commence par le mois en cours et on fait le reste ensuite.

                        echo "<th>".$mois_a_ecrire[$j]."</th>";
                        
                        if($j == 11){
                            $j = 0;
                        }else{
                            $j++;
                        }
                    }
                ?>
            </tr>
        </thead>
        <tbody><!-- Affichage de chacun des jours -->
            <?php
                for($i=0; $i < 31; $i++){

                    echo "<tr>";

                    //Affichage + Tri des jours normaux/fériés/week end/vacances et attribution des classes
                    for($j=0, $k= date("n")-1; $j < 12; $j++){
                        
                        if(array_key_exists($i, $array_annee[$k])){

                            $jour  = explode(".", $array_annee[$k][$i]);
                            $ferie = count($jour) ;
                            
                            if($jour[0] == ""){
                                echo '<td class="vacances">'.$array_annee[$k][$i]."</td>";
                            }elseif($jour[0] == "S" OR $jour[0] == "D"){
                                echo '<td class="week_end">'.$array_annee[$k][$i]."</td>";
                            }elseif($ferie >= 3){ 
                                echo '<td class="ferie">'.$array_annee[$k][$i]."</td>";
                            }else{
                                echo '<td>'.$array_annee[$k][$i].'</td>';
                            }

                        }else{
                            echo '<td></td>';
                        }

                        if($k == 11){
                            $k = 0;
                        }else{
                            $k++;
                        }
                    }

                    echo '</tr>';
                }
            ?>
        </tbody>
    </table>

</page>

<?php
$content = ob_get_clean();

require __DIR__.'/vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;

try {

    $pdf = new HTML2PDF('L', 'A3', 'fr');
    // $pdf = new HTML2PDF('P', 'A4', 'fr'); <= Pour une feuille en mode portrait et en A4

    $pdf->writeHTML($content);
    $pdf->Output('calendrier.pdf', 'D');
    //Forcer le téléchargement en mettant $pdf->Output('calendrier.pdf','D'); sinon juste écrire $pdf->Output('calendrier.pdf');

} catch (HTML2PDF_exception $e) {

    die($e);

}