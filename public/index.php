<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
   
 require 'vendor/autoload.php';
 
//require 'flight/Flight.php';
Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=u280608908_Checador_db','u280608908_Checador_db','@Carrasco2'));
///////////////////////////////////////////////////////
  
   

  
   
 /*Flight::route('GET /recuperacion', function(){
     $mail = new PHPMailer(true);
     
try{
   
   $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
   //Enable verbose debug output 
   $mail->isSMTP(); 
   
   $mail->SMTPbeug = SMTP:: DEBUG_SERVER;
   
   //Send using SMTP 
   $mail->Host ='smtp.hostinger.com'; 
   //Set the SMTP server to send through 
   $mail->SMTPAuth = true;
   //Enable SMTP authentication 
   $mail->Username ='recuperacion@checador.tech'; 
   //SMTP username 
   $mail->Password ='@Recuperacion123'; 
   //SMTP password 
   $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
   //Enable implicit TLS encryption
   $mail->Port = 465;
  
  //Recipients
    $mail->setFrom('recuperacion@checador.tech', 'codigo');
    $mail->addAddress('cruz.a.rubio7@gmail.com', 'cru');     //Add a recipient
    //$mail->addAddress('ellen@example.com');               //Name is optional
  
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'hola soy yo si funcina  <b>in bold!</b>';
    
    if(!$mail->send()){
        throw new Excepcion($mail->ErrorInfo);
    }
    
    //$mail->send();
      echo 'enviado';
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
 });*/

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///Buscar aulas 
Flight::route('GET /aulas/@idAula', function($idAula){
    $consulta = "SELECT nombre_aula FROM aulas WHERE idAula = ?";
    $sentencia = Flight::db()->prepare($consulta);
    $sentencia->bindParam(1, $idAula);
    $sentencia->execute();

    $aulass = array();
    while($fila = $sentencia->fetch(PDO::FETCH_ASSOC)){
        $aulass[] = array_map('utf8_encode', $fila);
    }

    Flight::json($aulass);
});
//insertar aulas
Flight::route('POST /aulas', function(){
    $idAula = Flight::request()->data->idAula;
    $nombre_aula = Flight::request()->data->nombre_aula;

    $consultaExistencia = "SELECT * FROM aulas WHERE idAula = ?";
    $sentenciaExistencia = Flight::db()->prepare($consultaExistencia);
    $sentenciaExistencia->bindParam(1, $idAula);
    $sentenciaExistencia->execute();

    if ($sentenciaExistencia->rowCount() > 0) {
        Flight::json(["message" => "EXISTE"]);
    } else {
        $consultaInsertar = "INSERT INTO aulas (idAula, nombre_aula) VALUES (?, ?)";
        $sentenciaInsertar = Flight::db()->prepare($consultaInsertar);
        $sentenciaInsertar->bindParam(1, $idAula);
        $sentenciaInsertar->bindParam(2, $nombre_aula);
        $sentenciaInsertar->execute();

        Flight::json(["message" => "INSERTADO"]);
    }
});
//aCTUALIZAR aulas
Flight::route('POST /aulas/update', function(){
    $idAula = Flight::request()->data->idAula;
    $nombre_aula = Flight::request()->data->nombre_aula;

    $consulta = "UPDATE aulas SET nombre_aula = ? WHERE idAula = ?";
    $sentencia = Flight::db()->prepare($consulta);
    $sentencia->bindParam(1, $nombre_aula);
    $sentencia->bindParam(2, $idAula);
    $sentencia->execute();

    Flight::json(["message" => "Actualizado"]);
});
//Borrar aulas
Flight::route('POST /aulas/delete', function(){
    $idAula = Flight::request()->data->idAula;

    $consulta = "DELETE FROM aulas WHERE idAula = ?";
    $sentencia = Flight::db()->prepare($consulta);
    $sentencia->bindParam(1, $idAula);
    $sentencia->execute();

    Flight::json(["message" => "Eliminado"]);
});
//Mostrar datos clases aula
Flight::route('GET /aulas', function(){
    $consulta = "SELECT nombre_aula FROM aulas";
    $sentencia = Flight::db()->prepare($consulta);
    $sentencia->execute();

    $response = array();

    while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) {
        $aula = $row["nombre_aula"];
       
        $item = array(
            "nombre_aula" => $aula,
        );

        $response[] = $item;
    }

    Flight::json($response);
});
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//insertar usuarios
Flight::route('POST /usuarios/registro', function(){
    $nombre = Flight::request()->data->nombre;
    $email = Flight::request()->data->email;
    $contraseña = Flight::request()->data->contraseña;

    try {
        $consultaInsertar = "INSERT INTO usuarios (nombre, email, contraseña) VALUES (?, ?, ?)";
        $sentencia = Flight::db()->prepare($consultaInsertar);
        $sentencia->bindParam(1, $nombre);
        $sentencia->bindParam(2, $email);
        $sentencia->bindParam(3, $contraseña);
        $sentencia->execute();

        if ($sentencia->rowCount() > 0) {
            Flight::json(["message" => "Usuario registrado correctamente"]);
        } else {
            Flight::json(["message" => "Error al insertar el registro"]);
        }
    } catch(PDOException $e) {
        Flight::json(["error" => "Internal server error"]);
    }
});

//Validar usuarios
Flight::route('POST /validar_usuario', function(){
    $email = Flight::request()->data->email;
    $contraseña = Flight::request()->data->contraseña;

    $consultaSeleccionar = "SELECT * FROM usuarios WHERE email = ? AND contraseña = ?";
    $sentencia = Flight::db()->prepare($consultaSeleccionar);
    $sentencia->bindParam(1, $email);
    $sentencia->bindParam(2, $contraseña);
    $sentencia->execute();

    $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        Flight::json($resultado);
    } else {
        Flight::json(["message" => "Login failed"]);
    }
});


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Buscar Docentes
Flight::route('GET /docentes/@Matricula', function($Matricula){
    $consulta = "SELECT * FROM docentes WHERE Matricula = ?";
    $sentencia = Flight::db()->prepare($consulta);
    $sentencia->bindParam(1, $Matricula);
    $sentencia->execute();

    $docentes = array();

    while($fila = $sentencia->fetch(PDO::FETCH_ASSOC)){
        $docentes[] = array_map('utf8_encode', $fila);
    }

    Flight::json($docentes);
}); 
//insertar docentes 
Flight::route('POST /docentes', function(){
    $nombre = Flight::request()->data->nombre;
    $apellidos = Flight::request()->data->apellidos;
    $academia = Flight::request()->data->academia;
    $Matricula = Flight::request()->data->Matricula;

    $consultaExistencia = "SELECT * FROM docentes WHERE Matricula = ?";
    $sentenciaExistencia = Flight::db()->prepare($consultaExistencia);
    $sentenciaExistencia->bindParam(1, $Matricula);
    $sentenciaExistencia->execute();

    if ($sentenciaExistencia->rowCount() > 0) {
        Flight::json(["message" => "EXISTE"]);
    } else {
        $consultaInsertar = "INSERT INTO docentes (nombre, apellidos, academia, Matricula) VALUES (?, ?, ?, ?)";
        $sentenciaInsertar = Flight::db()->prepare($consultaInsertar);
        $sentenciaInsertar->bindParam(1, $nombre);
        $sentenciaInsertar->bindParam(2, $apellidos);
        $sentenciaInsertar->bindParam(3, $academia);
        $sentenciaInsertar->bindParam(4, $Matricula);
        $sentenciaInsertar->execute();

        Flight::json(["message" => "INSERTADO"]);
    }
});
//eliminar docentes 
Flight::route('POST /eliminar-docente', function(){
    $Matricula = Flight::request()->data->Matricula;

    $consulta = "DELETE FROM docentes WHERE Matricula = ?";
    $sentencia = Flight::db()->prepare($consulta);
    $sentencia->bindParam(1, $Matricula);
    
    if ($sentencia->execute()) {
        Flight::json(["message" => "ELIMINADO"]);
    } else {
        Flight::json(["message" => "ERROR"]);
    }
});
//mostrar docentes clases
Flight::route('GET /obtener-docentes', function(){
    $consulta = "SELECT nombre, apellidos FROM docentes";
    $sentencia = Flight::db()->prepare($consulta);
    $sentencia->execute();
    
    $response = array();

    while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) {
        $nombre = $row["nombre"];
        $apellido = $row["apellidos"];

        $item = array(
            "nombre" => $nombre,
            "apellidos" => $apellido
        );

        $response[] = $item;
    }

    Flight::json($response);
});
//editar docentes 
Flight::route('POST /actualizar-docente', function(){
    $nombre = Flight::request()->data->nombre;
    $apellidos = Flight::request()->data->apellidos;
    $academia = Flight::request()->data->academia;
    $Matricula = Flight::request()->data->Matricula;

    $consulta = "UPDATE docentes SET nombre = ?, apellidos = ?, academia = ? WHERE Matricula = ?";
    $sentencia = Flight::db()->prepare($consulta);
    $sentencia->bindParam(1, $nombre);
    $sentencia->bindParam(2, $apellidos);
    $sentencia->bindParam(3, $academia);
    $sentencia->bindParam(4, $Matricula);

    if ($sentencia->execute()) {
        Flight::json(["message" => "ACTUALIZADO"]);
    } else {
        Flight::json(["message" => "ERROR"]);
    }
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//clases
Flight::route('POST /insertar-clase', function(){
    $docentes = Flight::request()->data->docentes;
    $aula = Flight::request()->data->aula;
    $hora = Flight::request()->data->hora;
    $opcion = Flight::request()->data->opcion;
    $fecha = Flight::request()->data->fecha;

    $consultaInsertar = "INSERT INTO clases (docentes, aula, hora, opcion, fecha) VALUES (?, ?, ?, ?, ?)";
    $sentenciaInsertar = Flight::db()->prepare($consultaInsertar);
    $sentenciaInsertar->bindParam(1, $docentes);
    $sentenciaInsertar->bindParam(2, $aula);
    $sentenciaInsertar->bindParam(3, $hora);
    $sentenciaInsertar->bindParam(4, $opcion);
    $sentenciaInsertar->bindParam(5, $fecha);

    if ($sentenciaInsertar->execute()) {
        Flight::json(["message" => "Inserción exitosa"]);
    } else {
        Flight::json(["message" => "Error al insertar"]);
    }
});

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//filtrado de fecha 
//filtrofecharegistro
Flight::route('GET /filtrar-fecha', function(){
    // Obtener los parámetros de filtrado
    $fecha = Flight::request()->query['fecha'];
    $opcion = Flight::request()->query['opcion'];

    // Escapar los parámetros para prevenir inyección de SQL (opcional, dependiendo del caso)
    $fecha = Flight::db()->quote($fecha);
    $opcion = Flight::db()->quote($opcion);

    // Crear la consulta SQL con los parámetros de filtrado
    $consulta = "SELECT * FROM clases WHERE fecha = $fecha AND opcion = $opcion";
    $sentencia = Flight::db()->prepare($consulta);
    $sentencia->execute();

    // Obtener los resultados
    $data = array();

    while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    Flight::json($data);
});
///busquedafiltradofecha
Flight::route('GET /filtrar-clases-fecha', function(){
    // Obtener los parámetros de filtrado
    $fecha = Flight::request()->query['fecha'];

    // Escapar los parámetros para prevenir inyección de SQL (opcional, dependiendo del caso)
    $fecha = Flight::db()->quote($fecha);

    // Crear la consulta SQL con los parámetros de filtrado
    $consulta = "SELECT * FROM clases WHERE fecha = $fecha";
    $sentencia = Flight::db()->prepare($consulta);
    $sentencia->execute();

    // Obtener los resultados
    $data = array();

    while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    Flight::json($data);
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//reportefiltrado
Flight::route('GET /filtrar-reporte', function(){
    // Obtener los parámetros de filtrado
    $fecha = Flight::request()->query['fecha'];
    $hora = Flight::request()->query['hora'];
    $opcion = Flight::request()->query['opcion'];

    // Escapar los parámetros para prevenir inyección de SQL (opcional, dependiendo del caso)
    $fecha = Flight::db()->quote($fecha);
    $hora = Flight::db()->quote($hora);
    $opcion = Flight::db()->quote($opcion);

    // Crear la consulta SQL con los parámetros de filtrado
    $consulta = "SELECT * FROM clases WHERE fecha = $fecha AND hora = $hora AND opcion = $opcion";
    $sentencia = Flight::db()->prepare($consulta);
    $sentencia->execute();

    // Obtener los resultados
    $data = array();

    while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    Flight::json($data);
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//cambiar y obtener  correo 
Flight::route('POST /cambiar-contraseña', function(){
    $email = Flight::request()->data->email;
    $contraseña = Flight::request()->data->contraseña;

    $consulta = "SELECT * FROM usuarios WHERE email = ?";
    $sentenciaConsulta = Flight::db()->prepare($consulta);
    $sentenciaConsulta->bindParam(1, $email);
    $sentenciaConsulta->execute();

    if ($sentenciaConsulta->rowCount() > 0) {
        $actualizarConsulta = "UPDATE usuarios SET contraseña = ? WHERE email = ?";
        $sentenciaActualizar = Flight::db()->prepare($actualizarConsulta);
        $sentenciaActualizar->bindParam(1, $contraseña);
        $sentenciaActualizar->bindParam(2, $email);
        $sentenciaActualizar->execute();

        if ($sentenciaActualizar->rowCount() > 0) {
            $response = "CONTRASEÑA_CAMBIADA";
        } else {
            $response = "ERROR_CONTRASEÑA";
        }
    } else {
        $response = "CORREO_NO_REGISTRADO";
    }

    Flight::json(["message" => $response]);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Verificacion 
Flight::route('POST /solicitar-verificacion', function(){
    $data = Flight::request()->getBody();
    $json_data = json_decode($data);

    if (isset($json_data->email)) {
        $email = $json_data->email;

        // Aquí podrías generar el token de verificación y enviar el correo
        // En este ejemplo, simplemente responderemos con un mensaje de éxito
        $response = array('message' => 'Solicitud de verificación recibida');
        Flight::json($response);
    } else {
        Flight::json(['error' => 'Datos incompletos'], 400);
    }
});


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//reporteselect
Flight::route('GET /obtener-clases', function(){
    $consulta = "SELECT * FROM clases";
    $sentencia = Flight::db()->prepare($consulta);
    $sentencia->execute();

    // Obtener los resultados
    $clases = array();

    while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) {
        $clases[] = $row;
    }

    Flight::json($clases);
});


//////////////////////////////////////////////////////////////////////////////////////////
Flight::route('POST /Validacion', function(){
    $email = Flight::request()->data->email;
    $recuperacion = Flight::request()->data->recuperacion;

    try {
        // Verificar si el correo ya existe en la base de datos
        $consulta_verificar = "SELECT COUNT(*) as count FROM recuperarcontraseña WHERE email = ?";
        $sentencia_verificar = Flight::db()->prepare($consulta_verificar);
        $sentencia_verificar->bindParam(1, $email);
        $sentencia_verificar->execute();
        $resultado = $sentencia_verificar->fetch(PDO::FETCH_ASSOC);

        if ($resultado['count'] > 0) {
            // Si el correo existe, realizar una actualización en lugar de una inserción
            $actualizar_consulta = "UPDATE recuperarcontraseña SET recuperacion = ? WHERE email = ?";
            $sentencia_actualizar = Flight::db()->prepare($actualizar_consulta);
            $sentencia_actualizar->bindParam(1, $recuperacion);
            $sentencia_actualizar->bindParam(2, $email);
            $sentencia_actualizar->execute();
        } else {
            // Si el correo no existe, realizar la inserción normal
            $insercion_consulta = "INSERT INTO recuperarcontraseña (email, recuperacion) VALUES (?, ?)";
            $sentencia_insercion = Flight::db()->prepare($insercion_consulta);
            $sentencia_insercion->bindParam(1, $email);
            $sentencia_insercion->bindParam(2, $recuperacion);
            $sentencia_insercion->execute();
        }
        
         $mail = new PHPMailer(true);
     
try{
   
   $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
   //Enable verbose debug output 
   $mail->isSMTP(); 
   
   $mail->SMTPbeug = SMTP:: DEBUG_SERVER;
   
   //Send using SMTP 
   $mail->Host ='smtp.hostinger.com'; 
   //Set the SMTP server to send through 
   $mail->SMTPAuth = true;
   //Enable SMTP authentication 
   $mail->Username ='recuperacion@checador.tech'; 
   //SMTP username 
   $mail->Password ='@Recuperacion123'; 
   //SMTP password 
   $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
   //Enable implicit TLS encryption
   $mail->Port = 465;
  
  //Recipients
    $mail->setFrom('recuperacion@checador.tech', 'CheckTech Recuperacion');
    $mail->addAddress($email);     //Add a recipient
    //$mail->addAddress('ellen@example.com');               //Name is optional
  
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Codigo de Acceso';
    $mail->Body = 'Tu código de recuperación es: ' . $recuperacion;

    
    if(!$mail->send()){
        throw new Excepcion($mail->ErrorInfo);
    }
    
    //$mail->send();
      echo 'enviado';
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }

        Flight::json(["message" => "Data inserted or updated successfully"]);
    } catch(PDOException $e) {
        Flight::json(["error" => "Internal server error"]);
    }
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Flight::route('POST /validar_recuperacion', function(){
    $email = Flight::request()->data->email;
    $recuperacion = Flight::request()->data->recuperacion;

    $consultaSeleccionar = "SELECT * FROM recuperarcontraseña WHERE email = ? AND recuperacion = ?";
    $sentencia = Flight::db()->prepare($consultaSeleccionar);
    $sentencia->bindParam(1, $email);
    $sentencia->bindParam(2, $recuperacion);
    $sentencia->execute();

    $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        Flight::json($resultado);
    } else {
        Flight::json(["message" => "Login failed"]);
    }
});

Flight::start();