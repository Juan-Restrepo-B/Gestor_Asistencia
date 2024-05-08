function registerAndCheckIn() {
    const userid = document.getElementById('userid').value;
    const action = document.getElementById('action').value; // Esto puede ser 'register' o 'checking' dependiendo del contexto de tu aplicación
    const control = document.getElementById('control').value; // Asegúrate de obtener el valor correcto del control

    const url = 'http://pruebasregistro.juanprestrepob.com/?action=' + encodeURIComponent(action) + '&control=' + encodeURIComponent(control) + '&userid=' + encodeURIComponent(userid);

    // Registrar el log y el check-in
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'checkin.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                document.getElementById('message').textContent = xhr.responseText;
            } else {
                document.getElementById('message').textContent = 'Error en el servidor.';
            }
        }
    };

    // Aquí, incluye el campo userid en la solicitud POST
    const formData = new FormData();
    formData.append('userid', userid);
    formData.append('action', action);
    formData.append('control', control);

    xhr.send(formData);
}

