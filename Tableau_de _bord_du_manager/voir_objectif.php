<?php
session_start();
include('../db_config.php');

// Handle pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 6;
$offset = ($page - 1) * $items_per_page;

// Handle search queries
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$search_status = isset($_GET['search_status']) ? trim($_GET['search_status']) : '';

try {
    // Prepare SQL query with search conditions
    $sql = "SELECT o.*, e.NomPrenom AS EmployeeName 
            FROM Objectifs o
            JOIN Employé e ON o.EmployeID = e.EmployéID
            WHERE 1=1";
    
    if ($search_name !== '') {
        $sql .= " AND e.NomPrenom LIKE :search_name";
    }
    if ($search_status !== '') {
        $sql .= " AND o.Statut = :search_status";
    }
    
    $sql .= " ORDER BY o.ObjectifID DESC 
            LIMIT :limit OFFSET :offset";

    // Prepare and execute the main query
    $stmt = $pdo->prepare($sql);

    if ($search_name !== '') {
        $stmt->bindValue(':search_name', '%' . $search_name . '%', PDO::PARAM_STR);
    }
    if ($search_status !== '') {
        $stmt->bindValue(':search_status', $search_status, PDO::PARAM_STR);
    }
    
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $objectifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total number of pages for pagination
    $countSql = "SELECT COUNT(*) 
                 FROM Objectifs o
                 JOIN Employé e ON o.EmployeID = e.EmployéID
                 WHERE 1=1";
    
    if ($search_name !== '') {
        $countSql .= " AND e.NomPrenom LIKE :search_name";
    }
    if ($search_status !== '') {
        $countSql .= " AND o.Statut = :search_status";
    }

    $countStmt = $pdo->prepare($countSql);

    if ($search_name !== '') {
        $countStmt->bindValue(':search_name', '%' . $search_name . '%', PDO::PARAM_STR);
    }
    if ($search_status !== '') {
        $countStmt->bindValue(':search_status', $search_status, PDO::PARAM_STR);
    }
    
    $countStmt->execute();
    $total_items = $countStmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit(); // Add exit to stop further execution on database error
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Objectifs Progress</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/hrLogo.png">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/stylee.css">
    <style>
        .objectif-card {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
        }

        .objectif-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .objectif-title {
            font-size: 18px;
            font-weight: bold;
        }

        .objectif-status {
            font-size: 14px;
            color: #007bff;
        }

        .sub-objectifs-list {
            margin-top: 10px;
            padding-left: 20px;
        }

        .sub-objectif-item {
            list-style-type: none;
            margin-bottom: 5px;
        }

        .pagination {
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include('header.php'); ?>
        <?php include('sidebar.php'); ?>

        <!-- Page Content Section -->
        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title mt-5">Objectifs en Cours</h3>
                        </div>
                    </div>
                </div>

                <!-- Search Form -->
                <div class="row">
                    <div class="col-lg-12">
                        <form method="GET" action="">
                            <div class="form-row align-items-center">
                                <div class="col-auto">
                                    <input type="text" class="form-control mb-2" name="search_name" placeholder="Search by Name" value="<?= htmlspecialchars($_GET['search_name'] ?? '') ?>">
                                </div>
                                <div class="col-auto">
                                    <select class="form-control mb-2" name="search_status">
                                        <option value="">Search by Status</option>
                                        <option value="En cours" <?= (isset($_GET['search_status']) && $_GET['search_status'] == 'En cours') ? 'selected' : '' ?>>En cours</option>
                                        <option value="Terminé" <?= (isset($_GET['search_status']) && $_GET['search_status'] == 'Terminé') ? 'selected' : '' ?>>Terminé</option>
                                        <option value="Non commencé" <?= (isset($_GET['search_status']) && $_GET['search_status'] == 'Non commencé') ? 'selected' : '' ?>>Non commencé</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary mb-2">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- End Search Form -->

                <div class="row">
                    <div class="col-lg-12">
                        <?php if (!empty($objectifs) && count($objectifs) > 0) : ?>
                            <?php foreach ($objectifs as $objectif) : ?>
                                <?php
                                $objectif_id = $objectif['ObjectifID'];
                                $objectif_title = $objectif['Titre'] ?? 'No Title';
                                $objectif_status = $objectif['Statut'] ?? 'No Status';
                                $objectif_description = $objectif['Description'] ?? 'No Description';
                                $employee_name = $objectif['EmployeeName'] ?? 'No Employee';

                                // Fetch sub-objectives for the current objective
                                $subSql = "SELECT * FROM Objectifs WHERE ParentObjectifID = ?";
                                $subStmt = $pdo->prepare($subSql);
                                $subStmt->execute([$objectif_id]);
                                $subObjectifs = $subStmt->fetchAll();

                                // Calculate progress based on sub-objectives
                                $totalSubObjectifs = count($subObjectifs);
                                $completedSubObjectifs = 0;

                                foreach ($subObjectifs as $subObjectif) {
                                    if ($subObjectif['Statut'] == 'Terminé') {
                                        $completedSubObjectifs++;
                                    }
                                }

                                $progressPercentage = ($totalSubObjectifs > 0) ? ($completedSubObjectifs / $totalSubObjectifs) * 100 : 0;
                                ?>

                                <div class="card objectif-card">
                                    <div class="card-body">
                                        <div class="objectif-header">
                                            <div class="objectif-title"><?= htmlspecialchars($objectif_title) ?></div>
                                            <div class="objectif-status"><?= htmlspecialchars($objectif_status) ?></div>
                                        </div>
                                        <div class="objectif-details">
                                            <p><strong>Description:</strong> <?= htmlspecialchars($objectif_description) ?></p>
                                            <p><strong>Employé Assigné:</strong> <?= htmlspecialchars($employee_name) ?></p>
                                            <p><strong>Progression:</strong> <?= number_format($progressPercentage, 2) ?>%</p>
                                        </div>
                                        <ul class="sub-objectifs-list">
                                            <?php if ($totalSubObjectifs > 0) : ?>
                                                <?php foreach ($subObjectifs as $subObjectif) : ?>
                                                    <?php
                                                    $subTitle = $subObjectif['Titre'] ?? 'No Title';
                                                    $subStatus = $subObjectif['Statut'] ?? 'No Status';
                                                    ?>
                                                    <li class="sub-objectif-item"><?= htmlspecialchars($subTitle) ?> - <?= htmlspecialchars($subStatus) ?></li>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <li class="sub-objectif-item">No sub-objectives found.</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p>No objectives found.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Pagination -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <?php if ($page > 1) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&search_name=<?= urlencode($search_name) ?>&search_status=<?= urlencode($search_status) ?>">Previous</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&search_name=<?= urlencode($search_name) ?>&search_status=<?= urlencode($search_status) ?>"><?= $i ?></a></li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&search_name=<?= urlencode($search_name) ?>&search_status=<?= urlencode($search_status) ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <!-- End Pagination -->
            </div>
        </div>
    </div>

    <!-- External JS libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>
