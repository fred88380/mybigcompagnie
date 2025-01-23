<?php
include 'includes/db.php';

function createDepartment($db, $name) {
    $insert = $db->prepare('INSERT INTO departments SET name = :name');
    $insert->bindValue(':name', trim(htmlspecialchars($name)), PDO::PARAM_STR);
    $insert->execute();
    return $db->lastInsertId();
}

function updateDepartment($db, $id, $name) {
    $update = $db->prepare('UPDATE departments SET name = :name WHERE id = :id');
    $update->bindValue(':name', trim(htmlspecialchars($name)), PDO::PARAM_STR);
    $update->bindValue(':id', $id, PDO::PARAM_INT);
    $update->execute();
}

function deleteDepartment($db, $id) {
    $delete = $db->prepare('DELETE FROM departments WHERE id = :id');
    $delete->bindValue(':id', $id, PDO::PARAM_INT);
    $delete->execute();
}

function getAllDepartments($db) {
    $query = $db->prepare('SELECT * FROM departments');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getDepartmentById($db, $id) {
    $query = $db->prepare('SELECT * FROM departments WHERE id = :id');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

if (!empty($_POST["Enregistrer"]) and !empty($_POST["name"])) {
    createDepartment($db, $_POST["name"]);
}

if (!empty($_POST["Editer"]) and !empty($_POST["name"])) {
    updateDepartment($db, $_POST["id"], $_POST["name"]);
}

if (!empty($_GET["id"]) and !empty($_GET["action"]) and $_GET["action"] == "effacer" and $_GET["id"] > 0) {
    deleteDepartment($db, $_GET["id"]);
}

$getDepartments = getAllDepartments($db);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" 
    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" 
    crossorigin="anonymous">
    <link rel="stylesheet" href="./css/code.css">
    <title>Php sql</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">MyBigCompagnie</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
</nav>
    <a href="./index.php">GESTION EMPLOYES</a>&nbsp;<a href="./services.php">GESTION SERVICES</a>
    <h1>Listes des services</h1>
    <?php
    foreach ($getDepartments as $department) {
        echo $department["name"];
        echo " <a href=\"services.php?action=effacer&id=" . $department["id"] . "\">effacer</a>&nbsp;";
        echo '<a href="services.php?action=editer&id=' . $department["id"] . '">editer</a><br>';
    }
    ?>
    <?php 
    if (!empty($_GET["action"]) and $_GET["action"] == "editer") {
        $titre = "Editer";
        $departmentData = getDepartmentById($db, $_GET["id"]);
    } else {
        $titre = "Enregistrer";
        $departmentData = ["name" => "", "id" => ""];
    }
    ?>
    <h1><?php echo $titre; ?> un service</h1>
    <form method="post" action="services.php">
        <br>
        <input type="hidden" name="id" value="<?php echo $departmentData['id']; ?>">
        <input type="text" name="name" placeholder="nom" value="<?php echo $departmentData['name']; ?>"><br>
        <input type="submit" name="<?php echo $titre; ?>" value="<?php echo $titre; ?>">
    </form>
    <script src="./js/modal.js"></script>
    <script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>