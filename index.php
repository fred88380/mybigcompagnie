<?php
// Activation des erreurs PHP pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusion de la connexion à la base de données
include 'includes/db.php';

// Fonction pour créer un employé
function createEmployee($db, $firstName, $lastName, $departmentId)
{
    // Débogage des valeurs reçues
    echo "Prénom : $firstName<br>";
    echo "Nom : $lastName<br>";
    echo "ID Département : $departmentId<br>";

    // Insertion de l'employé dans la base de données
    $insert = $db->prepare('INSERT INTO employees (first_name, last_name, id_department) VALUES (:first_name, :last_name, :id_department)');
    $insert->bindValue(':first_name', trim(htmlspecialchars($firstName)), PDO::PARAM_STR);
    $insert->bindValue(':last_name', trim(htmlspecialchars($lastName)), PDO::PARAM_STR);
    $insert->bindValue(':id_department', $departmentId, PDO::PARAM_INT);
    $insert->execute();
    return true;

    // Exécution de la requête
    if ($insert->execute()) {
        return true;
    } else {
        // Afficher l'erreur SQL en cas d'échec
        echo "Erreur SQL : ";
        print_r($insert->errorInfo());
        return false;
    }
}

// Fonction pour mettre à jour un employé
function updateEmployee($db, $id, $firstName, $lastName, $departmentId)
{
    $update = $db->prepare('UPDATE employees SET first_name = :first_name, last_name = :last_name, id_department = :id_department WHERE id = :id');
    $update->bindValue(':first_name', trim(htmlspecialchars($firstName)), PDO::PARAM_STR);
    $update->bindValue(':last_name', trim(htmlspecialchars($lastName)), PDO::PARAM_STR);
    $update->bindValue(':id_department', $departmentId, PDO::PARAM_INT);
    $update->bindValue(':id', $id, PDO::PARAM_INT);
    return $update->execute();
}

// Fonction pour supprimer un employé
function deleteEmployee($db, $id)
{
    $delete = $db->prepare('DELETE FROM employees WHERE id = :id');
    $delete->bindValue(':id', $id, PDO::PARAM_INT);
    return $delete->execute();
}

// Fonction pour récupérer tous les employés
function getAllEmployees($db)
{
    $query = $db->prepare('SELECT employees.id AS employee_id, employees.first_name, employees.last_name, departments.name AS department_name FROM employees INNER JOIN departments ON employees.id_department = departments.id');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer un employé par son ID
function getEmployeeById($db, $id)
{
    $query = $db->prepare('SELECT * FROM employees WHERE id = :id');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer tous les départements
function getAllDepartments($db)
{
    $query = $db->prepare('SELECT * FROM departments');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

// Traitement de l'ajout d'un employé
if (!empty($_POST["Enregistrer"]) && !empty($_POST["first_name"]) && !empty($_POST["last_name"]) && $_POST["department"] > 0) {
    var_dump("vcxcvvx");
    if (createEmployee($db, $_POST["first_name"], $_POST["last_name"], $_POST["department"])) {
        // Redirection après l'ajout réussi
       // header("Location: index.php");
        // exit();
    } else {
        var_dump("vcxcvvx");
        // Gérer le cas d'échec de l'insertion
        echo "Erreur lors de l'ajout de l'employé.";
    }
}

// Traitement de l'édition d'un employé
if (!empty($_POST["Editer"]) && !empty($_POST["first_name"]) && !empty($_POST["last_name"]) && $_POST["department"] > 0) {
    if (updateEmployee($db, $_POST["id_employee"], $_POST["first_name"], $_POST["last_name"], $_POST["department"])) {
        // Redirection après la mise à jour réussie
        header("Location: index.php");
        exit();
    } else {
        // Gérer le cas d'échec de la mise à jour
        echo "Erreur lors de la mise à jour de l'employé.";
    }
}

// Traitement de la suppression d'un employé
if (!empty($_GET["id"]) && !empty($_GET["action"]) && $_GET["action"] == "effacer" && $_GET["id"] > 0) {
    if (deleteEmployee($db, $_GET["id"])) {
        // Redirection après la suppression réussie
        header("Location: index.php");
        exit();
    } else {
        // Gérer le cas d'échec de la suppression
        echo "Erreur lors de la suppression de l'employé.";
    }
}

// Récupération des départements pour afficher dans le formulaire
$allDepartments = getAllDepartments($db);

// Définition du titre en fonction de l'action (ajouter ou éditer un employé)
if (!empty($_GET["action"]) && $_GET["action"] == "Editer") {
    $titre = "Éditer";
    $employeeData = getEmployeeById($db, $_GET["id"]);
} else {
    $titre = "Enregistrer";
    $employeeData = ["first_name" => "", "last_name" => "", "id" => "none", "id_department" => "none"];
}

// Récupérer tous les employés
$getEmployees = getAllEmployees($db);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/code.css">
    <title>Gestion des employés</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg ">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">MyBigCompagnie</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" 
                data-target="#navbarNav" aria-controls="navbarNav" 
                aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php">Gestion Employés</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/services.php">Gestion Services</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <div class="container my-5">
        <div class="row">
           
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Liste des employés</span>
                        <select class="form-control w-auto" id="filter" onchange="filterEmployees(this.value)">
                            <option value="">Tous les services</option>
                            <?php foreach ($allDepartments as $department): ?>
                                <option value="<?= $department['name'] ?>"><?= $department['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <tbody>
                                <?php foreach ($getEmployees as $employee): ?>
                                <tr data-department="<?= $employee['department_name'] ?>">
                                    <td><?= $employee['first_name'] ?></td>
                                    <td><?= $employee['last_name'] ?></td>
                                    <td><?= $employee['department_name'] ?></td>
                                    <td>
                                        <a href="index.php?action=effacer&id=<?= $employee['employee_id'] ?>" class="btn btn-danger btn-sm">Effacer</a>
                                        <a href="index.php?action=Editer&id=<?= $employee['employee_id'] ?>" class="btn btn-primary btn-sm">Éditer</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <?= $titre ?> un employé
                    </div>
                    <div class="card-body">
                        <form method="post" action="index.php">
                            <div class="form-group">
                                <label for="department">Département</label>
                                <select class="form-control" name="department" id="department">
                                    <?php foreach ($allDepartments as $department): ?>
                                    <option value="<?= $department['id'] ?>" <?= $employeeData["id_department"] == $department['id'] ? 'selected' : '' ?>>
                                        <?= $department['name'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="id_employee" value="<?= $employeeData["id"] ?>">
                                <label for="first_name">Prénom</label>
                                <input type="text" class="form-control" name="first_name" id="first_name" value="<?= $employeeData["first_name"] ?>">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Nom</label>
                                <input type="text" class="form-control" name="last_name" id="last_name" value="<?= $employeeData["last_name"] ?>">
                            </div>
                            <button type="submit" name="<?= $titre ?>" class="btn btn-<?= $titre == 'Enregistrer' ? 'success' : 'primary' ?>"><?= $titre ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./js/modal.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterEmployees(departmentName) {
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const departmentCell = row.getAttribute('data-department');
                if (!departmentName || departmentCell === departmentName) {
                    row.style.display = ""; 
                } else {
                    row.style.display = "none"; 
                }
            });
        }
    </script>
</body>
</html>
