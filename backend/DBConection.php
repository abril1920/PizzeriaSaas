<?php
    function conectarDB(){
        $dsn    = 'mysql:host=localhost;dbname=projectUser';
        $user   = 'root';
        $pass   = '';

        try {
            $pdo = new PDO($dsn,$user,$pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Conectado a la db Correctamente <br>";
            return $pdo;
        } catch (PDOException $e) {
            echo "Error: ".$e->getMessage();
        }
    }
    // $stm = $pdo->prepare("SELECT * FROM users");//      - PREPARAR CONSULTA
    // $stm->execute();                            //      - EJECUTAR CONSULTA
    // // $users = $stm->fetch(PDO::FETCH_ASSOC);
    // $usuarios = $stm->fetchAll(PDO::FETCH_ASSOC);
?>