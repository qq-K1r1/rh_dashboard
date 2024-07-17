<?php
include '../db_config.php'; // Include your database configuration file

function fetchFormationReport($pdo) {
    try {
        $sql = "
            SELECT 
                e.NomPrenom AS employe_nom,
                f.Nom_Formation AS formation_nom,
                f.Date_Formation AS formation_date,
                f.Duree AS formation_duree,
                d.Nom_Département AS departement_nom,
                COUNT(ev.EvaluationID) AS nombre_evaluations
            FROM 
                Employé e
                JOIN EmployeeFormation ef ON e.EmployéID = ef.EmployeID
                JOIN Formation f ON f.FormationID = ef.FormationID
                JOIN Département d ON e.DépartementID = d.DépartementID
                LEFT JOIN Evaluation ev ON e.EmployéID = ev.EmployéID
            GROUP BY 
                e.EmployéID, f.FormationID
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        return [];
    }
}

function fetchFormationSummary($pdo) {
    try {
        $summary = [
            'total_sessions' => 0,
            'average_evaluations' => 0,
            'top_department' => ''
        ];

        $sql_total_sessions = "SELECT COUNT(*) AS total_sessions FROM Formation";
        $stmt = $pdo->prepare($sql_total_sessions);
        $stmt->execute();
        $summary['total_sessions'] = (int) $stmt->fetchColumn();

        $sql_average_evaluations = "
            SELECT AVG(evaluation_count) AS average_evaluations
            FROM (
                SELECT COUNT(ev.EvaluationID) AS evaluation_count
                FROM Employé e
                LEFT JOIN Evaluation ev ON e.EmployéID = ev.EmployéID
                GROUP BY e.EmployéID
            ) subquery
        ";
        $stmt = $pdo->prepare($sql_average_evaluations);
        $stmt->execute();
        $summary['average_evaluations'] = round((float) $stmt->fetchColumn(), 2);

        $sql_top_department = "
            SELECT d.Nom_Département, COUNT(*) AS formation_count
            FROM Formation f
            JOIN Employé e ON f.EmployéID = e.EmployéID
            JOIN Département d ON e.DépartementID = d.DépartementID
            GROUP BY d.Nom_Département
            ORDER BY formation_count DESC
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql_top_department);
        $stmt->execute();
        $top_department = $stmt->fetch(PDO::FETCH_ASSOC);
        $summary['top_department'] = $top_department ? htmlspecialchars($top_department['Nom_Département']) : '';

        return $summary;
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        return [
            'total_sessions' => 0,
            'average_evaluations' => 0,
            'top_department' => ''
        ];
    }
}

$formations = fetchFormationReport($pdo);
$summary = fetchFormationSummary($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de Formation</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <header>
        <!-- Inclure le contenu de l'en-tête -->
    </header>
    <nav>
        <!-- Inclure le contenu de la barre latérale -->
    </nav>

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Rapport de Formation</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="manager.php">Tableau de Bord</a></li>
                        <li class="breadcrumb-item active">Rapport de Formation</li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped custom-table">
                            <thead>
                                <tr>
                                    <th>Nom de l'Employé</th>
                                    <th>Nom de la Formation</th>
                                    <th>Date de Formation</th>
                                    <th>Durée</th>
                                    <th>Département</th>
                                    <th>Nombre d'Évaluations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($formations) > 0): ?>
                                    <?php foreach ($formations as $formation): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($formation['employe_nom']) ?></td>
                                            <td><?= htmlspecialchars($formation['formation_nom']) ?></td>
                                            <td><?= htmlspecialchars($formation['formation_date']) ?></td>
                                            <td><?= htmlspecialchars($formation['formation_duree']) ?></td>
                                            <td><?= htmlspecialchars($formation['departement_nom']) ?></td>
                                            <td><?= htmlspecialchars($formation['nombre_evaluations']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6">Aucune donnée de formation trouvée.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h4>Informations sur la Formation</h4>
                    <ul>
                        <li>Total de Sessions de Formation Réalisées : <?= $summary['total_sessions'] ?></li>
                        <li>Évaluations Moyennes par Employé : <?= $summary['average_evaluations'] ?></li>
                        <li>Département avec le Plus de Formations : <?= $summary['top_department'] ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
