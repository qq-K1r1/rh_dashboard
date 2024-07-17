<?php
include('../db_config.php');
include('../return_to_login_page.php');

$authentification_id = $_SESSION['authentification_id'];
$role = $_SESSION['role'];
$user_name = $_SESSION['full_name']; // Fetching manager's full name from session

// Fetch total number of employees
$total_employees_sql = "SELECT COUNT(*) as total_employees FROM Employé";
$total_employees_stmt = $pdo->query($total_employees_sql);
$total_employees = $total_employees_stmt->fetchColumn();

// Fetch employees on leave
$employees_on_leave_sql = "SELECT COUNT(*) as employees_on_leave FROM Congé WHERE Statut = 'Approved' AND Date_Début <= CURDATE() AND Date_Fin >= CURDATE()";
$employees_on_leave_stmt = $pdo->query($employees_on_leave_sql);
$employees_on_leave = $employees_on_leave_stmt->fetchColumn();

// Fetch today's presence count
$todays_presence_sql = "SELECT COUNT(*) as todays_presence FROM toutPrésence WHERE Mois = MONTH(CURDATE()) AND Année = YEAR(CURDATE()) AND Jour".date('j')." = 'P'";
$todays_presence_stmt = $pdo->query($todays_presence_sql);
$todays_presence = $todays_presence_stmt->fetchColumn();

// Fetch the latest employees
$latest_employees_sql = "SELECT EmployéID, NomPrenom, Email, Téléphone, Date_Embauche, DépartementID, Role FROM Employé ORDER BY Date_Embauche DESC LIMIT 5";
$latest_employees_stmt = $pdo->query($latest_employees_sql);
$latest_employees = $latest_employees_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>System De Gestion Resources Humaines</title>
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/hrLogo.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/feathericon.min.css">
    <link rel="stylesheet" href="https://cdn.oesmith.co.uk/morris-0.5.1.css">
    <link rel="stylesheet" href="../assets/plugins/morris/morris.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/stylee.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include('header.php'); ?>
        <?php include('sidebar.php'); ?>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12 mt-5">
                            <h3 class="page-title mt-3">Bienvenue <?= htmlspecialchars($user_name) ?> !</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item active">Table De Bord</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card board1 fill">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <div>
                                        <h3 class="card_widget_header"><?= $total_employees ?></h3>
                                        <h6 class="text-muted">Total des employés</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card board1 fill">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <div>
                                        <h3 class="card_widget_header"><?= $employees_on_leave ?></h3>
                                        <h6 class="text-muted">Employés en congé</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card board1 fill">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <div>
                                        <h3 class="card_widget_header"><?= $todays_presence ?></h3>
                                        <h6 class="text-muted">Présences aujourd'hui</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-6">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h4 class="card-title">Présence mensuelle</h4>
                            </div>
                            <div class="card-body">
                                <div id="bar-chart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h4 class="card-title">Absences</h4>
                            </div>
                            <div class="card-body">
                                <div id="pie-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 d-flex">
                        <div class="card card-table flex-fill">
                            <div class="card-header">
                                <h4 class="card-title float-left mt-2">Derniers employés</h4>
                              <a href="list_employee.php"> <button type="button" class="btn btn-primary float-right veiwbutton">Voir Tout</button></a> 
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center">
                                        <thead>
                                            <tr>
                                                <th>ID Employé </th>
                                                <th>Nom & Prénom</th>
                                                <th class="text-center"> Email </th>
                                                <th> Téléphone</th>
                                                <th class="text-center">Date Embauche</th>
                                                <th>Département</th>
                                                <th class="text-center">Rôle</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($latest_employees as $employee): ?>
                                            <tr>
                                                <td class="text-nowrap"><?= htmlspecialchars($employee['EmployéID']) ?></td>
                                                <td class="text-nowrap"><?= htmlspecialchars($employee['NomPrenom']) ?></td>
                                                <td><?= htmlspecialchars($employee['Email']) ?></td>
                                                <td><?= htmlspecialchars($employee['Téléphone']) ?></td>
                                                <td class="text-center"><?= htmlspecialchars($employee['Date_Embauche']) ?></td>
                                                <td><?= htmlspecialchars($employee['DépartementID']) ?></td>
                                                <td class="text-center"><span class="badge badge-pill bg-success inv-badge"><?= htmlspecialchars($employee['Role']) ?></span></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery-3.5.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../assets/plugins/raphael/raphael.min.js"></script>
    <script src="../assets/plugins/morris/morris.min.js"></script>
    <script src="../assets/js/chart.morris.js"></script>
    <script src="../assets/js/script.js"></script>

    <script>
        $(document).ready(function () {
            var barChartData = [
                { month: '2024-01', value: 40 },
                { month: '2024-02', value: 35 },
                { month: '2024-03', value: 50 },
                { month: '2024-04', value: 45 },
                { month: '2024-05', value: 60 },
                { month: '2024-06', value: 55 }
            ];

            Morris.Bar({
                element: 'bar-chart',
                data: barChartData,
                xkey: 'month',
                ykeys: ['value'],
                labels: ['Présence'],
                barColors: ['#1e88e5'],
                resize: true
            });

            var pieChartData = [
                { label: "Congé Maladie", value: 10 },
                { label: "Vacances", value: 30 },
                { label: "Congé Sans Solde", value: 20 },
                { label: "Autre", value: 15 }
            ];

            Morris.Donut({
                element: 'pie-chart',
                data: pieChartData,
                colors: ['#42a5f5', '#66bb6a', '#ff7043', '#ab47bc'],
                resize: true
            });
        });
    </script>
</body>
</html>
