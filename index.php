<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="48x48" href="Img/Logo.png">
    <link rel="stylesheet" href="Css/Styles.css">
    <title>REGISTRO SUMMIT</title>
</head>

<body>
    <div class="wrapper login">
        <!-- Logo de la esquina superior -->
        <div class="container">
            <div class="col-left">
                <div class="login-text">
                    <img src="Img/Logo.png" alt="">
                </div>
            </div>
            <!-- Formulario del registro -->
            <div class="col-right">
                <div class="login-form">
                    <!-- Titulo del formulario -->
                    <h2>LOGIN</h2>
                    <form action="index.php" method="POST">
                        <p>
                            <!-- Solicitud del usuario -->
                            <label>USERNAME<span>*</span></label>
                            <input type="text" placeholder="USERNAME" name="user" required>
                        </p>
                        <p>
                            <!-- Solicitud de la contraseña -->
                            <label>PASSWORD<span>*</span></label>
                            <input type="password" placeholder="PASSWORD" name="pass" required>
                        </p>
                        <p>
                            <input type="submit" name="Sign In">
                        </p>
                        <?php
                        // Mantiene la sesion y informacion activa
                        session_start();

                        // Conxion con BD
                        

                        // Establecer la zona horaria a "America/Bogota"
                        date_default_timezone_set('America/Bogota');

                        // Variables
                        $color = 'red';
                        $currentHour = date('H');

                        // Definir la franja horaria permitida
                        $allowedStartHour = 7; // Hora de inicio permitida (7 AM)
                        $allowedEndHour = 18; // Hora de finalización permitida (6 PM)
                        
                        // Recibe la informacion del formulario
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            // Recibe el usuario y en caso contrario asigna una cadena vacia
                            $USUARIO = isset($_POST["user"]) ? $_POST["user"] : '';
                            // Recibe el password y en caso contrario asigna una cadena vacia
                            $PASSWORD = isset($_POST["pass"]) ? $_POST["pass"] : '';

                            // Query de consulta sobre la BD
                            $query = mysqli_query($conn, "SELECT USER, USERS_PERMISOS, USERS_SPONSER FROM USERS WHERE USER = '" . $USUARIO . "'");
                            $query1 = mysqli_query($conn, "SELECT PASS FROM USERS WHERE PASS = '" . $PASSWORD . "'");
                            $nr = mysqli_num_rows($query);
                            $nr1 = mysqli_num_rows($query1);

                            if ($nr == 1) {
                                $row = mysqli_fetch_assoc($query);
                                $row1 = mysqli_fetch_assoc($query1);
                                $user_permission = $row['USERS_PERMISOS'];
                                $user_sponser = $row['USERS_SPONSER'];

                                if ($nr1 == 1) {
                                    // Si el usuario es administrador (permiso = 1) o está dentro de la franja horaria
                                    if ($user_permission == 1 || $user_permission >= 2 && ($currentHour >= $allowedStartHour && $currentHour <= $allowedEndHour)) {
                                        $_SESSION['user_permission'] = $user_permission;
                                        $_SESSION['idsponser'] = $user_sponser;
                                        $_SESSION['logged_in'] = true;
                                        $_SESSION['last_activity'] = time();
                                        header("Location: Menu/main.php");
                                        exit(); // Detener la ejecución del código
                                    }
                                } else {
                                    $color = 'red';
                                    echo '<span style="color: ' . $color . ';"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" style="fill: rgba(255, 0, 0, 1);transform: ;msFilter:;">
                                    <path d="M11.953 2C6.465 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.493 2 11.953 2zM12 20c-4.411 0-8-3.589-8-8s3.567-8 7.953-8C16.391 4 20 7.589 20 12s-3.589 8-8 8z"></path>
                                    <path d="M11 7h2v7h-2zm0 8h2v2h-2z"></path></svg>  Contraseña incorrecta</span>';
                                }
                            } else {
                                echo '<span style="color: ' . $color . ';"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" style="fill: rgba(255, 0, 0, 1);transform: ;msFilter:;">
                                <path d="M11.953 2C6.465 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.493 2 11.953 2zM12 20c-4.411 0-8-3.589-8-8s3.567-8 7.953-8C16.391 4 20 7.589 20 12s-3.589 8-8 8z"></path>
                                <path d="M11 7h2v7h-2zm0 8h2v2h-2z"></path></svg>  Usuario incorrecto</span>';
                            }
                        } else {
                            echo "El inicio de sesión está permitido solo entre las $allowedStartHour:00 y las $allowedEndHour:00.";
                        }
                        ?>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <p>&copy;Todos los derechos reservados por &nbsp;
        <div class="class"> Juan Restrepo</div>
        </p>
    </footer>
    <script>
        window.onload = function () {
            // Obtener los parámetros de la URL
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action');
            const control = urlParams.get('control');
            const userid = urlParams.get('userid');

            if (action === 'register' && control) {
                // Realizar el check-in enviando una solicitud POST a checkin.php
                const formData = new FormData();
                formData.append('userid', userid);
                formData.append('action', action);
                formData.append('control', control);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', './Validaciones/checkin.php', true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // Respuesta del servidor
                            console.log(xhr.responseText);
                            // Almacenar el userid en las cookies
                            setCookie('userid', userid, 30); // La cookie expirará en 30 días
                            window.location.href = './Validaciones/registroe.php'; // Redirección a registroe.php
                        } else {
                            console.log('Error en el servidor.');
                            window.location.href = './Validaciones/registrof.php'; // Redirección a registrof.php
                        }
                    }
                };
                xhr.send(formData);
            } else if (action === 'checking' && control) {
                // Realizar el check-in enviando una solicitud POST a checkin.php
                const formData = new FormData();
                formData.append('userid', userid);
                formData.append('action', action);
                formData.append('control', control);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', './Validaciones/checkin.php', true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // Respuesta del servidor
                            console.log(xhr.responseText);

                            // Obtener el userid desde las cookies
                            const userid = getCookie('userid');
                            if (!userid) {
                                // Si no se encuentra el userid en las cookies, redirigir a user.php
                                window.location.href = './Validaciones/user.php';
                            } else if (control === 'INGRESO') {
                                window.location.href = './Validaciones/entrada.php'; // Redirección a entrada.php
                            } else if (control === 'SALIDA') {
                                window.location.href = './Validaciones/salida.php'; // Redirección a salida.php
                            } else if (control === 'SALON') {
                                window.location.href = './Validaciones/sala.php'; // Redirección a sala.php
                            } else if (control === 'SIMPOCIO') {
                                window.location.href = './Validaciones/segui.php'; // Redirección a seguimiento.php
                            } else {
                                console.log('Control desconocido.');
                            }
                        } else {
                            console.log('Error en el servidor.');
                        }
                    }
                };
                xhr.send(formData);
            }
        };

        // Función para obtener el valor de una cookie
        function getCookie(name) {
            const value = "; " + document.cookie;
            const parts = value.split("; " + name + "=");
            if (parts.length === 2) return parts.pop().split(";").shift();
        }

        // Función para establecer una cookie
        function setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + date.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }
    </script>
</body>

</html>