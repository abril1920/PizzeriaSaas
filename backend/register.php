<?php
    require 'DBConection.php';
    try {
        $name       = $_POST['name'];
        $lastName   = $_POST['lastName'];
        $age        = $_POST['age'];
        $mail       = $_POST['mail'];
        $pass       = $_POST['pass'];

        $errores = [];

        $db = conectarDB();
        $allMails = $db->query("SELECT id FROM users WHERE mail = '$mail'");
        $arrayMails = $allMails->fetchAll();

        if (count($arrayMails) > 0) {
            $errores = "<p> el email ya esta registrado </p>";
        }
    } catch (\Throwable $th) {
        echo "Error al guardar: ". $th;
    }
    // $stm = $db->query("SELECT * FROM users");
    // $usuarios = $stm->fetchAll(PDO::FETCH_ASSOC);
?>  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
        if (count($arrayMails) > 0) {
            // header("Location: ../frontend/views/RegisterView.php");
            echo "<p> el email ya esta registrado </p>";?>
            <a href="../frontend/views/RegisterView.php"> volver</a>
    <?php      
            exit();
        }?>
    
</body>
</html>
<?php 
try {
    $hash   = password_hash($pass, PASSWORD_DEFAULT);
    $sql    = "INSERT INTO users(name,lastName,age,mail,password) VALUES (:name,:lastname,:age,:mail,:password)";
    $save   = $db->prepare($sql);
    $save->execute([
        ':name'     => $name,
        ':lastname' => $lastName,
        ':age'      => $age,
        ':mail'     => $mail,
        ':password' => $hash,
    ]);
    if ($save) {
        echo "¡Se guardo correctamente!";
    }else{
        echo "Error al guardar";
    }
    header("Location: ../frontend/views/LoginView.php");
    exit();
} catch (\Throwable $th) {
    //throw $th;
}
    
?>