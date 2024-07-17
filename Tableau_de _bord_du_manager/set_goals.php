<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>System De Gestion Resources Humaines</title>
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/hrLogo.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/feathericon.min.css">
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
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title float-left mt-2">Définir un Objectif</h4>
                        </div>
                    </div>
                </div>

                <div class="container mt-5">
                    <div class="card">
                        <div class="card-body">
                            <form action="save_objective.php" method="post">
                                <div class="form-group">
                                    <label for="objectiveTitle">Titre de l'Objectif</label>
                                    <input type="text" class="form-control" id="objectiveTitle" name="objectiveTitle" required>
                                </div>
                                <div class="form-group">
                                    <label for="objectiveDescription">Description de l'Objectif</label>
                                    <textarea class="form-control" id="objectiveDescription" name="objectiveDescription" rows="4" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="assignedEmployees">Employés Assignés</label>
                                    <select multiple class="form-control" id="assignedEmployees" name="assignedEmployees[]" required>
                                        <!-- Replace with dynamic options fetched from database -->
                                        <option value="1">Employé 1</option>
                                        <option value="2">Employé 2</option>
                                        <option value="3">Employé 3</option>
                                        <!-- Example static options; populate dynamically as per your database -->
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Enregistrer Objectif</button>
                            </form>
                        </div>
                    </div>
                </div>

                <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
            </div>
        </div>
    </div>
</body>

</html>
