<?php
include 'includes/db.php';

if (!empty($_POST["Enregistrer"]) and !empty($_POST["first_name"]) and !empty($_POST["last_name"]) and $_POST["department"] > 0) {
    $insert = $db->prepare('INSERT INTO employees SET first_name = :first_name, last_name = :last_name, id_department = :id_department');
    $insert->bindValue(':first_name', $_POST["first_name"], PDO::PARAM_STR);
    $insert->bindValue(':last_name', $_POST["last_name"], PDO::PARAM_STR);
    $insert->bindValue(':id_department', $_POST["department"], PDO::PARAM_INT);
    $insert->execute();
    $insert_id = $db->lastInsertId();
}

if (!empty($_POST["Editer"]) and !empty($_POST["first_name"]) and !empty($_POST["last_name"]) and $_POST["department"] > 0) {
    $insert = $db->prepare('update employees SET first_name = :first_name, last_name = :last_name, id_department = :id_department where id = :id_employee');
    $insert->bindValue(':first_name', $_POST["first_name"], PDO::PARAM_STR);
    $insert->bindValue(':last_name', $_POST["last_name"], PDO::PARAM_STR);
    $insert->bindValue(':id_department', $_POST["department"], PDO::PARAM_INT);
    $insert->bindValue(':id_employee', $_POST["id_employee"], PDO::PARAM_INT);
    $insert->execute();
}

if (!empty($_GET["id"]) and !empty($_GET["action"]) and $_GET["action"] == "effacer" and $_GET["id"] > 0) {
    $delete = $db->prepare('DELETE FROM employees WHERE id = :id');
    $delete->bindValue(':id', $_GET["id"], PDO::PARAM_INT);
    $delete->execute();
}

$getEmployees = $db->query('select employees.id AS employee_id, employees.first_name, employees.last_name, departments.name from employees inner join departments on employees.id_department = departments.id');
$getDepartments = $db->query('select * from departments');
$allDepartments = $getDepartments->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Php sql</title>
</head>
<body>
    <a href="/index.php">GESTION EMPLOYES</a>&nbsp;<a href="/services.php">GESTION SERVICES</a>
    <h1>Listes des employés</h1>
    <?php
        while ($employee = $getEmployees->fetchObject()) {
            echo "$employee->first_name $employee->last_name $employee->name";
            echo " <a href=\"index.php?action=effacer&id=$employee->employee_id\">effacer</a>&nbsp;";
            echo '<a href="index.php?action=editer&id=' . $employee->employee_id . '">editer</a><br>';
        }
    ?>
    <?php 
        if (!empty($_GET["action"]) and $_GET["action"] == "editer") {
            $titre = "Editer";
            $edit = $db->prepare('select * FROM employees WHERE id = :id');
            $edit->bindValue(':id', $_GET["id"], PDO::PARAM_INT);
            $edit->execute();
            $employeeData = $edit->fetch();
        } else {
            $titre = "Enregistrer";
            $employeeData["first_name"] = "";
            $employeeData["last_name"] = "";
            $employeeData["id"] = "none";
            $employeeData["id_department"] = "none";
        }
    ?>
    <h1><?php echo $titre ?> un employé</h1>
    <form method="post" action="index.php">
        <select name="department">
        <?php
            foreach ($allDepartments as $department) {
                if ($employeeData["id_department"] == $department["id"]) {
                    echo "<option value=" . $department["id"] . " selected>" . $department["name"] . "</option>";
                } else {
                    echo "<option value=" . $department["id"] . ">" . $department["name"] . "</option>";
                }
            }
        ?>
        </select>
        <br>
        <input type="hidden" name="id_employee" value="<?php echo $employeeData["id"] ?>">
        <input type="text" name="first_name" placeholder="prenom" value="<?php echo $employeeData["first_name"] ?>"><br>
        <input type="text" name="last_name" placeholder="nom" value="<?php echo $employeeData["last_name"] ?>"><br>
        <input type="submit" name="<?php echo $titre ?>" value="<?php echo $titre ?>">
    </form>
</body>
</html>