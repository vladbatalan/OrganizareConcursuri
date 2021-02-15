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

$error_message = "";

// pentru modificarea costurilor evenementelor
if($specified_id_flag == true){
    // a fost apasat butonul de change cost eveniment
    if(isset($_POST['event_change_submit'])){
        $evenimente = $conn->select_evenimente(["id_concurs" => $concurs_ales->ID_CONCURS]);
        $changed_event = array();
        foreach($evenimente as $event){
            $string = "cost_eveniment" . $event->ID_EVENIMENT;

            if(isset($_POST[$string]) and !empty($_POST[$string])){
                $cost_nou = $_POST[$string];
                if(is_numeric($cost_nou)){
                    $r = $conn->update_cost_eveniment($event->ID_EVENIMENT, $cost_nou);
                    if(!$r){
                        // eroare
                        $error_message .= "<li>Eroare: Evenimentul \"".$event->NUME_EVENIMENT."\" nu a putut fi schimbat!</li>";
                    }
                    else{
                        $changed_event[$event->ID_EVENIMENT] = true;
                    }
                }
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
    <title>Evenimente Concurs</title>
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
                        echo "Evenimente Concurs";
                    else
                        echo "Evenimentele concursului <a href='detalii_concurs.php?concursId=".$concurs_ales->ID_CONCURS."'>\"".$concurs_ales->NUME_CONCURS."\" - ".$concurs_ales->EDITIE_CONCURS."</a>";
                    ?>
                </h1>
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Evenimente Concurs</li>
                </ol>
                <div class="card mb-0">
                    <div class="card-body">
                        <p class="mb-0">
                            Pagina cu evenimentele care alcatuiesc unui concurs.
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

                        <?php
                        // ca rezultat al unui update cu succes, va fi afisat un success card
                        if(isset($changed_event) and count($changed_event) > 0){ 
                            ?>
                            <div class="card bg-success text-white mb-4">
                                <div class="card-header">Succes!</div>
                                <div class="card-body">Update realizat cu succes!</div>
                            </div>

                            <?php
                        }

                        // daca apar probleme la update-uri
                        if(!empty($error_message)){ 
                            $error_message = "<ul>" .$error_message. "</ul>";
                            ?>
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-header">Eroare</div>
                                <div class="card-body"><?php echo $error_message; ?></div>
                            </div>
                            <?php
                        }
                        ?>

                        <div class="row">

                            <div class="col-xl-3 col-md-6">
                                <div class="card <?php echo $card_type_evenimente; ?> text-white mb-4">
                                    <div class="card-body">
                                        Cost Evenimente
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="large text-white stretched-link" href="evenimente_concurs.php?concursId=<?php echo $concurs_ales->ID_CONCURS; ?>"><?php echo $cost_evenimente." ($procent_evenimente)"; ?></a>
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



                        <!-- acest div este vizibil doar cand exista un concurs selectat -->
                        <div class="card-body">

                            <!-- Tabela evenimente -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-table mr-1"></i>
                                    <?php
                                        echo "Evenimentele concursului <a href='detalii_concurs.php?concursId=".$concurs_ales->ID_CONCURS."'>\"".$concurs_ales->NUME_CONCURS."\" - ".$concurs_ales->EDITIE_CONCURS."</a>";
                                    ?>
                                </div>
                                <div class="card-body">

                                    <form method='post' action='evenimente_concurs.php?concursId=<?php echo $concurs_ales->ID_CONCURS; ?>'>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Nume Eveniment</th>
                                                        <th>Interval Timp</th>
                                                        <th>Public Tinta</th>
                                                        <th>Cost Eveniment</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Nume Eveniment</th>
                                                        <th>Interval Timp</th>
                                                        <th>Public Tinta</th>
                                                        <th>Cost Eveniment</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>

                                                    <?php
                                                    $evenimente = $conn->select_evenimente(['id_concurs' => $concurs_ales->ID_CONCURS]);
                                                    foreach($evenimente as $event){
                                                        $tr_class = "";
                                                        if(isset($changed_event[$event->ID_EVENIMENT]))
                                                            $tr_class = "text-success";

                                                        echo "

                                                        <tr class='$tr_class'>
                                                        <td>" . $event->DATA_EVENIMENT . "</td>
                                                        <td>" . $event->NUME_EVENIMENT . "</td>
                                                        <td>" . $event->INTERVAL_TIMP . "</td>
                                                        <td>" . $event->PUBLIC_TINTA . "</td>
                                                        <td>
                                                        <input type='number' 
                                                        min='0' step='0.01'
                                                        name='cost_eveniment".$event->ID_EVENIMENT."' 
                                                        placeholder='" . $event->COST_EVENIMENT . "''
                                                        >
                                                        </td>
                                                        </tr>
                                                        ";
                                                    }
                                                    ?>

                                                </tbody>
                                            </table>
                                        </div>
                                        <input type='submit' class='btn btn-primary' value='Save Changes' name='event_change_submit'>
                                    </form>
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
