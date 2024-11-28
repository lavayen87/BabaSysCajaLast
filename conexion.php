<?php

$connection = mysqli_connect('localhost', 'daniel', 'd4n13l1593.', 'db_sistema');

/* verificar la conexión */
if (mysqli_connect_errno()) {
    printf("Falló la conexión: %s\n", mysqli_connect_error());
    exit();
}

//printf("Conjunto de caracteres inicial: %s\n", mysqli_character_set_name($connection));

/* cambiar el conjunto de caracteres a utf8 */
if (mysqli_set_charset($connection, "utf8")) {
    mysqli_character_set_name($connection);
}

?>
