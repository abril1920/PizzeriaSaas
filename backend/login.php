<?php
    require 'DBConection.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $logMail = $_POST['mail'];
            $logPass = $_POST['pass'];

            // echo $logMail;
            // echo $logPass;
            $pdo = conectarDB();
            $stm = $pdo->prepare("SELECT id, password FROM users WHERE mail = ?");
            $stm->execute([$logMail]);

            $user = $stm->fetch(PDO::FETCH_ASSOC);

            // echo $user['id'];
            // echo $user['password'];

            if ($user && password_verify($logPass, $user['password'])){
                echo "Has ingresado como usuario";
                header("Location: ../frontend/views/DashboardMain.php");
            }else{
                echo "Credenciales invalidas";
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
?>