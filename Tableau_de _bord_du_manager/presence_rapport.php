<?php
session_start();
include '../db_config.php';

// Activer le rapport d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

function fetchMonthlyAttendance($pdo, $search = null, $month = null)
{
    $sql = "SELECT tp.*, e.NomPrenom as EmployeNomPrenom, d.Nom_Département as Departement, d.Entreprise as Entreprise,
                   SUM(CASE WHEN tp.Jour1 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour2 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour3 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour4 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour5 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour6 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour7 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour8 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour9 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour10 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour11 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour12 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour13 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour14 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour15 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour16 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour17 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour18 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour19 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour20 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour21 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour22 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour23 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour24 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour25 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour26 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour27 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour28 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour29 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour30 = 'P' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour31 = 'P' THEN 1 ELSE 0 END) as TotalPresence,
                   SUM(CASE WHEN tp.Jour1 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour2 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour3 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour4 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour5 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour6 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour7 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour8 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour9 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour10 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour11 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour12 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour13 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour14 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour15 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour16 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour17 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour18 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour19 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour20 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour21 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour22 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour23 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour24 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour25 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour26 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour27 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour28 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour29 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour30 = 'A' THEN 1 ELSE 0 END +
                       CASE WHEN tp.Jour31 = 'A' THEN 1 ELSE 0 END) as TotalAbsence,
                   COUNT(CASE WHEN tp.Jour1 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour2 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour3 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour4 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour5 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour6 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour7 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour8 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour9 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour10 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour11 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour12 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour13 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour14 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour15 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour16 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour17 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour18 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour19 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour20 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour21 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour22 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour23 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour24 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour25 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour26 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour27 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour28 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour29 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour30 IS NOT NULL THEN 1 END +
                         CASE WHEN tp.Jour31 IS NOT NULL THEN 1 END) as DaysPresent,
                   tp.Mois, tp.Année
            FROM toutPrésence tp
            JOIN Employé e ON tp.EmployéID = e.EmployéID
            JOIN Département d ON e.DépartementID = d.DépartementID";

    $conditions = [];
    $params = [];

    if ($search) {
        $conditions[] = "e.NomPrenom LIKE :search";
        $params['search'] = '%' . $search . '%';
    }

    if ($month) {
        $monthParts = explode('-', $month);
        $conditions[] = "tp.Mois = :month AND tp.Année = :year";
        $params['month'] = $monthParts[1];
        $params['year'] = $monthParts[0];
    }

    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY tp.EmployéID, tp.Mois, tp.Année";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchMonth = isset($_GET['month']) ? $_GET['month'] : '';

// Récupérer les données de présence
$attendanceData = fetchMonthlyAttendance($pdo, $searchTerm, $searchMonth);

// Obtenir les informations du responsable de session
$rhManagerName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Responsable inconnu';
$reportDate = date('d/m/Y H:i:s');
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de gestion des ressources humaines - Rapport de présence</title>
    <link rel="stylesheet" href="../assets/css/stylee.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            color: white;
            text-align: center;
            padding: 1em 0;
        }

        section {
            margin: 20px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .report-details {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f2f2f2;
        }

        .report-details p {
            margin: 5px 0;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
        }

        button {
            background-color: #009efb;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #009efb;
        }

        .search-form {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-form input[type="text"],
        .search-form input[type="month"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .search-form button {
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #009efb;
            color: white;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #45a049;
        }

        /* Styles pour l'impression */
        @media print {
            .search-form,
            .search-form *,
            button {
                display: none;
            }

            table {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>Système de gestion des ressources humaines</h1>
        <h2>Rapport de présence</h2>
    </header>

    <section id="attendance-report">
        <div class="report-details">
            <p>Généré par : <?php echo $rhManagerName; ?></p>
            <p>Date et heure : <?php echo $reportDate; ?></p>
            <?php if ($attendanceData && count($attendanceData) > 0) : ?>
                <p>Entreprise : <?php echo $attendanceData[0]['Entreprise']; ?></p>
                <p>Département : <?php echo $attendanceData[0]['Departement']; ?></p>
            <?php else : ?>
                <p>Aucune donnée de présence disponible.</p>
            <?php endif; ?>
        </div>

        <div class="search-form">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Rechercher par nom d'employé" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <input type="month" name="month" value="<?php echo htmlspecialchars($searchMonth); ?>">
                <button type="submit">Rechercher</button>
            </form>
        </div>

        <h3>Résumé de présence mensuelle</h3>
        <div class="table-container">
            <table id="attendance-table">
                <thead>
                    <tr>
                        <th>Identifiant Employé</th>
                        <th>Nom de l'employé</th>
                        <th>Mois</th>
                        <th>Année</th>
                        <th>Total Présence</th>
                        <th>% Présence</th>
                        <th>Total Absence</th>
                        <th>% Absence</th>
                    </tr>
                </thead>
                <tbody id="attendance-data">
                    <?php if ($attendanceData) : ?>
                        <?php foreach ($attendanceData as $record) : ?>
                            <?php
                            // Calcul des pourcentages de présence et d'absence
                            $totalDaysInMonth = 31; // Assurez-vous que ce nombre correspond au mois en cours.
                            $presencePercentage = ($record['TotalPresence'] / $totalDaysInMonth) * 100;
                            $absencePercentage = ($record['TotalAbsence'] / $totalDaysInMonth) * 100;
                            ?>
                            <tr>
                                <td><?php echo $record['EmployéID']; ?></td>
                                <td><?php echo $record['EmployeNomPrenom']; ?></td>
                                <td><?php echo $record['Mois']; ?></td>
                                <td><?php echo $record['Année']; ?></td>
                                <td><?php echo $record['TotalPresence']; ?></td>
                                <td><?php echo number_format($presencePercentage, 2) . '%'; ?></td>
                                <td><?php echo $record['TotalAbsence']; ?></td>
                                <td><?php echo number_format($absencePercentage, 2) . '%'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <button onclick="printReport()">Imprimer le rapport</button>
    </section>

    <script>
        function printReport() {
            window.print();
        }
    </script>
</body>

</html>
