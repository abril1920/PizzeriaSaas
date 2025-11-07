<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <title>Login</title>
</head>
<body>
    <?php include_once "components/header.html"; ?>
    <main>
            <form class="formLog" action="../../backend/login.php" method="post">
                <h2 class="logtit">Ingresa</h2>
                <label for="mail">Correo Electronico: 
                    <input type="mail" name="mail" required>
                </label>
                
                <label for="pass">Contraseña: 
                   <input type="password" name="pass" required> 
                </label>
                

                <button type="submit" >Ingresar</button>
                <p>aun no tienes cuenta? <a href="RegisterView.php">crea tu cuenta</a></p>
            </form>
    </main>
    <?php include_once "components/footer.html"; ?>
</body>
</html>