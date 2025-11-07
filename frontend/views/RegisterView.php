<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <title>Registro User - Inventraios</title>
</head>
<body>
    <header>
        <img src="" alt="">
        <h1>Registro de usuario</h1>
        <nav>
            <a href="LoginView.php">Iniciar Sesiòn</a>
        </nav>
    </header>
    <main>
        <!-- <div> -->
            <form class="formLog" action="../../backend/register.php" method="post" enctype="multipart/form-data">
                <h2 class="logtit">Registro</h2>
                <label for="name">Nombre:
                    <input type="text"      name="name" required>
                </label>
                
                <label for="lastName">Apellido:
                    <input type="text"      name="lastName" required>
                </label>
                
                <label for="age">Edad: 
                    <input type="number"    name="age" required maxlength="1">
                </label>
                
                <label for="mail">Correo:
                    <input type="email"     name="mail" required>
                </label>
                
                <label for="pass">Contraseña: 
                    <input type="password"  name="pass" required>
                </label>
                <button type="submit" >Registrar</button>
                <p>Ya tiene cuenta? <a href="LoginView.php">Ingresa aqui</a></p>
            </form>
        <!-- </div> -->
    </main>
    <?php include_once "components/footer.html";?>
</body>
</html>