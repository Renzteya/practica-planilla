<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validar los campos del formulario
    $required_fields = ['Name', 'Phone_Number', 'Email', 'Message'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $response = array("status" => "error", "message" => "Por favor, completa todos los campos del formulario");
            echo json_encode($response);
            exit();
        }
    }

    // Obtener datos del formulario y sanearlos
    $name = strip_tags(htmlspecialchars($_POST['Name']));
    $email = strip_tags(htmlspecialchars($_POST['Email']));
    $subject = strip_tags(htmlspecialchars($_POST['Phone_Number']));
    $message = strip_tags(htmlspecialchars($_POST['Message']));

    // Configuración del destinatario (tu correo de Zoho)
    $to_zoho = "renzoleonardo.luquechino@renzteya.site"; // Cambia esto con tu correo de Zoho
    $subject_zoho = "$subject: $name";
    $body_zoho = "
    <html>
    <head>
    <title>Nuevo mensaje desde el formulario de contacto</title>
    </head>
    <body>
    <p>Has recibido un nuevo mensaje desde el formulario de contacto de tu sitio web.</p>
    <p><strong>Detalles:</strong></p>
    <ul>
        <li><strong>Nombre:</strong> $name</li>
        <li><strong>Email:</strong> $email</li>
        <li><strong>Telefono:</strong> $subject</li>
        <li><strong>Mensaje:</strong> $message</li>
    </ul>
    </body>
    </html>
    ";

    // Configuración de PHPMailer para Zoho
    $mail_zoho = new PHPMailer(true);
    $mail_zoho->isSMTP();
    $mail_zoho->Host = "smtp.zoho.com";
    $mail_zoho->SMTPAuth = true;
    $mail_zoho->Username = "renzoleonardo.luquechino@renzteya.site"; // Cambia esto con tu correo de Zoho
    $mail_zoho->Password = "Renzo73929102"; // Cambia esto con tu contraseña de Zoho
    $mail_zoho->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail_zoho->Port = 587;

    // Configurar el remitente y destinatario para Zoho
    $mail_zoho->setFrom("renzoleonardo.luquechino@renzteya.site", $name); // Cambia esto con tu correo de Zoho y nombre del remitente
    $mail_zoho->addAddress($to_zoho);

    // Configurar el contenido del mensaje para Zoho
    $mail_zoho->isHTML(true);
    $mail_zoho->Subject = $subject_zoho;
    $mail_zoho->Body = $body_zoho;

    if (!empty($_FILES['attachment']['name'])) {
        $attachment_path = $_FILES['attachment']['tmp_name'];
        $attachment_name = $_FILES['attachment']['name'];
        $mail_zoho->addAttachment($attachment_path, $attachment_name);
    }

    // Intentar enviar el mensaje a Zoho
    try {
        $mail_zoho->send();
    } catch (Exception $e) {
        $response = array("status" => "error", "message" => "Error al enviar el correo a Zoho");
        echo json_encode($response);
        exit();
    }

    // Obtener el contenido HTML del archivo 'mensaje-remitente.html'
    $html_content_user = file_get_contents('mensaje-remitente.html');
    
    // Reemplazar las etiquetas de marcador de posición con los datos del remitente
    $html_content_user = str_replace('{{NOMBRE_REMITENTE}}', $name, $html_content_user);
    $html_content_user = str_replace('{{EMAIL_REMITENTE}}', $email, $html_content_user);
    $html_content_user = str_replace('{{TELEFONO_REMITENTE}}', $subject, $html_content_user);
    $html_content_user = str_replace('{{MENSAJE_REMITENTE}}', $message, $html_content_user);

    // Resto del código sin cambios
    // ...

    // Configuración de PHPMailer para el usuario
    $mail_user = new PHPMailer(true);
    $mail_user->isSMTP();
    $mail_user->Host = "smtp-relay.brevo.com";
    $mail_user->SMTPAuth = true;
    $mail_user->Username = "renzoluquechino1@gmail.com"; // Cambia esto con tu correo de Brevo
    $mail_user->Password = "HR1nGxMc9FqvSDOg"; // Cambia esto con tu clave SMTP de Brevo
    $mail_user->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail_user->Port = 587;

    // Configurar el remitente y destinatario para el usuario
    $mail_user->setFrom("renzoleonardo.luquechino@renzteya.site", "Renzteya"); // Cambia esto con tu correo de Brevo y nombre del remitente
    $mail_user->addAddress($email);
    
    // Configurar el contenido del mensaje para el usuario
    $mail_user->isHTML(true);
    $mail_user->Subject = "Gracias por ponerte en contacto, $name";
    $mail_user->Body = $html_content_user;

    if (!empty($_FILES['attachment']['name'])) {
        $mail_user->addAttachment($attachment_path, $attachment_name);
    }

    // Intentar enviar el mensaje al usuario
    try {
        $mail_user->send();
        $response = array("status" => "success", "message" => "Correo enviado correctamente");
        echo json_encode($response);
        exit();
    } catch (Exception $e) {
        $response = array("status" => "error", "message" => "Error al enviar el correo al usuario");
        echo json_encode($response);
        exit();
    }
}
?>
