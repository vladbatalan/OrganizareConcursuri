
<?php
include 'db_connection.php';

$specified_id_flag = false;
$concurs_ales = null;
if(isset($_GET['concursId'])){
        // GET THE ID_CONCURS VALUE
    $concurs_id = htmlspecialchars($_GET['concursId']);

        // SELECT CONCURS FROM DB WINTH THE ID
    $concurs_ales = $conn->select_concursuri(["id_concurs" => $concurs_id]);
    if(count($concurs_ales) == 1){
        $specified_id_flag = true;
        $concurs_ales = $concurs_ales[0];
    }
}

if($specified_id_flag == true)
{
    // modificare asociere premiii - participanti prin apasarea SaveChanges din tabelul principal
    if(isset($_POST['participanti_change'])){
        // daca a fost trimis si id ul concursului
        if(isset($_POST['hidden_concurs_id'])){
            $id_concurs = $_POST['hidden_concurs_id'];
            if(is_numeric($id_concurs)){

                // selectez toti participantii care participa la concurs
                $participanti = $conn->select_participanti_concurs_premiu($concurs_ales->ID_CONCURS);

                // array pentru a sublinia datele scimbate si erori
                $deleted_relations = array();
                $updated_relations = array();

                // pentru fiecare participant, verific select-ul
                foreach ($participanti as $part) {
                    $string = "premiu_select".$part->ID_PARTICIPANT;
                    if(isset($_POST[$string])){

                        // este premiul selectat in care sa fie schimbat
                        $id_premiu_selectat = $_POST[$string];
                        if(is_numeric($id_premiu_selectat)){

                            // caut in baza de date asocierea participant-premiu-concurs
                            // premiul asociat participantului pentru concursul respectiv
                            $assoc = $conn->select_participanti_concurs_premiu($id_concurs, true, $part->ID_PARTICIPANT);
                            $premiu_participant = -1;
                            if(count($assoc) == 1 and !empty($assoc[0]->ID_PREMIU))
                                $premiu_participant = $assoc[0]->ID_PREMIU;

                            // daca se doreste stergerea unui premiu
                            if($id_premiu_selectat == -1){
                                // daca participantul are un premiu asociat, il vom sterge
                                if($premiu_participant != -1){
                                    // sterge din baza de date relatia pa baza:
                                    // concursului si a id-ului concurentului
                                    $r = $conn->delete_relatie_participant_premiu($id_concurs, $part->ID_PARTICIPANT);
                                    if(!$r){
                                        $delete_error = true;
                                    }
                                    else{
                                        $deleted_relations[$part->ID_PARTICIPANT] = true;
                                    }
                                }

                            }
                            // se doreste schimbarea premiului in altul
                            else{
                                // daca premiul pentru student nu exista, se face insert
                                if($premiu_participant == -1){
                                    $r = $conn->insert_asociere_participant_premiu($id_concurs, $part->ID_PARTICIPANT, $id_premiu_selectat);
                                    if($r){
                                        $updated_relations[$part->ID_PARTICIPANT] = true;
                                    }

                                }
                                // daca premiul exista, se face update
                                else if($premiu_participant != $id_premiu_selectat){
                                    $r = $conn->update_relatie_premiu_participant($id_premiu_selectat, $part->ID_PARTICIPANT, $id_concurs);
                                    if($r){
                                        $updated_relations[$part->ID_PARTICIPANT] = true;
                                    }
                                }
                            }



                        }


                    }

                }



            }

        }
    }



    // apasare Save Changes pentru a modifica costurile premiilor
    if(isset($_POST['premii_change_submit']))
    {
        $concurs_id = -1;
        if(isset($_POST['concurs_id']) and is_numeric($concurs_id)){
            $concurs_id = $_POST['concurs_id'];
        }

        // vom selecta fiecare index al premiile existente
        $all_prizes = $conn->select_optiuni_premii(['id_concurs' => $concurs_ales->ID_CONCURS]);

        // array-ul asta memoreaza toate premiile care s au schimbat
        $changed_premiu = array();

        foreach($all_prizes as $premiu){

            // verificam daca a fost modificat costul
            if(isset($_POST['cost_premiu'.$premiu->ID_PREMIU])){
                $cost_nou = $_POST['cost_premiu'.$premiu->ID_PREMIU];

                // vom executa update doar daca valoarea este numerica
                if(is_numeric($cost_nou)){
                    $changed_premiu[$premiu->ID_PREMIU] = true;

                    $conn->update_cost_premiu($premiu->ID_PREMIU, $cost_nou);
                }
            }
        }

        // stergere premii selectate
        if(isset($_POST['delete_premiu'])){
            $deleted_prizes = array();
            $error_delete = array();
            foreach($_POST['delete_premiu'] as $premiu_id){
                $rezultat = $conn->delete_premiu_transaction($premiu_id, $concurs_id);
                if(strpos($rezultat, "Eroare") !== false)
                    $error_delete[] = $rezultat;
                else
                    $deleted_prizes[] = $rezultat;
            }
        }

        // vom verifica daca a fost adaugat un nou tip de premiu
        if(isset($_POST['nume_premiu_nou']) and isset($_POST['cost_premiu_nou']) and isset($_POST['concurs_id']))
        {
            $nume_premiu_nou = htmlspecialchars($_POST['nume_premiu_nou']);
            $cost_premiu_nou = htmlspecialchars($_POST['cost_premiu_nou']);

            if(!empty($nume_premiu_nou)){
                // vom introduce premiul in baza de date
                $insert_complete = $conn->insert_premiu($nume_premiu_nou, $cost_premiu_nou, $concurs_id);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Participanti Concurs</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">

    <!-- Top Navbar -->
    <?php 
    include "nav_top.php";
    ?>

    <div id="layoutSidenav">

     <!-- Side Navbar -->
     <?php 
     include "nav_side.php";
     ?>

     <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h1 class="mt-2">
                    <?php
                        // in functie daca este un concurs ales, voi afisa titlu corespunzator
                    if($specified_id_flag == false)
                        echo "Participanti Concurs";
                    else
                        echo "Participantii concursului <a href='detalii_concurs.php?concursId=".$concurs_ales->ID_CONCURS."'>\"".$concurs_ales->NUME_CONCURS."\" - ".$concurs_ales->EDITIE_CONCURS."</a>";
                    ?>
                </h1>
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Participanti Concurs</li>
                </ol>
                <div class="card mb-0">
                    <div class="card-body">
                        <p class="mb-0">
                            Toti participantii din cadrul unui concurs vor fi prezentati pe aceasta pagina, impreuna cu premiile detinute de catre acestia. <br>
                            Aici puteti sa atribuiti premii diferitilor participanti de la concurs.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-xl-4 col-md-2">
                                <div class="form-group">
                                    <label class="small mb-0" for="concursId">Nume Concurs</label>
                                    <select class="form-control py-8" id="concursId" name="concursId">
                                        <?php
                                            // voi selecta toate concursurile
                                        $concursuri = $conn->select_concursuri();
                                        foreach($concursuri as $concurs){
                                                // daca id-ul concursului ales e acelasi cu cel din optiune
                                            $selected = "";
                                            if($specified_id_flag == true and 
                                                $concurs_ales->ID_CONCURS == $concurs->ID_CONCURS)
                                                $selected = "selected";

                                                echo 
                                                "<option value='" . $concurs->ID_CONCURS ."' $selected>"
                                                . $concurs->NUME_CONCURS." - ".$concurs->EDITIE_CONCURS .
                                                "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-md-2">
                                    <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                        <input class="btn btn-primary" type="submit" value="Cauta concurs"></input>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>


                    <?php 
                    if($specified_id_flag == true) { 
                        // voi extrage costurile de realizare ale concursului si le voi afisa 
                        $cost_premii = $conn->cost_premii_oferite($concurs_ales->ID_CONCURS);
                        $cost_evenimente = $conn->cost_evenimente($concurs_ales->ID_CONCURS);
                        $suma_sponsorizari = $conn->suma_sponsorizari($concurs_ales->ID_CONCURS);

                        // calcul procent din suma totala
                        $procent_premii = round($cost_premii/$suma_sponsorizari * 100, 2);
                        $procent_evenimente = round($cost_evenimente/$suma_sponsorizari * 100, 2);

                        // in functie de procent, cardul va fi normal sau warning
                        $card_type_premii = "bg-primary";
                        $card_type_evenimente = "bg-primary";
                        if($procent_premii >= 100)
                            $card_type_premii = "bg-warning";
                        if($procent_evenimente >= 100)
                            $card_type_evenimente = "bg-warning";

                        // ultimul mesaj va fi unul care va spune profitul, in functie de acesta, va fi de tip danger sau success
                        $profit = $suma_sponsorizari - ($cost_premii + $cost_evenimente);
                        $cart_type_profit = "bg-success";

                        if($profit < 0)
                            $cart_type_profit = "bg-danger";

                        // adaug % la procente
                        $procent_premii .= " %";
                        $procent_evenimente .= " %";
                        ?>




                        <!-- Afisare eroare, respectiv succes -->
                        <?php
                            // daca sunt chestii efectuate cu succes sau eroare, se va realiza inca
                            // un card
                        $success_message = "";
                        $error_message = "";

                        if(isset($deleted_relations) and count($deleted_relations) > 0){
                            $success_message .= "<li> Au fost sterse ".count($deleted_relations)." relatii participant-premiu!</li>";
                        }
                        if(isset($updated_relations) and count($updated_relations) > 0){
                            $success_message .= "<li> Au fost modificate ".count($updated_relations)." relatii participant-premiu!</li>";
                        }

                        if(isset($insert_complete)){
                            if(!$insert_complete){
                                $error_message .= "<li> Eroare: adaugare de premiu nou esuata!</li>";
                            }
                            else{
                                $success_message .= "<li> Premiul nou a fost adaugat cu succes!</li>";
                            }
                        }
                        if(isset($changed_premiu)){
                            if(count($changed_premiu) > 0)
                                $success_message .= "<li>Costuri premii schimbate cu succes!</li>";
                        }
                        if(isset($deleted_prizes)){
                            if(count($deleted_prizes) > 0){
                                if(count($deleted_prizes) == 1)
                                    $success_message .= "<li>A fost sters un premiu cu succes!</li>";
                                else
                                    $success_message .= "<li>A fost sterse ".count($deleted_prizes)." premii cu succes!</li>";
                            }
                        }
                        if(isset($error_delete) and count($error_delete) > 0){
                            $error_message .= "<li> Eroare: ".count($error_delete)." premii care nu au putut fi sterse!</li>";
                        }
                        if(isset($delete_error) and count($delete_error) > 0){
                            $error_message .= "<li> Eroare: ".count($delete_error)." asocieri de premii care nu au putut fi sterse!</li>";
                        }


                        if(!empty($success_message)){
                            $success_message = "<ul>" . $success_message . "</ul>";
                            ?>
                            <div class="card bg-success text-white mb-4">
                                <div class="card-header">
                                    Operatii reusite
                                </div>
                                <div class="card-body">
                                    <?php echo $success_message; ?>
                                </div>
                            </div>
                            <?php
                        }

                        if(!empty($error_message)){
                            $error_message = "<ul>" . $error_message . "</ul>";
                            ?>
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-header">
                                    Erori
                                </div>
                                <div class="card-body">
                                    <?php echo $error_message; ?>
                                </div>
                            </div>
                            <?php
                        }

                        ?>


                        <div class="row">

                            <div class="col-xl-3 col-md-6">
                                <div class="card <?php echo $card_type_premii; ?> text-white mb-4">
                                    <div class="card-body">
                                        Cost Premii
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="large text-white stretched-link" href="premii_concurs.php?concursId=<?php echo $concurs_ales->ID_CONCURS; ?>"><?php echo $cost_premii." ($procent_premii)"; ?></a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">
                                        Suma Totala Sponsorizari
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="large text-white stretched-link" href="detalii_concurs.php?concursId=<?php echo $concurs_ales->ID_CONCURS; ?>#sponsor-anchor"><?php echo $suma_sponsorizari; ?> (100.00 %)</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card <?php echo $cart_type_profit; ?> text-white mb-4">
                                    <div class="card-body">Total Profit</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="large text-white stretched-link" href=""><?php echo $profit; ?></a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div> 


                        <div class="card mb-4"><!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tabela_premii_modal">
                                Modifica premii disponibile
                            </button>
                        </div>


                        <!-- acest div este vizibil doar cand exista un concurs selectat -->
                        <div class="card-body">

                            <!-- Tabela evenimente -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-table mr-1"></i>
                                    <?php
                                    echo "Participantii concursului <a href='detalii_concurs.php?concursId=".$concurs_ales->ID_CONCURS."'>\"".$concurs_ales->NUME_CONCURS."\" - ".$concurs_ales->EDITIE_CONCURS."</a>";
                                    ?>
                                </div>
                                <div class="card-body">

                                    <form method='post' action='participanti_concurs.php?concursId=<?php echo $concurs_ales->ID_CONCURS; ?>'>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th>Premiu</th>
                                                        <th>Nume Participant</th>
                                                        <th>Nume Institutie</th>
                                                        <th>Profesor Insotitor</th>
                                                        <th>Cost Premiu</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>Premiu</th>
                                                        <th>Nume Participant</th>
                                                        <th>Nume Institutie</th>
                                                        <th>Profesor Insotitor</th>
                                                        <th>Cost Premiu</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    $participanti = $conn->select_participanti_concurs_premiu($concurs_ales->ID_CONCURS);
                                                    $premii = $conn->select_optiuni_premii(['id_concurs' => $concurs_ales->ID_CONCURS]);
                                                    foreach($participanti as $part){

                                                        $tr_class = "";
                                                        if(isset($updated_relations[$part->ID_PARTICIPANT]))
                                                            $tr_class = "text-success";
                                                        if(isset($deleted_relations[$part->ID_PARTICIPANT]))
                                                            $tr_class = "text-danger";

                                                        echo "

                                                        <tr class='$tr_class'>
                                                        <td>
                                                        <select class='form-control py-8' name='premiu_select".$part->ID_PARTICIPANT."'>";


                                                        // pentru fiecare select, voi pune ca optiune premiile
                                                        $selected = "selected";
                                                        if(!empty($part->ID_PREMIU)){
                                                            $selected = "";
                                                        }

                                                        echo "<option $selected value='-1'>...</option>";
                                                        foreach ($premii as $premiu) {
                                                            $selected = "";
                                                            if($part->ID_PREMIU == $premiu->ID_PREMIU){
                                                                $selected = "selected";
                                                            }
                                                            echo "<option $selected value='".$premiu->ID_PREMIU."'>".$premiu->NUME_PREMIU."</option>";
                                                        }



                                                        echo"
                                                        </select>
                                                        </td>
                                                        <td>" . $part->NUME_PARTICIPANT . "</td>
                                                        <td>" . $part->NUME_INSTITUTIE . "</td>
                                                        <td>" . $part->NUME_PROFESOR . "</td>
                                                        <td>" . $part->COST_PREMIU . "</td>
                                                        </tr>
                                                        ";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <input type='hidden' name="hidden_concurs_id" value="<?php echo $concurs_ales->ID_CONCURS; ?>">
                                        <input type='submit' class='btn btn-primary' value='Save Changes' name='participanti_change'>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="tabela_premii_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            Premii disponibile
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="card font-italic text-danger mb-2">
                                            <div class="card-header"> Atentie! </div>
                                            <div class="card-body">
                                                Stergerea unui tip de premiu va avea ca efect inlocuirea acestuia petru toti participanti care l-au castigat cu un premiu <b>null</b>
                                            </div>
                                        </div>
                                        <!-- Tabela premii disponibile concurs -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <i class="fas fa-table mr-1"></i>
                                                Premii disponibile
                                            </div>
                                            <div class="card-body">
                                                <form method="post" action='participanti_concurs.php?concursId=<?php echo $concurs_ales->ID_CONCURS; ?>'>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Premiu</th>
                                                                    <th>Cost Premiu</th>
                                                                    <th>Sterge</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <?php

                                                                $premii = $conn->select_optiuni_premii(['id_concurs' => $concurs_ales->ID_CONCURS]);
                                                                foreach($premii as $premiu){


                                                                    echo "

                                                                    <tr>
                                                                    <td>" . $premiu->NUME_PREMIU . "</td>
                                                                    <td>
                                                                    <input class='form-control py-2' type='number' step='0.01' min='0' placeholder='" . $premiu->COST_PREMIU . "' /
                                                                    name='cost_premiu".$premiu->ID_PREMIU."'>
                                                                    </td>
                                                                    <td>

                                                                    <div class='custom-control custom-checkbox checkbox-xl'>
                                                                    <input class='checkbox-lg' type='checkbox' name='delete_premiu[]' value='".$premiu->ID_PREMIU."'></input>
                                                                    </div
                                                                    </td>
                                                                    </tr>
                                                                    ";
                                                                }
                                                                ?>
                                                            </tbody>

                                                        </table>
                                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Adauga Premiu Nou</th>
                                                                    <th>Cost Premiu</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td> 
                                                                        <input class='form-control py-2' type='text' placeholder='Nume Premiu Nou' name='nume_premiu_nou'> 
                                                                    </td>
                                                                    <td>
                                                                        <input class='form-control py-2' min = '0' type='number' step='0.01' placeholder='Cost Premiu' name='cost_premiu_nou'>
                                                                        <input type='hidden' name='concurs_id' value='<?php echo $concurs_ales->ID_CONCURS ?>'>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="float-right">
                                                        <input type='submit' value='Save changes' class='btn btn-primary' name='premii_change_submit'>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                            Close
                                                        </button>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <?php 
                    }
                    ?>


                </div>
            </main>

            <!-- Footer -->
            <?php
            include 'footer.php';
            ?>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/datatables-demo.js"></script>
</body>
</html>
