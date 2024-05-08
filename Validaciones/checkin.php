<?php
session_start();

// Registrar el check-in en la base de datos (reemplaza estos datos con los tuyos)
$host = "72.167.100.192";
$user = "Desarrollo_Summit";
$pass = "y9B>^y=>FT+G`C@,";
$database = "registro_summit";

// Crear conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

if (isset($_POST['action'], $_POST['control'])) {
    $action = $_POST['action'];
    $control = $_POST['control'];

    date_default_timezone_set('America/Bogota');
    $fechareg = date('Y-m-d H:i:s');

    // Si la acción es 'register', verificar si el 'userid' está presente
    if ($action === 'register' && isset($_POST['userid'])) {
        $userid = $_POST['userid'];
        $_SESSION['userid'] = $userid;

        // Verificar la conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Preparar y ejecutar la consulta de inserción
        $stmt = $conn->prepare("INSERT INTO LOG_USERS (LOG_IDUSER, LOG_FECHORA, LOG_PUNTO , LOG_ACCION) VALUES (?, ?, ?, ?)");

        $stmt->bind_param("ssss", $userid, $fechareg, $control, $action);

        if ($stmt->execute()) {
            echo 'Check-in exitoso para: ' . $userid;
            setcookie('userid', $userid, time() + (86400 * 30), '/'); // La cookie expirará en 30 días
        } else {
            echo 'Error al realizar el check-in. Error: ' . $stmt->error;
        }

        // Cerrar la conexión
        $stmt->close();
        $conn->close();
    } else if ($action === 'registro' && isset($_POST['userid'])) {
        $userid = $_POST['userid'];
        $sponser = $_SESSION['idsponser'];

        // Verificar la conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Preparar y ejecutar la consulta de inserción
        $stmt = $conn->prepare("INSERT INTO LOGS_VISITAS (LOGV_SPONNER, LOGV_IDVISITANTE, LOGV_FECHORA) VALUES (?, ?, ?)");

        $stmt->bind_param("ssss", $sponser, $userid, $fechareg);

        if ($stmt->execute()) {
            header('Location: registroe2.php');
        } else {
            echo 'Error al realizar el registro. Error: ' . $stmt->error;
        }

        // Cerrar la conexión
        $stmt->close();
        $conn->close();
    } else if ($action === 'checking') {
        // Obtener el valor del userid almacenado en la sesión
        $userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

        // Verificar si el iduser está presente
        if (!$userid) {
            echo 'Por favor realizar el check-in primero.';
        } else {
            // Consulta para obtener el último registro de entrada y salida del día anterior
            $sql = "SELECT LOG_PUNTO FROM LOG_USERS WHERE LOG_IDUSER = ? AND DATE(LOG_FECHORA) = CURDATE() - INTERVAL 1 DAY ORDER BY LOG_FECHORA DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $userid);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $lastControl = $row['LOG_PUNTO'];

            // Verificar si se puede realizar el check-in del punto de control actual
            if ($control === 'INGRESO' && $lastControl !== 'SALIDA') {
                echo 'No se puede registrar el ingreso sin haber marcado la salida del día anterior.';
            } elseif ($control === 'SALIDA' && $lastControl !== 'INGRESO') {
                echo 'No se puede registrar la salida sin haber marcado la entrada del día anterior.';
            } else {
                // Realizar el registro del punto de control
                $stmt = $conn->prepare("INSERT INTO LOG_USERS (LOG_IDUSER, LOG_FECHORA, LOG_PUNTO , LOG_ACCION) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $userid, $fechareg, $control, $action);

                if ($stmt->execute()) {
                    echo $userid . ' se registró exitosamente: ' . $control;
                } else {
                    echo 'Error al realizar el registro del punto de control. Error: ' . $stmt->error;
                }
            }

            // Cerrar la conexión
            $stmt->close();
            $conn->close();
        }
    } else {
        echo 'Acción desconocida.';
    }
} else {
    echo 'Datos insuficientes para realizar el check-in.';
}
?>