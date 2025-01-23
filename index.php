<?php
include 'includes/db.php';

function createEmployee($db, $firstName, $lastName, $departmentId) {
    $insert = $db->prepare('INSERT INTO employees SET first_name = :first_name, last_name = :last_name, id_department = :id_department');
    $insert->bindValue(':first_name', trim(htmlspecialchars($firstName)), PDO::PARAM_STR);
    $insert->bindValue(':last_name', trim(htmlspecialchars($lastName)), PDO::PARAM_STR);
    $insert->bindValue(':id_department', $departmentId, PDO::PARAM_INT);
    $insert->execute();
    return $db->lastInsertId();
}

function updateEmployee($db, $id, $firstName, $lastName, $departmentId) {
    $update = $db->prepare('UPDATE employees SET first_name = :first_name, last_name = :last_name, id_department = :id_department WHERE id = :id');
    $update->bindValue(':first_name', trim(htmlspecialchars($firstName)), PDO::PARAM_STR);
    $update->bindValue(':last_name', trim(htmlspecialchars($lastName)), PDO::PARAM_STR);
    $update->bindValue(':id_department', $departmentId, PDO::PARAM_INT);
    $update->bindValue(':id', $id, PDO::PARAM_INT);
    $update->execute();
}

function deleteEmployee($db, $id) {
    $delete = $db->prepare('DELETE FROM employees WHERE id = :id');
    $delete->bindValue(':id', $id, PDO::PARAM_INT);
    $delete->execute();
}

function getAllEmployees($db) {
    $query = $db->prepare('SELECT employees.id AS employee_id, employees.first_name, employees.last_name, departments.name AS department_name FROM employees INNER JOIN departments ON employees.id_department = departments.id');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getEmployeeById($db, $id) {
    $query = $db->prepare('SELECT * FROM employees WHERE id = :id');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

function getAllDepartments($db) {
    $query = $db->prepare('SELECT * FROM departments');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

if (!empty($_POST["Enregistrer"]) && !empty($_POST["first_name"]) && !empty($_POST["last_name"]) && $_POST["department"] > 0) {
    createEmployee($db, $_POST["first_name"], $_POST["last_name"], $_POST["department"]);
}

if (!empty($_POST["Editer"]) && !empty($_POST["first_name"]) && !empty($_POST["last_name"]) && $_POST["department"] > 0) {
    updateEmployee($db, $_POST["id_employee"], $_POST["first_name"], $_POST["last_name"], $_POST["department"]);
}

if (!empty($_GET["id"]) && !empty($_GET["action"]) && $_GET["action"] == "effacer" && $_GET["id"] > 0) {
    deleteEmployee($db, $_GET["id"]);
}

$allDepartments = getAllDepartments($db);

if (!empty($_GET["action"]) && $_GET["action"] == "editer") {
    $titre = "Editer";
    $employeeData = getEmployeeById($db, $_GET["id"]);
} else {
    $titre = "Enregistrer";
    $employeeData = ["first_name" => "", "last_name" => "", "id" => "none", "id_department" => "none"];
}

$getEmployees = getAllEmployees($db);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des employés</title>
</head>
<body>
    <a href="/index.php">GESTION EMPLOYES</a>&nbsp;<a href="/services.php">GESTION SERVICES</a>
    <h1>Listes des employés</h1>
    <?php foreach ($getEmployees as $employee): ?>
        <?= $employee['first_name'] ?> <?= $employee['last_name'] ?> <?= $employee['department_name'] ?>
        <a href="index.php?action=effacer&id=<?= $employee['employee_id'] ?>">effacer</a>&nbsp;
        <a href="index.php?action=editer&id=<?= $employee['employee_id'] ?>">editer</a><br>
    <?php endforeach; ?>
    <h1><?= $titre ?> un employé</h1>
    <form method="post" action="index.php">
        <select name="department">
            <?php foreach ($allDepartments as $department): ?>
                <option value="<?= $department['id'] ?>" <?= $employeeData["id_department"] == $department['id'] ? 'selected' : '' ?>>
                    <?= $department['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="hidden" name="id_employee" value="<?= $employeeData["id"] ?>">
        <input type="text" name="first_name" placeholder="Prénom" value="<?= $employeeData["first_name"] ?>"><br>
        <input type="text" name="last_name" placeholder="Nom" value="<?= $employeeData["last_name"] ?>"><br>
        <input type="submit" name="<?= $titre ?>" value="<?= $titre ?>">
    </form>
    <script src="./js/modal.js"></script>
</body>
</html>