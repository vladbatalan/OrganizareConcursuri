<?php
    include 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Concursuri</title>
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
                        <h1 class="mt-4">Concursuri</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Tabela Concursuri</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-body">
                                In aceasta pagina, puteti urmari toate concursurile care au fost organizate.
                            </div>
                        </div>
                        <div class="card mb-4">
                            
                            <div class="card-header">
                                <i class="fas fa-table mr-1"></i>
                                Tabela Concursuri
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Nume Concurs</th>
                                                <th>Editie Concurs</th>
                                                <th>Oras</th>
                                                <th>Numar Participanti</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Nume Concurs</th>
                                                <th>Editie Concurs</th>
                                                <th>Oras</th>
                                                <th>Numar Participanti</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>

                                            <?php
                                                $concursuri = $conn->select_concursuri();
                                                foreach($concursuri as $concurs){
                                                    // selectez numarul de participanti
                                                    $num_participanti = $conn->numar_participanti_concurs($concurs->ID_CONCURS);

                                                    // selectez data in care a avut loc concursul
                                                    //$data_concurs = $conn->data_concurs($concurs->ID_CONCURS)[0]->DATA_CONCURS;
                                                    echo "

                                                    <tr>
                                                        <td><a href='detalii_concurs.php?concursId=".$concurs->ID_CONCURS."'>" . $concurs->NUME_CONCURS . "</a></td>
                                                        <td>" . $concurs->EDITIE_CONCURS . "</td>
                                                        <td>" . $concurs->ORAS . "</td>
                                                        <td>" . $num_participanti . " <a href='participanti_concurs.php?concursId=".$concurs->ID_CONCURS."'>(Vezi  Participanti)</a></td>
                                                    </tr>
                                                    ";
                                                }
                                            ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/datatables-demo.js"></script>
    </body>
</html>
