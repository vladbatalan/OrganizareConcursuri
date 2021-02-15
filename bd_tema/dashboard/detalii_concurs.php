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

// PENTRU STRIP CHART

$max_sponsor = -999999;
$labels = "";
$values = "";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Detalii Concurs</title>
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
                        echo "Detalii Concurs";
                    else
                        echo "Concursul <a href='detalii_concurs.php?concursId=".$concurs_ales->ID_CONCURS."'>\"".$concurs_ales->NUME_CONCURS."\" - ".$concurs_ales->EDITIE_CONCURS . "</a>";
                    ?>
                </h1>
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Detalii Concurs</li>
                </ol>
                <div class="card mb-0">
                    <div class="card-body">
                        <p class="mb-0">
                            Pe aceasta pagina puteti vizualiza detalii despre concursurile organizate.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <form action="detalii_concurs.php" method="get">
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

                        // mumarul de participanti la concurs
                        $nr_participanti = $conn->numar_participanti_concurs($concurs_ales->ID_CONCURS);

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



                        <div class="card text-black mb-4">
                            <div class='card-body'>
                                <p> Orasul in care se desfasoara: <b><?php echo $concurs_ales->ORAS; ?></b> </p>
                                <p> Numar de participanti: <b><?php echo $nr_participanti; ?></b></p>
                                <p><a href="participanti_concurs.php?concursId=<?php echo $concurs_ales->ID_CONCURS; ?>" class='btn btn-primary'>Vezi participanti</a></p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-bar mr-1"></i>
                                    Sponsorizari Concurs
                                </div>
                                <a id="sponsor-anchor">
                                    <div class="card-body"><canvas id="sponsor-chart" width="100%" height="50"></canvas></div>
                                </a>
                                <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
                            </div>
                        </div>




                        <?php
                            // voi obtine informatii pentru STRIPCHART sponsirizari
                            $sponsorizari = $conn->select_sponsorizari($concurs_ales->ID_CONCURS);
                            $max_sponsor = -999999;
                            $labels = "";
                            $values = "";
                            $index = 0;
                            foreach($sponsorizari as $sponsorizare){
                                $labels .= "\"".$sponsorizare->NUME_SPONSORIZARE."\"";
                                $values .= $sponsorizare->SUMA_SPONSORIZATA;

                                if($max_sponsor < $sponsorizare->SUMA_SPONSORIZATA)
                                    $max_sponsor = $sponsorizare->SUMA_SPONSORIZATA;

                                if($index != count($sponsorizari) - 1)
                                {
                                  $labels .= ", ";
                                  $values .= ", ";
                                }
                                $index ++;
                            }

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

    <!-- SCRIPT CHART SPONSORIZARI -->
    <script>


        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#292b2c';

        var ctx = document.getElementById("sponsor-chart");
        var myLineChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: [<?php echo $labels; ?>],
            datasets: [{
              label: "Suma sponsorizata",
              backgroundColor: "rgba(2,117,216,1)",
              borderColor: "rgba(2,117,216,1)",
              data: [<?php echo $values; ?>],
          }],
      },
      options: {
        scales: {
          xAxes: [{
            time: {
              unit: 'month'
          },
          gridLines: {
              display: false
          },
          ticks: {
              maxTicksLimit: 6
          }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          max: <?php echo $max_sponsor; ?>,
          maxTicksLimit: 20
      },
      gridLines: {
          display: true
      }
  }],
},
legend: {
  display: false
}
}
});
</script>
</body>
</html>
