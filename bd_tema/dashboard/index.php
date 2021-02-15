<?php
include 'db_connection.php';

// statistici pentru caseta de informatii generale
$all_concursuri = $conn->select_concursuri();
$numar_evenimente = count($conn->select_evenimente());
$numar_premii_castigate = count($conn->select_participanti_concurs_premiu(-1, false));
$numar_total_participanti = $conn->numar_total_participanti();

// pentru fiecare concurs, voi afla costurile aferente
$cost_premii = array();
$cost_evenimente = array();
$sponsorizari = array();
$profit = array();


// calcul sume pentru fiecare concurs
foreach ($all_concursuri as $concurs) {
    $cost_premii[$concurs->ID_CONCURS] = $conn->cost_premii_oferite($concurs->ID_CONCURS);
    $cost_evenimente[$concurs->ID_CONCURS] = $conn->cost_evenimente($concurs->ID_CONCURS);
    $sponsorizari[$concurs->ID_CONCURS] = $conn->suma_sponsorizari($concurs->ID_CONCURS);
    $profit[$concurs->ID_CONCURS] = $sponsorizari[$concurs->ID_CONCURS] - ($cost_premii[$concurs->ID_CONCURS] + $cost_evenimente[$concurs->ID_CONCURS]);
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
    <title> Organizatori Concursuri </title>
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

        <!-- Main Content -->
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-body">
                            <p class="mb-2">
                                Bine ati venit pe pagina de administrator a site-ului <b><i>organizatori.concursuri.ac.tuiasi</i></b>!<br>
                                Acest site a fost realizat de studentul <b>Batalan Vlad</b> din grupa <b>1308A</b> a facultatii de <b><i>"Automatica si Calculatoare"</i></b> din cadrul Universitatii Tehnice <b><i>"Gheorghe Asachi"</i></b>, Iasi.
                            </p>
                            <p class="mb-4">
                                Site-ul are in vedere usurarea organizarii a mai multor concursuri de matematica.<br>
                                De la deciderea costurilor evenimentelor din cadrul unui concurs, pana la posibilitatea de a atribui castigatorilor premiile corespunzatoare.
                            </p>
                            <p class="mb-2">
                                Concursuri organziate: <b><?php echo count($all_concursuri); ?></b><br>
                                Numar participanti: <b><?php echo $numar_total_participanti; ?></b><br>
                                Premii castigate: <b><?php echo $numar_premii_castigate; ?></b><br>
                                Evenimente existente: <b><?php echo $numar_evenimente; ?></b>
                            </p>
                            <p class="mb-0">
                                <a class="btn btn-primary" href="concursuri_table.php">Vezi toate concursurile</a>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        foreach ($all_concursuri as $concurs) {
                            $bg_card = "bg-success";
                            if($profit[$concurs->ID_CONCURS] < 0)
                                $bg_card = "bg-danger";
                            ?>
                            <div class="col-xl-3 col-md-6">
                                <div class="card <?php echo $bg_card; ?> text-white mb-4">
                                    <div class="card-header"><?php echo "\"". $concurs->NUME_CONCURS ."\" - " . $concurs->EDITIE_CONCURS;  ?></div>
                                    <div class="card-body">
                                        <p class="lead">Profit total concurs:   <?php echo $profit[$concurs->ID_CONCURS]; ?></p>

                                        <a class="big text-white" data-toggle="collapse" data-target="#moreData<?php echo $concurs->ID_CONCURS; ?>" aria-expanded="false" aria-controls="moreData<?php echo $concurs->ID_CONCURS; ?>">
                                            <i class="fas fa-angle-right" ></i>
                                            Mai multe informatii
                                        </a>
                                        <div class="collapse" id="moreData<?php echo $concurs->ID_CONCURS; ?>">
                                            Cost Premii:     <?php echo $cost_premii[$concurs->ID_CONCURS]; ?><br>
                                            Cost Evenimente: <?php echo $cost_evenimente[$concurs->ID_CONCURS]; ?><br>
                                            Sponsorizari:    <?php echo $sponsorizari[$concurs->ID_CONCURS]; ?>
                                        </div>

                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="big text-white" href="detalii_concurs.php?concursId=<?php echo $concurs->ID_CONCURS; ?>">Detalii Concurs</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }

                        ?>
                    </div>
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
</body>
</html>
