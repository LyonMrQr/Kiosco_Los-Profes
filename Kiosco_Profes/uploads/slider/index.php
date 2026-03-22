<?php
// ==================================================================================
// CONFIGURACIÓN Y UTILIDADES PHP
// ==================================================================================

// Iniciar sesión para rastrear el estado del usuario (admin) y la sesión del estudiante.
session_start();

// --- 1. Configuración de la Base de Datos MySQL ---
// ATENCIÓN: Debe reemplazar estos valores con sus credenciales reales de MySQL.
define('DB_HOST', 'sql209.infinityfree.com');
define('DB_USER', 'if0_40756910');
define('DB_PASS', 'Pb3WWjLAQ1wNCAt');
define('DB_NAME', 'if0_40756910_kiosco_profes_db'); // Asegúrese de que esta DB exista (ejecute database_setup.sql)

// --- 2. Conexión a la Base de Datos ---
$pdo = null;
$db_connected = false;
$error_message = '';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_connected = true;
} catch (PDOException $e) {
    // Si falla la conexión, la aplicación lo mostrará en el frontend.
    $db_connected = false;
    $error_message = "⚠️ Error de conexión a la base de datos. Por favor, revise DB_HOST, DB_USER y DB_PASS. Mensaje: " . $e->getMessage();
}

// --- GESTIÓN DE SLIDER Y ASIGNATURAS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Agregar Slide
    if (isset($_POST['action']) && $_POST['action'] === 'add_slide') {
        $title = $_POST['title'];
        $file = $_FILES['slide_image'];

        $target_dir = "uploads/slider/";
        $file_extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $stmt = $pdo->prepare("INSERT INTO slider_content (image_path, title) VALUES (?, ?)");
            $stmt->execute([$target_file, $title]);
        }
    }

    // 2. Agregar Asignatura
    if (isset($_POST['action']) && $_POST['action'] === 'add_subject') {
        $name = $_POST['subject_name'];
        $color = $_POST['subject_color'];
        $stmt = $pdo->prepare("INSERT INTO subjects_list (name, color_hex) VALUES (?, ?)");
        $stmt->execute([$name, $color]);
    }

    // 3. Eliminar (Simple)
    if (isset($_POST['delete_type'])) {
        $id = $_POST['id'];
        if ($_POST['delete_type'] === 'slide') {
            $stmt = $pdo->prepare("DELETE FROM slider_content WHERE id = ?");
        } else {
            $stmt = $pdo->prepare("DELETE FROM subjects_list WHERE id = ?");
        }
        $stmt->execute([$id]);
    }
}
// --- 3. Variables y Constantes de la Aplicación ---
// Mantenemos la lista de asignaturas para la lógica, aunque la vista principal use las tarjetas.
$ASSIGNATURES = [
    'Matemáticas',
    'Inglés',
    'Química',
    'Física',
    'Biología',
    'Comprensión Lectora',
    'Ciencias Sociales',
    'Otro tipo de asesorías'
];

// NUEVO: Definición de las tarjetas para la vista interactiva
$ASSIGNATURE_CARDS = [
    [
        'subject' => 'Matemáticas',
        'icon' => '<svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M19 10H5a2 2 0 00-2 2v2a2 2 0 002 2h14a2 2 0 002-2v-2a2 2 0 00-2-2z"></path></svg>', // Icono de Suma/Fórmula
        'color' => 'hover:bg-indigo-50 border-indigo-500'
    ],
    [
        'subject' => 'Inglés',
        'icon' => '<svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m6 0a9 9 0 01-9-9"></path></svg>', // Icono de Mundo/Idioma
        'color' => 'hover:bg-red-50 border-red-500'
    ],
    [
        'subject' => 'Química',
        'icon' => '<svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19K5.6 13.6L12 7l6.4 6.6K11 19K11 15.6H5.6K11 19V15.6K11 15.6H16.4V19K16.4 15.6H21L12 7Z"></path></svg>', // Icono de Tubo de Ensayo
        'color' => 'hover:bg-green-50 border-green-500'
    ],
    [
        'subject' => 'Física',
        'icon' => '<svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>', // Icono de Rayo/Energía
        'color' => 'hover:bg-blue-50 border-blue-500'
    ],
    [
        'subject' => 'Biología',
        'icon' => '<svg class="w-10 h-10 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.606-4.606a2 2 0 10-2.828-2.828l-5.606 5.606a2 2 0 000 2.828l2.828 2.828a2 2 0 002.828 0l5.606-5.606z"></path></svg>', // Icono de ADN (simulado)
        'color' => 'hover:bg-teal-50 border-teal-500'
    ],
    [
        'subject' => 'Comprensión Lectora',
        'icon' => '<svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.467 9.5 4.5 8 4.5c-1.253 0-2.333.67-3 1.5M12 6.253C13.168 5.467 14.5 4.5 16 4.5c1.253 0 2.333.67 3 1.5M12 6.253v13"></path></svg>', // Icono de Libro Abierto
        'color' => 'hover:bg-purple-50 border-purple-500'
    ],
    [
        'subject' => 'Ciencias Sociales',
        'icon' => '<svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2a3 3 0 015-2.236M11 5.05L17 12V4zm-3-1l5 4.95"></path></svg>', // Icono de Personas/Comunidad
        'color' => 'hover:bg-yellow-50 border-yellow-600'
    ],
    [
        'subject' => 'Otro tipo de asesorías',
        'icon' => '<svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>', // Icono de Más/Otro
        'color' => 'hover:bg-gray-100 border-gray-500'
    ],
];


$VIEWS = [
    'SCHEDULE_VIEW' => 'schedule',
    'PAYMENT_VIEW' => 'payment',
    'ADMIN_LOGIN' => 'admin_login',
    'ADMIN_DASHBOARD' => 'admin_dashboard',
];

// Obtener o crear un ID de sesión para el estudiante anónimo (simula el Firebase UID)
if (!isset($_SESSION['student_session_id'])) {
    $_SESSION['student_session_id'] = uniqid('student_');
}
$student_session_id = $_SESSION['student_session_id'];

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Inicializar estado de la cita actual
$current_appointment = null;


// --- PROCESAR LIBERACIÓN MANUAL ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_payment_manual') {
    $id_cita = $_POST['id'];
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'PAID' WHERE id = ?");
    if ($stmt->execute([$id_cita])) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?filter=" . ($_GET['filter'] ?? 'ALL') . "&success=1");
        exit();
    }
}
// --- 4. Funciones CRUD para MySQL ---
// --- ACCIÓN: CONFIRMAR PAGO MANUALMENTE (ADMIN) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_payment_manual') {
    $appointment_id = $_POST['id'];

    // Cambiamos el estado a 'PAID' (que es el que configuramos como verde)
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'PAID' WHERE id = ?");

    if ($stmt->execute([$appointment_id])) {
        // Redirigimos para ver los cambios
        $filter = $_GET['filter'] ?? 'ALL';
        header("Location: " . $_SERVER['PHP_SELF'] . "?view=" . $VIEWS['ADMIN_DASHBOARD'] . "&filter=" . $filter . "&success=1");
        exit();
    }
}
/**
 * Intenta obtener la cita actual del estudiante. También cancela citas expiradas.
 */
function get_current_appointment($student_id, $pdo)
{
    global $error_message;
    if (!$pdo) return null;

    try {
        // Primero, cancelar cualquier cita PENDING_PAYMENT que haya expirado
        $stmt_cancel = $pdo->prepare("
            UPDATE appointments 
            SET status = 'CANCELLED' 
            WHERE status = 'PENDING_PAYMENT' 
            AND expires_at < NOW()
        ");
        $stmt_cancel->execute(); // Ejecutar en todas las citas, no solo en la del estudiante actual

        // Luego, buscar la cita activa (no CANCELLED, no PAID) para el estudiante actual
        $stmt = $pdo->prepare("
            SELECT * FROM appointments 
            WHERE student_session_id = ? 
            AND status IN ('PENDING_PAYMENT', 'PENDING_VALIDATION')
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$student_id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        // Convertir strings de fecha a objetos DateTime para consistencia
        if ($appointment) {
            // Manejar expires_at que puede ser NULL
            $appointment['created_at'] = new DateTime($appointment['created_at']);
            if ($appointment['expires_at']) {
                $appointment['expires_at'] = new DateTime($appointment['expires_at']);
            }
        }

        return $appointment;
    } catch (PDOException $e) {
        $error_message = "Error al obtener cita: " . $e->getMessage();
        return null;
    }
}
// --- Lógica para Actualizar Estado de la Cita (Confirmar/Rechazar) ---
// --- ACCIÓN: CONFIRMAR PAGO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_payment') {
    $appointment_id = $_POST['id'];

    // Actualizamos a PAID
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'PAID' WHERE id = ?");

    if ($stmt->execute([$appointment_id])) {
        // Mantenemos el filtro actual para que el usuario no se pierda
        $current_filter = $_GET['filter'] ?? 'ALL';
        header("Location: " . $_SERVER['PHP_SELF'] . "?filter=" . $current_filter . "&success=1");
        exit();
    }
}
// --- Lógica para Confirmar Pago y Liberar Asesoría ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_payment') {
    $appointment_id = $_POST['id'];

    // Actualizamos el estado a CONFIRMED
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'CONFIRMADO/PAGADA' WHERE id = ?");
    if ($stmt->execute([$appointment_id])) {
        // Redirigimos para refrescar la tabla y mostrar el cambio
        header("Location: " . $_SERVER['PHP_SELF'] . "?msg=success");
        exit();
    }
}


// --- Lógica para Exportar Informe CSV Organizado ---
if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
    // 1. Obtener filtros de la URL
    $current_filter = $_GET['filter'] ?? 'ALL';
    $search = $_GET['search'] ?? '';

    // 2. Construir consulta dinámica
    $query = "SELECT id, student_name, student_contact, subject, other_subject, date, time, status FROM appointments WHERE 1=1";
    $query = "SELECT id, student_name, student_contact, subject, date, time, status, reference_pay FROM appointments";
    $params = [];

    // Filtro por Estado (Botones/Cards)
    if ($current_filter !== 'ALL') {
        $query .= " AND status = :status";
        $params['status'] = $current_filter;
    }

    // Filtro por Buscador (Nombre o Materia)
    if (!empty($search)) {
        $query .= " AND (student_name LIKE :search OR subject LIKE :search OR other_subject LIKE :search OR id LIKE :search)";
        $params['search'] = "%$search%";
    }

    $query .= " ORDER BY date DESC, time DESC";

    // 3. Ejecutar
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Limpiar buffer para evitar errores de descarga
    if (ob_get_length()) ob_end_clean();

    // 5. Cabeceras para descarga limpia
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Reporte_Filtrado_' . date('d-m-Y') . '.csv');

    $output = fopen('php://output', 'w');

    // Soporte para Excel (Tildes y Ñ) y forzar separador
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputs($output, "sep=,\n");

    // Cabeceras de las columnas
    fputcsv($output, ['ID', 'ESTUDIANTE', 'CONTACTO', 'ASIGNATURA', 'DETALLE MATERIA', 'FECHA', 'HORA', 'ESTADO']);

    // Escribir datos organizados
    foreach ($results as $row) {
        fputcsv($output, [
            $row['id'],
            mb_strtoupper($row['student_name'], 'UTF-8'),
            $row['student_contact'],
            $row['subject'],
            $row['other_subject'] ?: 'N/A',
            date('d/m/Y', strtotime($row['date'])),
            date('h:i A', strtotime($row['time'])),
            strtoupper($row['status'])
        ]);
    }

    fclose($output);
    exit();
}

/**
 * Obtiene una sola cita por ID (útil para el administrador al editar).
 */
function get_appointment_by_id($id, $pdo)
{
    global $error_message;
    if (!$pdo) return null;

    try {
        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        // Convertir strings de fecha a objetos DateTime
        if ($appointment) {
            if (isset($appointment['created_at']) && is_string($appointment['created_at'])) {
                $appointment['created_at'] = new DateTime($appointment['created_at']);
            }
            if (isset($appointment['expires_at']) && is_string($appointment['expires_at']) && $appointment['expires_at']) {
                $appointment['expires_at'] = new DateTime($appointment['expires_at']);
            }
        }
        return $appointment;
    } catch (PDOException $e) {
        $error_message = "Error al obtener cita por ID: " . $e->getMessage();
        return null;
    }
}


/**
 * Obtiene todas las citas para el panel de administración.
 */
function get_all_appointments($pdo)
{
    global $error_message;
    if (!$pdo) return [];

    try {
        $stmt = $pdo->query("SELECT * FROM appointments ORDER BY created_at DESC");
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convertir strings de fecha a objetos DateTime
        foreach ($appointments as &$app) {
            // Asegurarse de que el campo existe y es un string antes de intentar convertir
            if (isset($app['created_at']) && is_string($app['created_at'])) {
                $app['created_at'] = new DateTime($app['created_at']);
            }
            if (isset($app['expires_at']) && is_string($app['expires_at']) && $app['expires_at']) {
                $app['expires_at'] = new DateTime($app['expires_at']);
            }
        }

        return $appointments;
    } catch (PDOException $e) {
        $error_message = "Error al obtener citas para Admin: " . $e->getMessage();
        return [];
    }
}


/**
 * Agenda una nueva cita. (Create - C)
 */
function schedule_appointment($details, $student_id, $pdo)
{
    global $error_message;
    if (!$pdo) return false;

    try {
        $now = new DateTime();

        // Dentro de la función schedule_appointment en tu archivo PHP:
        $expires_at = (new DateTime())->modify('+30 minutes'); // Cambiado a 30 minutos

        // Antes de agendar, verificar si ya tiene una cita activa 
        if (get_current_appointment($student_id, $pdo)) {
            $error_message = "Ya tiene una cita pendiente de pago o validación. Por favor, cancélela primero para agendar una nueva.";
            return false;
        }


        $stmt = $pdo->prepare("
            INSERT INTO appointments (student_session_id, subject, other_subject, date, time, student_name, student_contact, status, created_at, expires_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'PENDING_PAYMENT', ?, ?)
        ");
        $success = $stmt->execute([
            $student_id,
            $details['subject'],
            $details['other_subject'] ?? null,
            $details['date'],
            $details['time'],
            $details['student_name'],
            $details['student_contact'],
            $now->format('Y-m-d H:i:s'),
            $expires_at->format('Y-m-d H:i:s')
        ]);

        if ($success) {
            // Devolver el ID de la nueva cita
            return $pdo->lastInsertId();
        }
        return false;
    } catch (PDOException $e) {
        $error_message = "Error al agendar cita: " . $e->getMessage();
        return false;
    }
}

/**
 * Actualiza el estado o detalles de comprobante/detalle de una cita. (Update - U)
 * Ahora permite actualizar múltiples campos, incluyendo los detalles de la cita.
 */
function update_appointment($id, $updates, $pdo)
{
    global $error_message;
    if (!$pdo) return false;

    $set_clauses = [];
    $execute_params = [];

    // Lista segura de campos actualizables (incluye los campos de edición de detalles)
    $allowed_updates = ['status', 'proof_details', 'expires_at', 'subject', 'other_subject', 'date', 'time', 'student_name', 'student_contact'];

    foreach ($updates as $key => $value) {
        if (in_array($key, $allowed_updates)) {
            $set_clauses[] = "$key = ?";
            // Usar NULL explícito para PDO si el valor es null
            $execute_params[] = $value;
        }
    }

    if (empty($set_clauses)) return false;

    $execute_params[] = $id;

    try {
        $sql = "UPDATE appointments SET " . implode(', ', $set_clauses) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($execute_params);
    } catch (PDOException $e) {
        $error_message = "Error al actualizar cita: " . $e->getMessage();
        return false;
    }
}

/**
 * Elimina permanentemente una cita de la base de datos. (Delete - D)
 */
function delete_appointment($id, $pdo)
{
    global $error_message;
    if (!$pdo) return false;

    try {
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        $error_message = "Error al eliminar cita: " . $e->getMessage();
        return false;
    }
}

// --- 5. Lógica de Enrutamiento y Acciones ---

// Acción de Login de Administrador


// Verificamos si existe el usuario y si la contraseña coincide
// NOTA: Si usas password_hash en el registro, aquí usa password_verify($password, $user['password'])
// Procesar Login de Administrador
if (isset($_POST['action']) && $_POST['action'] === 'admin_login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($db_connected) {
        try {
            // Buscamos el usuario por su email
            $stmt = $pdo->prepare("SELECT * FROM administradores WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificamos si existe el usuario y si la contraseña coincide
            if ($user && $password === $user['password']) {
                // Seteamos las variables de sesión
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_nombre'] = $user['nombre'];
                $_SESSION['admin_rol'] = $user['rol'];

                $is_admin = true;
                // Redirigir al dashboard
                header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . "?view={$VIEWS['ADMIN_DASHBOARD']}");
                exit;
            } else {
                $error_message = "❌ Credenciales incorrectas. Acceso denegado.";
            }
        } catch (PDOException $e) {
            $error_message = "Error en la base de datos: " . $e->getMessage();
        }
    }
}


// Acción de Logout de Administrador
if (isset($_GET['action']) && $_GET['action'] === 'admin_logout') {
    $_SESSION['is_admin'] = false;
    unset($_SESSION['is_admin']);
    $is_admin = false;
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?')); // Redirigir al inicio (schedule view)
    exit;
}

// Acción de Agendar Cita (Estudiante)
if (isset($_POST['action']) && $_POST['action'] === 'schedule' && $db_connected) {
    $details = [
        'subject' => $_POST['subject'] ?? '',
        'other_subject' => $_POST['other_subject'] ?? null,
        'date' => $_POST['date'] ?? '',
        'time' => $_POST['time'] ?? '',
        'student_name' => $_POST['student_name'] ?? '',
        'student_contact' => $_POST['student_contact'] ?? '',
    ];

    // NOTA: La validación de $ASSIGNATURES ya no es estrictamente necesaria aquí si el campo es pre-rellenado
    // Pero mantenemos la validación de campos vacíos.

    if (empty($details['subject']) || empty($details['date']) || empty($details['time']) || empty($details['student_name']) || empty($details['student_contact'])) {
        $error_message = "Por favor, complete todos los campos obligatorios.";
    } else {
        // Validación extra para "Otro tipo de asesorías"
        if ($details['subject'] === 'Otro tipo de asesorías' && empty($details['other_subject'])) {
            $error_message = "Debe especificar el tipo de asesoría en el campo 'Otro'.";
        } else {
            $new_id = schedule_appointment($details, $student_session_id, $pdo);
            if ($new_id) {
                // Si la cita se agenda con éxito, redirigimos a la vista de pago
                header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . "?view={$VIEWS['PAYMENT_VIEW']}");
                exit;
            }
        }
    }
}

// Acción de Subir Comprobante (Estudiante)
if (isset($_POST['action']) && $_POST['action'] === 'upload_proof' && $db_connected) {
    $appointment_id = $_POST['appointment_id'] ?? 0;
    $proof_details = $_POST['proof_details'] ?? '';

    if (empty($proof_details)) {
        $error_message = "Debe ingresar detalles del comprobante.";
    } elseif ($appointment_id > 0) {
        $updates = [
            'status' => 'PENDING_VALIDATION',
            'proof_details' => $proof_details,
            // Eliminamos la fecha de expiración ya que el pago fue intentado
            'expires_at' => null // Usar NULL en PDO para campos DATETIME
        ];
        if (!update_appointment($appointment_id, $updates, $pdo)) {
            $error_message = "Fallo al actualizar el comprobante.";
        } else {
            // Recargar la página para mostrar el nuevo estado
            header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . "?view={$VIEWS['PAYMENT_VIEW']}");
            exit;
        }
    }
}
// Busca esta línea (aprox 235) y verifica que diga:
if (isset($_POST['action']) && $_POST['action'] === 'admin_edit' && $is_admin && $db_connected) {
    // ... tu código ...
}

// Busca esta línea (aprox 266) y verifica que diga:
if (isset($_POST['action']) && $_POST['action'] === 'admin_delete' && $is_admin && $db_connected) {
    // ... tu código ...
}

// Busca esta línea (aprox 292) y verifica que diga:
if (isset($_POST['action']) && $_POST['action'] === 'update_status' && $is_admin && $db_connected) {
    // ... tu código ...
}
// ACCIÓN NUEVA: Edición de Cita (Administrador)
if (isset($_POST['action']) && $_POST['action'] === 'admin_edit' && $is_admin && $db_connected) {
    $appointment_id = $_POST['appointment_id'] ?? 0;
    $filter_to_return = $_POST['filter'] ?? 'ALL'; // Para mantener el filtro después de la acción
    $details = [
        'subject' => $_POST['subject'] ?? '',
        'other_subject' => $_POST['other_subject'] ?? null,
        'date' => $_POST['date'] ?? '',
        'time' => $_POST['time'] ?? '',
        'student_name' => $_POST['student_name'] ?? '',
        'student_contact' => $_POST['student_contact'] ?? '',
    ];

    if ($appointment_id > 0 && !empty($details['subject']) && !empty($details['date'])) {
        // Validación extra para "Otro tipo de asesorías"
        if ($details['subject'] !== 'Otro tipo de asesorías') {
            $details['other_subject'] = null; // Limpiar si no aplica
        } elseif (empty($details['other_subject'])) {
            $error_message = "Debe especificar el tipo de asesoría en el campo 'Otro' al editar.";
            goto end_edit;
        }

        if (!update_appointment($appointment_id, $details, $pdo)) {
            $error_message = "Fallo al actualizar la cita ID: $appointment_id.";
        } else {
            // Éxito: Redirigir al dashboard
            header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . "?view={$VIEWS['ADMIN_DASHBOARD']}&filter={$filter_to_return}");
            exit;
        }
    } else {
        $error_message = "Faltan datos obligatorios para editar la cita.";
    }
    end_edit: // Etiqueta para manejar la redirección/continuación en caso de error
}


// ACCIÓN NUEVA: Eliminación de Cita (Administrador - Hard Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'admin_delete') {
    $appointment_id = $_POST['appointment_id'];
    
    // 1. Borramos la cita seleccionada
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    
    if ($stmt->execute([$appointment_id])) {
        // 2. REORDENAR TODOS LOS IDs (Para que no queden huecos)
        // Esto pone una variable en 0 y la va sumando fila por fila
        $pdo->query("SET @count = 0;");
        $pdo->query("UPDATE appointments SET id = (@count := @count + 1);");
        
        // 3. RESETEAR EL AUTO_INCREMENT
        // Para que la siguiente cita nueva use el número que sigue al último
        $pdo->query("ALTER TABLE appointments AUTO_INCREMENT = 1;");

        header("Location: " . $_SERVER['PHP_SELF'] . "?filter=" . ($_GET['filter'] ?? 'ALL') . "&msg=deleted");
        exit();
    }
}


// ACCIÓN: Cancelación por parte del estudiante
if (isset($_POST['action']) && $_POST['action'] === 'student_cancel' && $db_connected) {
    $appointment_id = $_POST['appointment_id'] ?? 0;

    // Solo permitir cancelar si tienen el ID de la cita
    if ($appointment_id > 0) {
        $updates = [
            'status' => 'CANCELLED',
            'expires_at' => null
        ];

        // En un entorno real, se debería verificar que student_session_id coincide con la cita
        if (!update_appointment($appointment_id, $updates, $pdo)) {
            $error_message = "Fallo al cancelar la cita.";
        } else {
            // Redirigir al inicio para que puedan agendar una nueva
            header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }
    }
}


// Acción de Actualizar Estado (Admin) - Usado para Aprobar Pago o Cancelación.
if (isset($_POST['action']) && $_POST['action'] === 'update_status' && $is_admin && $db_connected) {
    $appointment_id = $_POST['appointment_id'] ?? 0;
    $new_status = $_POST['status'] ?? '';
    $filter_to_return = $_POST['filter'] ?? 'ALL'; // Para mantener el filtro después de la acción

    if ($appointment_id > 0 && in_array($new_status, ['PAID', 'CANCELLED'])) {
        $updates = ['status' => $new_status];

        // Si se confirma o cancela, eliminamos la fecha de expiración
        $updates['expires_at'] = null; // Usar NULL en PDO para campos DATETIME

        // También quitamos el comprobante si se cancela, aunque no es estrictamente necesario en este demo
        if ($new_status === 'CANCELLED') {
            $updates['proof_details'] = null;
        }

        if (!update_appointment($appointment_id, $updates, $pdo)) {
            $error_message = "Fallo al actualizar el estado.";
        } else {
            // Redirigir al dashboard para ver el cambio
            header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . "?view={$VIEWS['ADMIN_DASHBOARD']}&filter={$filter_to_return}");
            exit;
        }
    }
}


// --- 6. Determinación de la Vista a Renderizar ---

$view_param = $_GET['view'] ?? $VIEWS['SCHEDULE_VIEW'];

if ($is_admin) {
    $current_view = $VIEWS['ADMIN_DASHBOARD'];
} else {
    // Buscar si el estudiante tiene una cita activa
    $current_appointment = get_current_appointment($student_session_id, $pdo);

    if ($current_appointment) {
        // Si hay cita activa (pendiente de pago o validación), forzar a la vista de pago
        $current_view = $VIEWS['PAYMENT_VIEW'];
    } elseif ($view_param === $VIEWS['ADMIN_LOGIN']) {
        $current_view = $VIEWS['ADMIN_LOGIN'];
    } else {
        $current_view = $VIEWS['SCHEDULE_VIEW'];
    }
}


// --- 7. Datos para el Slider Show ---
$SLIDER_IMAGES = [
    [
        'url' => 'r3.jpg',
        'title' => 'Domina las Matemáticas y las Ciencias',
        'caption' => 'Refuerza Matemáticas, Física y Química con docentes expertos y acompañamiento personalizado.',
        'cta' => 'Agenda tu asesoría',
        'color' => 'bg-gradient-to-r from-indigo-600 to-indigo-500'
    ],
    [
        'url' => 'r2.jpg',
        'title' => 'Prepárate para tu examen con confianza',
        'caption' => 'Te ayudamos a comprender, practicar y aprobar con éxito ese examen tan importante.',
        'cta' => 'Comienza ahora',
        'color' => 'bg-gradient-to-r from-emerald-600 to-green-500'
    ],
    [
        'url' => 'r1.jpg',
        'title' => 'Mejora tu Lectura e Inglés',
        'caption' => 'Fortalece tu comprensión lectora e inglés de forma práctica, clara y efectiva.',
        'cta' => 'Quiero mejorar',
        'color' => 'bg-gradient-to-r from-rose-600 to-red-500'
    ],
];


// --- 8. Función para Renderizar el Slider Show ---
/**
 * Renders the HTML markup for the testimonial slider show.
 */
function render_slider_show($images)
{
    if (empty($images)) return;
?>
    <div id="slider-container" class="max-w-4xl mx-auto mb-10 relative overflow-hidden rounded-xl shadow-2xl">
        <div id="slider-track" class="flex transition-transform duration-500 ease-in-out">
            <?php foreach ($images as $index => $image): ?>
                <div class="slider-item flex-shrink-0 w-full aspect-video md:aspect-[21/9] relative" data-index="<?php echo $index; ?>">

                    <img src="<?php echo htmlspecialchars($image['url']); ?>"
                        alt="<?php echo htmlspecialchars($image['caption']); ?>"
                        class="absolute inset-0 w-full h-full object-cover opacity-70">
                    <img src="..." class="absolute inset-0 w-full h-full object-cover opacity-70">

                    <div class="absolute inset-0 <?php echo htmlspecialchars($image['color']); ?> opacity-70"></div>

                    <div class="relative p-6 md:p-12 h-full flex flex-col justify-center items-center text-center">
                        <h3 class="text-xl md:text-3xl font-extrabold text-white drop-shadow-lg leading-tight">
                            <?php echo htmlspecialchars($image['caption']); ?>
                        </h3>
                        <p class="mt-2 text-sm md:text-lg text-gray-200 font-medium">¡Agenda tu sesión ahora!</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Flechas de navegación -->
        <button onclick="changeSlide(-1)" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-black bg-opacity-30 hover:bg-opacity-50 text-white p-3 rounded-full z-10 transition duration-300 hidden md:block" aria-label="Anterior">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button onclick="changeSlide(1)" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-black bg-opacity-30 hover:bg-opacity-50 text-white p-3 rounded-full z-10 transition duration-300 hidden md:block" aria-label="Siguiente">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <!-- Puntos de navegación -->
        <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
            <?php for ($i = 0; $i < count($images); $i++): ?>
                <button onclick="goToSlide(<?php echo $i; ?>)" class="dot w-3 h-3 rounded-full bg-white bg-opacity-50 hover:bg-opacity-100 transition duration-300" data-slide-index="<?php echo $i; ?>"></button>
            <?php endfor; ?>
        </div>
    </div>
<?php
}

// --- 9. Función para Renderizar las Tarjetas de Asignaturas ---
/**
 * Renders the interactive subject cards.
 */
function render_subject_cards($cards)
{
?>
    <style>
        /* Contenedor con perspectiva para efecto 3D */
        .grid-cards {
            perspective: 1000px;
        }

        /* Estilo base de la tarjeta: Efecto Cristal 3D */
        .card-3d-vibrant {
            position: relative;
            border-radius: 20px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 150px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            text-decoration: none !important;
        }

        /* Brillo interno superior para simular luz */
        .card-3d-vibrant::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 50%;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0) 100%);
            pointer-events: none;
        }

        /* Mapeo de Colores Vibrantes (Se activa según la clase border- que ya tienes) */
        .vibrant-blue {
            background: linear-gradient(135deg, #00c6ff, #44f54dff);
            border-bottom: 6px solid #38f00aff;
        }

        .vibrant-purple {
            background: linear-gradient(135deg, #834d9b, #d04ed6);
            border-bottom: 6px solid #8e24aa;
        }

        .vibrant-pink {
            background: linear-gradient(135deg, #ff0080, #ff8c00);
            border-bottom: 6px solid #f30aa5ff;
        }

        .vibrant-green {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            border-bottom: 6px solid #1b5e20;
        }

        .vibrant-orange {
            background: linear-gradient(135deg, #f2994a, #f2c94c);
            border-bottom: 6px solid #e65100;
        }

        .vibrant-red {
            background: linear-gradient(135deg, #eb3349, #f45c43);
            border-bottom: 6px solid #ccee0aff;
        }

        /* Comportamiento en Android al tocar (Feedback táctil) */
        .card-3d-vibrant:active {
            transform: scale(0.92) translateY(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            filter: brightness(1.2);
        }

        /* Iconos flotantes con sombra */
        .icon-container {
            font-size: 3rem;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
            margin-bottom: 10px;
            transition: transform 0.3s ease;
        }
    </style>

    <div class="max-w-7xl mx-auto mb-10 px-4">
        <h2 class="text-3xl md:text-5xl font-black text-white drop-shadow-[0_4px_8px_rgba(0,0,0,0.5)] mb-10 text-center uppercase tracking-tighter">
            ✨ Paso 1: Selecciona la Asesoría que Necesitas
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-8 grid-cards">
            <?php
            // Detectamos el color que ya trae tu base de datos y lo convertimos a "Vibrante"
            foreach ($cards as $card):
                $color_class = htmlspecialchars($card['color']);
                $vibrant_class = 'vibrant-blue'; // Default

                if (strpos($color_class, 'blue') !== false) $vibrant_class = 'vibrant-blue';
                elseif (strpos($color_class, 'purple') !== false) $vibrant_class = 'vibrant-purple';
                elseif (strpos($color_class, 'pink') !== false) $vibrant_class = 'vibrant-pink';
                elseif (strpos($color_class, 'green') !== false) $vibrant_class = 'vibrant-green';
                elseif (strpos($color_class, 'orange') !== false) $vibrant_class = 'vibrant-orange';
                elseif (strpos($color_class, 'red') !== false) $vibrant_class = 'vibrant-red';
            ?>
                <div class="card-3d-vibrant cursor-pointer <?php echo $vibrant_class; ?>"
                    data-subject="<?php echo htmlspecialchars($card['subject']); ?>"
                    onclick="selectSubject('<?php echo htmlspecialchars($card['subject']); ?>')">

                    <div class="icon-container">
                        <?php echo $card['icon']; ?>
                    </div>

                    <p class="text-white text-sm md:text-lg font-black text-center leading-tight drop-shadow-lg uppercase italic">
                        <?php echo htmlspecialchars($card['subject']); ?>
                    </p>

                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-white/20 rounded-full blur-xl"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php
}

?>
<!DOCTYPE html>
<html lang="es">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>


<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kiosco de Asesorías</title>
    <!-- Incluir Tailwind CSS CDN para estilos -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Estilo base */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        .transition-colors {
            transition: background-color 0.3s, color 0.3s;
        }

        .shadow-glow {
            box-shadow: 0 0 15px rgba(56, 228, 14, 0.5);
        }

        /* Elimina el foco feo en botones */
        button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.5);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        /* Estilos para el modal */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
        }

        /* Estilo para que el Comprobante se muestre bien */
        .proof-details-cell {
            white-space: normal;
            word-break: break-all;
            max-width: 250px;
        }

        /* Estilo para el slider */
        .slider-item {
            width: 100%;
        }

        /* AÑADIDO: Estilo para que el fondo de la vista de agendar ocupe toda la pantalla */
        /* Reset CORRECTO */
        /* Reset limpio y seguro */

        /* Reset correcto */
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: auto;
            min-height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            touch-action: manipulation;
        }

     .schedule-bg {
    /* Fondo con degradado y la imagen r2.jpg */
    background: 
        linear-gradient(rgba(10, 35, 90, 0.55), rgba(10, 35, 90, 0.65)),
        url("r2.jpg") no-repeat center center;
    
    /* 1. ESTO ES CLAVE: cover asegura que nunca queden bordes blancos */
    background-size: cover;

    /* 2. SOLUCIÓN PARA TABLET ANDROID: */
    /* Usamos svh (small viewport height) para ignorar la barra de navegación */
    min-height: 100svh; 
    
    /* 3. Evita que el fondo se mueva si el teclado de Android se abre */
    height: auto;
    width: 100%;

    /* 4. IMPORTANTE: En móviles y tablets el 'fixed' no funciona bien */
    /* 'scroll' permite que el fondo fluya con el contenido de la tabla */
    background-attachment: scroll;

    display: flex;
    flex-direction: column;
    position: relative;
}

/* Ajuste fino para cuando la Tablet está en horizontal */
@media (orientation: landscape) {
    .schedule-bg {
        background-position: center center;
        background-size: 100% 100%; /* Estira solo en horizontal si es necesario */
        background-size: cover; /* Mantiene la proporción */
    }
}
   
        @media (max-width: 480px) {
            .grid-cols-2 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
                /* Una sola columna en celulares pequeños */
            }

            .subject-card {
                padding: 1rem;
            }

            .text-4xl {
                font-size: 1.75rem !important;
                /* Títulos más pequeños en celular */
            }
        }

        @media (orientation: landscape) {
            .schedule-bg {
                background-position: center top;
            }
        }

        @media (orientation: portrait) {
            .schedule-bg {
                background-position: center;
            }
        }

        /* Asegurar que las imágenes y sliders no se desborden */
        img,
        svg {
            max-width: 100%;
            height: auto;
        }

        /* Hacer los bordes más suaves en pantallas táctiles */
        .rounded-xl {
            border-radius: 1rem;
        }

        /* Evitar que el texto largo rompa el diseño en tablets */
        .text-4xl {
            font-size: clamp(1.5rem, 5vw, 2.5rem);
            /* Tamaño fluido */
        }
    </style>
</head>

<body class="min-h-screen">

    <?php if (!$is_admin && $current_view !== $VIEWS['ADMIN_LOGIN']): // Botón de acceso Admin flotante 
    ?>
        <a href="?view=<?php echo $VIEWS['ADMIN_LOGIN']; ?>"
            class="fixed top-4 right-4 md:top-8 md:right-8 bg-purple-600 text-white p-3 rounded-full shadow-xl hover:bg-purple-700 transition duration-300 z-50 transform hover:scale-105"
            aria-label="Acceso Administrador"
            title="Acceso Administrador">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </a>
    <?php elseif ($is_admin): // Botón de Logout Admin flotante 
    ?>
        <a href="?action=admin_logout"
            class="fixed top-4 right-4 md:top-8 md:right-8 bg-red-600 text-white p-3 rounded-full shadow-xl hover:bg-red-700 transition duration-
            
            
            
            z-50 transform hover:scale-105"
            aria-label="Cerrar Sesión Administrador"
            title="Cerrar Sesión">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
        </a>
    <?php endif; ?>

    <div class="p-4 md:p-8">
        <?php if (isset($error_message) && $error_message): ?>
            <div class="max-w-4xl mx-auto bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md" role="alert">
                <p class="font-bold">Error del Sistema / Permisos:</p>
                <p><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        <?php endif; ?>

        <?php
        // ===============================================
        // VISTA: Agendar Asesoría (Estudiante)
        // ===============================================
        if ($current_view === $VIEWS['SCHEDULE_VIEW']): ?>
            <!-- CONTENEDOR PRINCIPAL CON FONDO Y OVERLAY -->
            <div class="schedule-bg min-h-screen relative pt-16 pb-16">
                <!-- Overlay para mejorar legibilidad del texto y el formulario -->
                <div class="absolute inset-0 bg-black/50 z-0"></div>

                <div class="relative z-10">
                    <!-- Contenedor del Logo y Título -->
                    <div class="max-w-4xl mx-auto mb-8 text-center">
                        <img src="logo.avif"
                            alt="Logo del Kiosco de Asesorías"
                            class="mx-auto h-12 md:h-16 mb-4 rounded-lg shadow-lg"
                            style="filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.4));">
                        <h1 class="text-4xl font-extrabold text-white drop-shadow-lg">Kiosco de Asesorías Académicas Los Profes</h1>
                    </div>

                    <?php render_slider_show($SLIDER_IMAGES); // Integración del Slider 
                    ?>

                    <!-- NUEVO: Renderiza las tarjetas interactivas -->
                    <?php render_subject_cards($ASSIGNATURE_CARDS); ?>

                    <!-- NUEVO: Contenedor del Formulario que se muestra al hacer clic -->
                    <div id="schedule_form_container" class="flex items-center justify-center pt-8" style="display: none;">
                        <!-- El formulario usa un fondo blanco semi-transparente y un ligero desenfoque -->
                        <div class="p-4 md:p-8 bg-white/90 backdrop-blur-sm shadow-2xl rounded-xl max-w-lg mx-auto w-full border-t-4 border-indigo-600">
                            <h2 class="text-3xl font-extrabold text-indigo-700 mb-6 border-b pb-2">
                                <span id="selected_subject_title">Agendar Asesoría</span>
                                <span class="text-sm font-normal text-gray-500 float-right mt-2 cursor-pointer hover:text-indigo-600" onclick="hideForm()">
                                    (Cambiar Asignatura)
                                </span>
                            </h2>
                            <form method="POST" action="" class="space-y-4">
                                <input type="hidden" name="action" value="schedule">

                                <!-- CAMPO DE ASIGNATURA ESCONDIDO Y PRE-RELLENADO -->
                                <div>
                                    <label for="subject_display" class="block text-sm font-medium text-gray-700">Asignatura Seleccionada</label>
                                    <div id="subject_display" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 p-2 font-semibold text-gray-800">
                                        <!-- Aquí se mostrará la asignatura seleccionada -->
                                    </div>
                                    <!-- Este es el campo que se envía, debe ser el mismo ID usado en el JS -->
                                    <input type="hidden" id="subject" name="subject" value="" required>
                                </div>

                                <!-- CAMPO CONDICIONAL PARA OTRA ASIGNATURA -->
                                <div id="other_subject_container" style="display:none;" class="mt-2">
                                    <label for="other_subject" class="block text-sm font-medium text-gray-700">Especifique otra Asesoría</label>
                                    <input type="text" id="other_subject" name="other_subject"
                                        placeholder="Ej: Análisis de Datos, Dibujo Técnico..."
                                        value="<?php echo htmlspecialchars($_POST['other_subject'] ?? ''); ?>"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                                </div>

                                <div class="flex space-x-4">
                                    <div class="flex-1">
                                        <label for="date" class="block text-sm font-medium text-gray-700">Fecha</label>
                                        <input type="date" id="date" name="date"
                                            value="<?php echo htmlspecialchars($_POST['date'] ?? date('Y-m-d')); ?>"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                                            required>
                                    </div>
                                    <div class="flex-1">
                                        <label for="time" class="block text-sm font-medium text-gray-700">Hora</label>
                                        <input type="time" id="time" name="time"
                                            value="<?php echo htmlspecialchars($_POST['time'] ?? '10:00'); ?>"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                                            required>
                                    </div>
                                </div>

                                <div>
                                    <label for="student_name" class="block text-sm font-medium text-gray-700">Nombre del Estudiante</label>
                                    <input type="text" id="student_name" name="student_name"
                                        value="<?php echo htmlspecialchars($_POST['student_name'] ?? ''); ?>"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                                        required>
                                </div>

                                <div>
                                    <label for="student_contact" class="block text-sm font-medium text-gray-700">Teléfono de Contacto</label>
                                    <input type="text" id="student_contact" name="student_contact"
                                        value="<?php echo htmlspecialchars($_POST['student_contact'] ?? ''); ?>"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                                        required>
                                </div>

                                <button type="submit"
                                    class="w-full py-3 px-4 border border-transparent rounded-lg shadow-lg text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-[1.01] hover:shadow-indigo-300">
                                    Confirmar Agendamiento
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- Fin Contenedor Formulario -->

                </div>
            </div>

        <?php
        // ===============================================
        // VISTA: Pago (Estudiante)
        // [CONTENIDO DEL RESTO DE VISTAS NO MODIFICADO PARA BREVEDAD, PERO INCLUIDO EN EL ARCHIVO FINAL]
        // ===============================================
        elseif ($current_view === $VIEWS['PAYMENT_VIEW'] && $current_appointment):
            $status = $current_appointment['status'];
            // $current_appointment['expires_at'] es un objeto DateTime o null
            $expires_at_timestamp = $current_appointment['expires_at'] ? $current_appointment['expires_at']->getTimestamp() * 1000 : 0;
            $is_payment_pending = $status === 'PENDING_PAYMENT';
            $is_cancellable = in_array($status, ['PENDING_PAYMENT', 'PENDING_VALIDATION']);

            $status_styles = [
                'PENDING_PAYMENT' => ['bg' => 'bg-yellow-100 text-yellow-800', 'message' => 'Pendiente de Pago'],
                'PENDING_VALIDATION' => ['bg' => 'bg-blue-100 text-blue-800', 'message' => 'Pendiente de Validación por el Administrador'],
                'PAID' => ['bg' => 'bg-green-100 text-green-800', 'message' => 'Pagada y Confirmada'],
                'CANCELLED' => ['bg' => 'bg-red-100 text-red-800', 'message' => 'Cancelada Automáticamente'],
            ];
            $style = $status_styles[$status] ?? ['bg' => 'bg-gray-100 text-gray-800', 'message' => 'Estado Desconocido'];
        ?>
            <div class="bg-gray-50 min-h-[80vh] flex items-center justify-center">
                <div class="p-4 md:p-8 bg-white shadow-2xl rounded-xl max-w-xl mx-auto w-full border-t-8 border-indigo-600">
                    <div class="flex justify-between items-center mb-6 border-b pb-2">
                        <h2 class="text-3xl font-extrabold text-indigo-700">Estado de su Reserva</h2>
                        <?php if (!$is_cancellable): // Si no está pendiente, se asume que está confirmada o fue cancelada/expiró, permitiendo reagendar 
                        ?>
                            <!-- El link sutil sigue disponible arriba -->
                            <a href="?view=<?php echo $VIEWS['SCHEDULE_VIEW']; ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                                Agendar nueva cita
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="p-4 rounded-lg text-center font-bold mb-6 <?php echo $style['bg']; ?>">
                        Estado de su Agendamiento: <?php echo $style['message']; ?>
                    </div>

                    <?php if ($status === 'PAID' || $status === 'CANCELLED'): ?>
                        <!-- Botón destacado de Agendar Nueva Cita cuando la actual está resuelta -->
                        <div class="mb-6 text-center">
                            <a href="?view=<?php echo $VIEWS['SCHEDULE_VIEW']; ?>"
                                class="inline-block py-3 px-6 rounded-lg shadow-xl text-base font-bold text-indigo-600 border-2 border-indigo-600 hover:bg-indigo-50 transition duration-150 ease-in-out transform hover:scale-[1.01] hover:shadow-indigo-300">
                                Agendar Nueva Cita
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($is_payment_pending): ?>
                        <div class="text-center mb-6">
                            <p class="text-lg font-semibold text-gray-800">Su reserva está en espera.</p>
                            <p class="text-xl font-bold text-red-600 mt-2">
                                Tiempo Restante para Pago: <span id="timer-display" class="text-4xl">05:00</span>
                            </p>
                            <p class="text-sm text-gray-500 mt-1">Si el temporizador llega a 0:00, su cita será cancelada automáticamente (refresque la página).</p>
                        </div>
                    <?php endif; ?>

                    <!-- Detalles de la cita -->
                    <div class="mb-6 space-y-2 text-gray-700">
                        <h3 class="text-2xl font-bold border-b pb-1 text-indigo-600">Detalles de la Cita</h3>
                        <p><strong>Estudiante:</strong> <?php echo htmlspecialchars($current_appointment['student_name']); ?></p>
                        <p><strong>Asignatura:</strong> <?php echo htmlspecialchars($current_appointment['subject']); ?>
                            <?php echo $current_appointment['other_subject'] ? "({$current_appointment['other_subject']})" : ''; ?>
                        </p>
                        <p><strong>Fecha/Hora:</strong> <?php echo htmlspecialchars($current_appointment['date']); ?> a las <?php echo htmlspecialchars($current_appointment['time']); ?></p>

                        <?php if ($current_appointment['proof_details']): ?>
                            <div class="mt-4 p-3 bg-gray-100 rounded-lg">
                                <p class="font-bold text-sm text-gray-900 mb-1">Comprobante Enviado:</p>
                                <p class="text-sm italic text-gray-600 break-words"><?php echo nl2br(htmlspecialchars($current_appointment['proof_details'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($is_payment_pending): ?>
                        <div class="border p-6 rounded-lg shadow-inner bg-yellow-50 mb-6">
                            <h3 class="text-xl font-bold text-yellow-700 mb-4">Información de Pago (Asesoría: $35.000 COP)</h3>
                            <p class="text-gray-700 mb-3">Realice la transferencia a:</p>
                            <ul class="list-disc list-inside space-y-1 ml-4 text-sm text-gray-800 font-semibold">

                                <li><strong>Nequi:</strong> 316 669 2913</li>
                            </ul>
                            <p class="text-sm text-red-500 mt-3">¡Importante! La cita se reservará por una Hora.</p>
                        </div>
                        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl text-center">
                            <p class="text-green-800 font-medium mb-3">¡Casi listo! Envía tu comprobante para confirmar:</p>

                            <a href="https://wa.me/573164876650?text=Hola!%20Acabo%20de%20agendar%20mi%20asesoría.%20Aquí%20adjunto%20mi%20comprobante."
                                target="_blank"
                                class="inline-flex items-center justify-center px-6 py-3 bg-[#25D366] hover:bg-[#20ba5a] text-white font-bold rounded-full transition-all transform hover:scale-105 shadow-lg gap-2">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.148-.669-1.612-.916-2.207-.242-.579-.487-.5-.695-.51-.209-.01-.447-.01-.683-.01-.237 0-.62.09-.943.446-.323.356-1.237 1.21-1.237 2.95 0 1.74 1.267 3.42 1.444 3.655.178.237 2.492 3.803 6.037 5.333.844.364 1.503.582 2.016.745.848.27 1.62.232 2.23.14.68-.103 2.09-.854 2.387-1.68.297-.825.297-1.53.208-1.68-.09-.15-.328-.239-.626-.388z" />
                                </svg>
                                Enviar Comprobante
                            </a>
                        </div>

                        <h3 class="text-xl font-bold text-indigo-700 mb-4">Paso 2: Subir Comprobante de Pago</h3>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="upload_proof">
                            <input type="hidden" name="appointment_id" value="<?php echo $current_appointment['id']; ?>">

                            <textarea name="proof_details" rows="3" placeholder="Ej: Pago realizado desde Nequi #300XXXXX a las 10:02 AM."
                                class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-4" required></textarea>

                            <button type="submit"
                                class="w-full py-3 px-4 rounded-lg shadow-md text-base font-medium text-white bg-green-600 hover:bg-green-700 transition duration-150">
                                Enviar Comprobante para Validación
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if ($is_cancellable): ?>
                        <div class="mt-4 border-t pt-4">
                            <form method="POST" action="" onsubmit="return confirm('¿Está seguro de que desea CANCELAR su cita? Esta acción no se puede deshacer.');">
                                <input type="hidden" name="action" value="student_cancel">
                                <input type="hidden" name="appointment_id" value="<?php echo $current_appointment['id']; ?>">
                                <button type="submit" class="w-full text-center text-sm font-semibold text-red-600 hover:text-red-800 transition duration-150">
                                    Cancelar Cita
                                </button>
                                <a href="?view=<?php echo $VIEWS['SCHEDULE_VIEW']; ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                                    Agendar nueva cita
                                </a>


                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Script para el Timer de Expiración -->
            <script>
                const expiresAtTimestamp = <?php echo $expires_at_timestamp; ?>;
                const timerDisplay = document.getElementById('timer-display');

                function updateTimer() {
                    const now = new Date().getTime();
                    const distance = expiresAtTimestamp - now;

                    if (distance <= 0) {
                        if (timerDisplay) {
                            timerDisplay.textContent = "00:00";
                            // No recargamos inmediatamente, esperamos a que PHP lo haga en el siguiente request
                            // Pero podemos cambiar el estilo para indicar la expiración
                            timerDisplay.closest('.text-center').innerHTML = "<p class='text-xl font-bold text-red-600'>¡Tiempo Expirado! Refresque para cancelar la cita.</p>";
                        }
                        clearInterval(x);
                        return;
                    }

                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    const displayMinutes = String(minutes).padStart(2, '0');
                    const displaySeconds = String(seconds).padStart(2, '0');

                    if (timerDisplay) {
                        timerDisplay.textContent = displayMinutes + ":" + displaySeconds;
                    }
                }

                if (expiresAtTimestamp > 0 && timerDisplay) {
                    // Actualizar el contador cada segundo
                    var x = setInterval(updateTimer, 1000);
                    // Ejecutar inmediatamente al cargar
                    updateTimer();
                }
            </script>

        <?php
        // ===============================================
        // VISTA: Login de Administrador
        // ===============================================
        elseif ($current_view === $VIEWS['ADMIN_LOGIN']): ?>
            <div class="min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat"
                style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('por.jpg');">

                <div class="p-8 bg-white/95 backdrop-blur-sm shadow-2xl rounded-xl max-w-sm mx-auto w-full border-t-8 border-purple-600">
                    <h2 class="text-3xl font-extrabold text-purple-700 mb-6 border-b pb-2 text-center">Acceso Administrador</h2>

                    <form method="POST" action="" class="space-y-4">
                        <input type="hidden" name="action" value="admin_login">

                        <div>
                            <label for="admin_email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="admin_email" name="email" value=""
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 p-2"
                                required>
                        </div>

                        <div>
                            <label for="admin_password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input type="password" id="admin_password" name="password" value=""
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 p-2"
                                required>
                        </div>

                        <button type="submit"
                            class="w-full py-3 px-4 border border-transparent rounded-lg shadow-lg text-base font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-150 ease-in-out transform hover:scale-[1.01]">
                            Iniciar Sesión
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <a href="?" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">Volver a Agendar Cita</a>
                    </div>
                </div>
            </div>


        <?php
        // ===============================================
        // VISTA: Dashboard de Administrador
        // =ENTA EL CÓDIGO DEL DASHBOARD...
        // ===============================================
        elseif ($current_view === $VIEWS['ADMIN_DASHBOARD'] && $is_admin):
            // Obtener todas las citas
            $all_appointments = get_all_appointments($pdo);
            $filter = $_GET['filter'] ?? 'ALL'; // Filtro por defecto
            $filtered_appointments = [];

            // Nuevo: Calcular conteos para el panel de resumen
            $counts = [
                'ALL' => count($all_appointments),
                'PENDING_PAYMENT' => 0,
                'PENDING_VALIDATION' => 0,
                'PAID' => 0,
                'CANCELLED' => 0,
            ];

            foreach ($all_appointments as $app) {
                if (isset($counts[$app['status']])) {
                    $counts[$app['status']]++;
                }
                // Aplicar el filtro a la lista de la tabla
                if ($filter === 'ALL' || $app['status'] === $filter) {
                    $filtered_appointments[] = $app;
                }
            }

            // Definir estilos y textos de estado
            $status_details = [
                'PENDING_PAYMENT' => ['text' => 'Pendiente Pago', 'bg' => 'bg-yellow-200 text-yellow-800', 'color' => 'yellow'],
                'PENDING_VALIDATION' => ['text' => 'Pendiente Validación', 'bg' => 'bg-blue-200 text-blue-800', 'color' => 'blue'],
                'PAID' => ['text' => 'CONFIRMADO / PAGADA', 'bg' => 'bg-green-200 text-green-800', 'color' => 'green'],
                'CANCELLED' => ['text' => 'Cancelada / Expirada', 'bg' => 'bg-red-200 text-red-800', 'color' => 'red'],
            ];
            $filters = ['ALL' => 'Todas'] + array_map(fn($d) => $d['text'], $status_details);

            // Preparar el Modal de Edición (si se activa)
            $edit_appointment_id = $_GET['edit'] ?? null;
            $appointment_to_edit = null;
            if ($edit_appointment_id && is_numeric($edit_appointment_id)) {
                $appointment_to_edit = get_appointment_by_id($edit_appointment_id, $pdo);
            }
        ?>

            <div class="mt-4 py-3 border-l-4 border-indigo-500 pl-4 bg-gray-50/80 rounded-r-2xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-tr from-indigo-600 to-purple-500 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md">
                            <?php echo strtoupper(substr($_SESSION['admin_nombre'] ?? 'A', 0, 1)); ?>
                        </div>
                        <span class="absolute bottom-0 right-0 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500 border-2 border-white"></span>
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-bold">Sesión activa</p>
                        </div>
                        <p class="text-lg font-semibold text-gray-800 leading-tight">
                            <?php echo htmlspecialchars($_SESSION['admin_nombre'] ?? 'Administrador'); ?>
                        </p>
                        <div class="mt-1">
                            <span class="bg-indigo-100 text-indigo-700 text-[10px] px-2 py-0.5 rounded-full font-black uppercase tracking-wider">
                                <?php echo htmlspecialchars($_SESSION['admin_rol'] ?? 'Invitado'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <h1 class="text-4xl font-extrabold text-gray-900 mb-6 border-b pb-2">Panel de Administración</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-4">








                <div class="flex flex-wrap gap-3">
                    <?php
                    $subs = $pdo->query("SELECT * FROM subjects_list WHERE is_active = 1")->fetchAll();
                    foreach ($subs as $m): ?>
                        <div class="flex items-center gap-2 px-4 py-2 rounded-full border-2 border-gray-100 font-bold text-xs" style="color: <?php echo $m['color_hex']; ?>">
                            <?php echo $m['name']; ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="delete_type" value="subject">
                                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                <button type="submit" class="ml-1 text-gray-300 hover:text-red-500">×</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
    </div>

    <!-- Panel de Conteo de Asesorías -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <?php
            $count_cards = [
                'ALL' => ['title' => 'Total Citas', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'color' => 'bg-indigo-600', 'count' => $counts['ALL']],
                'PENDING_VALIDATION' => ['title' => 'Pendientes Validar', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'bg-blue-600', 'count' => $counts['PENDING_VALIDATION']],
                'PENDING_PAYMENT' => ['title' => 'Pendientes Pago', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'bg-yellow-600', 'count' => $counts['PENDING_PAYMENT']],
                'PAID' => ['title' => 'Pagadas (Conf.)', 'icon' => 'M5 13l4 4L19 7', 'color' => 'bg-green-600', 'count' => $counts['PAID']],
                'CANCELLED' => ['title' => 'Canceladas / Exp.', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'bg-red-600', 'count' => $counts['CANCELLED']],
            ];

            foreach ($count_cards as $key => $card): ?>
            <div class="<?php echo $card['color']; ?> p-6 rounded-2xl text-white shadow-xl flex items-center justify-between transform hover:-translate-y-2 hover:shadow-2xl transition-all duration-300 cursor-pointer group relative overflow-hidden"
                onclick="window.location.href='?view=<?php echo $VIEWS['ADMIN_DASHBOARD']; ?>&filter=<?php echo $key; ?>'">

                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>

                <div class="relative z-10">
                    <p class="text-4xl font-black tracking-tight mb-1">
                        <?php echo $card['count']; ?>
                    </p>
                    <p class="text-xs font-bold uppercase tracking-widest opacity-90 group-hover:opacity-100 transition-opacity">
                        <?php echo $card['title']; ?>
                    </p>
                </div>

                <div class="relative z-10 bg-white/20 p-3 rounded-lg backdrop-blur-sm group-hover:bg-white/30 transition-colors duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="<?php echo $card['icon']; ?>"></path>
                    </svg>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- Fin Panel de Conteo de Asesorías -->


    <!-- Filtros (Se mantiene el bloque de filtros original para la compatibilidad y visibilidad del filtro activo) -->
    <div class="mb-6 flex space-x-3 items-center">
        <span class="font-semibold text-gray-700">Filtrar por Estado:</span>
        <?php foreach ($filters as $status_key => $status_text):
                $isActive = $filter === $status_key;
                $button_class = $isActive
                    ? "bg-indigo-600 text-white font-bold"
                    : "bg-gray-200 text-gray-700 hover:bg-gray-300";
        ?>
            <a href="?view=<?php echo $VIEWS['ADMIN_DASHBOARD']; ?>&filter=<?php echo $status_key; ?>"
                class="py-2 px-4 rounded-lg text-sm transition-colors <?php echo $button_class; ?>">
                <?php echo $status_text; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 bg-gradient-to-r from-white to-gray-50 p-4 md:p-6 shadow-md rounded-2xl border border-gray-100 overflow-hidden relative">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-50 rounded-full opacity-50"></div>

        <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6 relative z-10">
            <div class="flex-shrink-0 bg-white p-2 rounded-xl shadow-sm border border-gray-100">
                <img src="logo2.png" alt="Logo" class="h-16 md:h-20 w-auto object-contain">
            </div>
            <div class="text-center md:text-left">
                <h1 class="text-xl md:text-2xl font-black text-gray-800 leading-tight">
                    ¡Bienvenido, <span class="text-indigo-600"><?php echo htmlspecialchars($_SESSION['admin_nombre'] ?? 'Admin'); ?></span>!
                </h1>
                <p class="text-gray-500 text-xs md:text-sm font-medium mt-1">Gestión centralizada de asesorías académicas</p>
            </div>

        </div>
        <div class="mb-6 relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-indigo-500 group-focus-within:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" id="smartSearch"
                placeholder="Buscar por estudiante, asignatura o ID..."
                class="w-full pl-12 pr-4 py-4 bg-white border-2 border-gray-100 rounded-2xl shadow-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all text-gray-700 font-medium placeholder-gray-400"
                onkeyup="filterTable()">
        </div>

    </div>
    <div class="flex justify-end mb-4">
        <button type="button" onclick="descargarInforme()"
            class="w-full md:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Descargar Informe CSV
        </button>

        <script>
            function descargarInforme() {
                // 1. Obtener el texto del buscador inteligente (ajusta el ID si es diferente)
                const buscador = document.getElementById('smartSearch').value;

                // 2. Obtener el filtro de estado actual de la URL (si existe)
                const urlParams = new URLSearchParams(window.location.search);
                const filtroEstado = urlParams.get('filter') || 'ALL';

                // 3. Redirigir enviando ambos filtros
                window.location.href = `?action=export_csv&filter=${filtroEstado}&search=${encodeURIComponent(buscador)}`;
            }
        </script>
    </div>
    <div class="bg-transparent">

        <div class="overflow-x-auto bg-white shadow-2xl shadow-gray-200/50 rounded-[2rem] border border-gray-100 mb-8 scrollbar-hide touch-pan-x">
            <div class="overflow-x-auto pb-4">

                <table class="hidden md:table w-full text-left border-separate border-spacing-y-3">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">ID</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Estudiante</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Asignatura</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Fecha / Hora</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Comprobante</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black uppercase text-gray-400 tracking-widest">Estado</th>
                            <th class="px-6 py-4 text-right text-[10px] font-black uppercase text-gray-400 tracking-widest">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_appointments)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-10 italic text-gray-400">No hay citas registradas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_appointments as $app):
                                $status = $status_details[$app['status']] ?? ['bg' => 'bg-gray-100 text-gray-800', 'text' => $app['status']];
                            ?>
                                <tr class="bg-white hover:bg-gray-50 transition-all shadow-sm rounded-2xl">
                                    <td class="px-6 py-4 font-bold text-gray-400">#<?php echo $app['id']; ?></td>

                                    <td class="px-6 py-4">
                                        <div class="font-black text-gray-800 uppercase text-sm"><?php echo htmlspecialchars($app['student_name']); ?></div>
                                        <div class="text-xs text-indigo-500 font-bold"><?php echo htmlspecialchars($app['student_contact']); ?></div>
                                    </td>

                                    <td class="px-6 py-4 text-sm font-medium text-gray-600">
                                        <?php echo htmlspecialchars($app['subject']); ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-700"><?php echo $app['date']; ?></div>
                                        <div class="text-xs text-gray-400"><?php echo $app['time']; ?></div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($app['date']); ?><br>
                                        <span class="font-semibold"><?php echo htmlspecialchars($app['time']); ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm proof-details-cell">
                                        <?php if (!empty($app['proof_details'])): ?>
                                            <div class="text-xs italic p-1 bg-gray-100 rounded break-words">
                                                <?php echo nl2br(htmlspecialchars($app['proof_details'])); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400">N/A</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-tighter <?php echo $status['bg']; ?>">
                                            <?php echo $status['text']; ?>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-3">

                                            <a href="?view=<?php echo $VIEWS['ADMIN_DASHBOARD']; ?>&filter=<?php echo $filter; ?>&edit=<?php echo $app['id']; ?>"
                                                class="p-3 bg-indigo-100 text-indigo-600 rounded-2xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Editar">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <?php if ($app['status'] !== 'PAID'): ?>
                                                <form method="POST" class="inline" onsubmit="return confirm('¿Confirmar pago y liberar cita manualmente?');">
                                                    <input type="hidden" name="action" value="confirm_payment_manual">
                                                    <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                                    <button type="submit" class="p-3 bg-emerald-500 text-white rounded-2xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100" title="Liberar Cita">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <div class="p-3 text-emerald-600 bg-emerald-50 rounded-2xl border border-emerald-200">
                                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                                    </svg>
                                                </div>
                                            <?php endif; ?>

                                            <form method="POST" class="inline" onsubmit="return confirm('¿Eliminar permanentemente?');">
                                                <input type="hidden" name="action" value="admin_delete">
                                                <input type="hidden" name="appointment_id" value="<?php echo $app['id']; ?>">
                                                <button type="submit" class="p-3 bg-red-100 text-red-500 rounded-2xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="md:hidden space-y-4 px-2">
                    <?php foreach ($filtered_appointments as $app):
                        $status = $status_details[$app['status']] ?? ['bg' => 'bg-gray-100 text-gray-800', 'text' => $app['status']];
                    ?>
                        <div class="bg-white p-6 rounded-[2rem] shadow-lg border border-gray-100 relative overflow-hidden">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">ID #<?php echo $app['id']; ?></span>
                                    <h3 class="font-black text-gray-800 uppercase text-lg leading-tight"><?php echo htmlspecialchars($app['student_name']); ?></h3>
                                    <p class="text-xs text-indigo-500 font-bold"><?php echo htmlspecialchars($app['student_contact']); ?></p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase <?php echo $status['bg']; ?>">
                                    <?php echo $status['text']; ?>
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="bg-gray-50 p-3 rounded-2xl">
                                    <span class="block text-[8px] font-black text-gray-400 uppercase">Asignatura</span>
                                    <span class="text-xs font-bold text-gray-700"><?php echo htmlspecialchars($app['subject']); ?></span>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-2xl">
                                    <span class="block text-[8px] font-black text-gray-400 uppercase">Horario</span>
                                    <span class="text-xs font-bold text-gray-700"><?php echo $app['date']; ?> <br> <?php echo $app['time']; ?></span>
                                </div>
                            </div>

                            <div class="mb-4 bg-indigo-50/30 p-3 rounded-2xl border border-dashed border-indigo-100">
                                <span class="block text-[8px] font-black text-indigo-400 uppercase mb-1">Comprobante</span>
                                <p class="text-[10px] italic text-indigo-900 leading-relaxed"><?php echo !empty($app['proof_details']) ? nl2br(htmlspecialchars($app['proof_details'])) : 'Sin detalles de pago.'; ?></p>
                            </div>

                            <div class="flex gap-2">
                                <a href="?edit=<?php echo $app['id']; ?>" class="flex-1 py-3 bg-gray-100 text-gray-600 text-center rounded-xl font-black text-[10px] uppercase">Editar</a>
                                <?php if ($app['status'] === 'PENDING_VALIDATION'): ?>
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="appointment_id" value="<?php echo $app['id']; ?>">
                                        <input type="hidden" name="status" value="PAID">
                                        <button class="w-full py-3 bg-green-500 text-white rounded-xl font-black text-[10px] uppercase shadow-lg shadow-green-100">Confirmar Pago</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            </table>
        </div>

        <div class="md:hidden space-y-4">
            <?php foreach ($filtered_appointments as $app):
                $status = $status_details[$app['status']] ?? ['bg' => 'bg-gray-100 text-gray-800', 'text' => $app['status']];
            ?>
                <div class="bg-white p-5 rounded-2xl shadow-md border border-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 h-1 w-20 <?php echo strpos($status['bg'], 'green') !== false ? 'bg-green-500' : 'bg-amber-500'; ?>"></div>

                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold mr-3 shadow-lg shadow-indigo-200">
                                <?php echo strtoupper(substr($app['student_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h3 class="font-black text-gray-900 leading-none"><?php echo htmlspecialchars($app['student_name']); ?></h3>
                                <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($app['student_contact']); ?></p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-[9px] font-black rounded-full <?php echo $status['bg']; ?>">
                            <?php echo strtoupper($status['text']); ?>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 py-3 border-y border-gray-50 my-3 text-sm">
                        <div>
                            <p class="text-[10px] uppercase text-gray-400 font-bold">Materia</p>
                            <p class="font-semibold text-gray-700"><?php echo htmlspecialchars($app['subject']); ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase text-gray-400 font-bold">Horario</p>
                            <p class="font-semibold text-gray-700"><?php echo $app['date']; ?> <span class="text-indigo-500"><?php echo $app['time']; ?></span></p>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-4">
                        <a href="?edit=<?php echo $app['id']; ?>" class="flex-1 bg-indigo-50 text-indigo-600 text-center py-2.5 rounded-xl font-bold text-xs">Editar</a>
                        <form method="POST" class="flex-1" onsubmit="return confirm('¿Borrar?');">
                            <input type="hidden" name="action" value="admin_delete">
                            <input type="hidden" name="appointment_id" value="<?php echo $app['id']; ?>">
                            <button class="w-full bg-red-50 text-red-500 py-2.5 rounded-xl font-bold text-xs text-center">Eliminar</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    </div>

    <!-- Modal de Edición de Cita (Admin) -->
    <?php if ($appointment_to_edit): ?>
        <div id="edit-modal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 relative">
                <h3 class="text-2xl font-bold text-indigo-700 mb-4 border-b pb-2">Editar Cita #<?php echo $appointment_to_edit['id']; ?></h3>

                <a href="?view=<?php echo $VIEWS['ADMIN_DASHBOARD']; ?>&filter=<?php echo $filter; ?>"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>

                <form method="POST" action="" id="admin-edit-form" class="space-y-4">
                    <input type="hidden" name="action" value="admin_edit">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment_to_edit['id']; ?>">
                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">

                    <div>
                        <label for="edit_subject" class="block text-sm font-medium text-gray-700">Asignatura</label>
                        <select id="edit_subject" name="subject" onchange="toggleOtherSubjectAdmin(this.value, 'edit')"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                            required>
                            <?php foreach ($ASSIGNATURES as $sub): ?>
                                <option value="<?php echo htmlspecialchars($sub); ?>"
                                    <?php echo ($appointment_to_edit['subject'] === $sub) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($sub); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="edit_other_subject_container" style="<?php echo ($appointment_to_edit['subject'] === 'Otro tipo de asesorías') ? 'display:block;' : 'display:none;'; ?>" class="mt-2">
                            <input type="text" id="edit_other_subject" name="other_subject"
                                placeholder="Especifique la asignatura"
                                value="<?php echo htmlspecialchars($appointment_to_edit['other_subject'] ?? ''); ?>"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label for="edit_date" class="block text-sm font-medium text-gray-700">Fecha</label>
                            <input type="date" id="edit_date" name="date"
                                value="<?php echo htmlspecialchars($appointment_to_edit['date']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                                required>
                        </div>
                        <div class="flex-1">
                            <label for="edit_time" class="block text-sm font-medium text-gray-700">Hora</label>
                            <input type="time" id="edit_time" name="time"
                                value="<?php echo htmlspecialchars($appointment_to_edit['time']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                                required>
                        </div>
                    </div>

                    <div>
                        <label for="edit_student_name" class="block text-sm font-medium text-gray-700">Nombre del Estudiante</label>
                        <input type="text" id="edit_student_name" name="student_name"
                            value="<?php echo htmlspecialchars($appointment_to_edit['student_name']); ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                            required>
                    </div>

                    <div>
                        <label for="edit_student_contact" class="block text-sm font-medium text-gray-700">Email / Teléfono de Contacto</label>
                        <input type="text" id="edit_student_contact" name="student_contact"
                            value="<?php echo htmlspecialchars($appointment_to_edit['student_contact']); ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                            required>
                    </div>

                    <button type="submit"
                        class="w-full py-3 px-4 border border-transparent rounded-lg shadow-lg text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150">
                        Guardar Cambios
                    </button>
                </form>
            </div>
        </div>

        <script>
            // Función específica para el formulario de edición del Admin
            function toggleOtherSubjectAdmin(value, prefix) {
                const container = document.getElementById(`${prefix}_other_subject_container`);
                const input = document.getElementById(`${prefix}_other_subject`);
                if (container && input) {
                    if (value === 'Otro tipo de asesorías') {
                        container.style.display = 'block';
                    } else {
                        container.style.display = 'none';
                    }
                }
            }
        </script>

    <?php endif; ?>

<?php endif; ?>
</div>

<script>
    // Variables y funciones del Slider (reubicadas para que sean globales y usables)
    let currentSlide = 0;
    const totalSlides = <?php echo count($SLIDER_IMAGES); ?>;
    let slideInterval;
    let sliderTrack;
    let dots;
    const slideDuration = 5000; // 5 segundos

    function updateSlider() {
        if (!sliderTrack) return;
        const offset = -currentSlide * 100;
        sliderTrack.style.transform = `translateX(${offset}%)`;

        dots.forEach((dot, index) => {
            if (index === currentSlide) {
                dot.classList.add('bg-opacity-100', 'ring-2', 'ring-white');
                dot.classList.remove('bg-opacity-50');
            } else {
                dot.classList.remove('bg-opacity-100', 'ring-2', 'ring-white');
                dot.classList.add('bg-opacity-50');
            }
        });
    }

    function changeSlide(direction) {
        currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
        updateSlider();
        resetInterval();
    }

    function goToSlide(index) {
        currentSlide = index;
        updateSlider();
        resetInterval();
    }

    function startInterval() {
        slideInterval = setInterval(() => {
            changeSlide(1);
        }, slideDuration);
    }

    function resetInterval() {
        clearInterval(slideInterval);
        startInterval();
    }

    // --- NUEVAS FUNCIONES PARA INTERACCIÓN CON TARJETAS Y FORMULARIO ---

    const formContainer = document.getElementById('schedule_form_container');
    const subjectInput = document.getElementById('subject'); // Hidden input
    const subjectDisplay = document.getElementById('subject_display'); // Display div
    const selectedSubjectTitle = document.getElementById('selected_subject_title');
    const otherSubjectContainer = document.getElementById('other_subject_container');
    const subjectCards = document.querySelectorAll('.subject-card');

    /**
     * Maneja la selección de la asignatura desde la tarjeta.
     */
    function selectSubject(subject) {
        // 1. Ocultar todas las tarjetas y el título de selección
        subjectCards.forEach(card => card.style.display = 'none');

        // 2. Rellenar los campos del formulario
        subjectInput.value = subject;
        subjectDisplay.textContent = subject;
        selectedSubjectTitle.textContent = `Agendar: ${subject}`;

        // 3. Mostrar u ocultar el campo "Otro"
        if (subject === 'Otro tipo de asesorías') {
            otherSubjectContainer.style.display = 'block';
        } else {
            otherSubjectContainer.style.display = 'none';
        }

        // 4. Mostrar el formulario con animación (opcional, pero mejora la UX)
        formContainer.style.display = 'flex';

        // 5. Scroll al formulario
        formContainer.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    /**
     * Oculta el formulario y muestra las tarjetas.
     */
    function hideForm() {
        formContainer.style.display = 'none';
        subjectCards.forEach(card => card.style.display = 'block');
        // Scroll arriba
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }


    // Inicialización al cargar la página
    window.onload = function() {
        // Inicialización del Slider
        sliderTrack = document.getElementById('slider-track');
        dots = document.querySelectorAll('.dot');
        if (sliderTrack) {
            updateSlider();
            startInterval();
        }

        // Lógica para el formulario (si la vista es SCHEDULE_VIEW)
        // Ya no necesitamos toggleOtherSubject en el onload porque lo manejamos en selectSubject

        // Lógica para el formulario de edición del Admin
        const editSubjectSelect = document.getElementById('edit_subject');
        if (editSubjectSelect) toggleOtherSubjectAdmin(editSubjectSelect.value, 'edit');

        // Si hay un error de POST, el formulario debe seguir visible
        const isError = <?php echo isset($error_message) && $error_message ? 'true' : 'false'; ?>;
        const isSchedulePost = <?php echo isset($_POST['action']) && $_POST['action'] === 'schedule' ? 'true' : 'false'; ?>;

        if (isError && isSchedulePost) {
            // Si hubo un error al enviar el formulario, lo mostramos y rellenamos el campo.
            const lastSubject = "<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>";
            if (lastSubject) {
                selectSubject(lastSubject);
                // Ocultar tarjetas de nuevo ya que selectSubject las mostró si no lo estaban
                subjectCards.forEach(card => card.style.display = 'none');
            }
        }

    };
</script>
<?php if (!$is_admin): // Solo se muestra si NO es administrador 
?>
    <div class="fixed bottom-6 right-6 z-[100] flex flex-col items-end">

        <div class="mb-3 bg-white border border-gray-100 px-4 py-2 rounded-2xl shadow-xl animate-bounce hidden md:block">
            <p class="text-xs font-semibold text-gray-600 flex items-center">
                <span class="flex h-2 w-2 mr-2">
                    <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                ¡Logremos tus objetivos académicos juntos! 🚀
            </p>
        </div>

        <a href="https://wa.me/573164876650?text=Hola!%20Vengo%20del%20Kiosco%20de%20Asesorías%20y%20necesito%20apoyo%20con%20una%20materia."
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center justify-center w-16 h-16 bg-[#25D366] rounded-full shadow-[0_10px_25px_rgba(37,211,102,0.4)] hover:shadow-[0_15px_30px_rgba(37,211,102,0.6)] transition-all duration-300 transform hover:scale-110 active:scale-95 group relative">

            <svg class="w-9 h-9 text-white group-hover:rotate-12 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.148-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
            </svg>

            <span class="absolute inset-0 rounded-full bg-green-500 opacity-20 animate-ping"></span>
        </a>
    </div>
<?php endif; ?>
<script>
    function filterTable() {
        const input = document.getElementById("smartSearch");
        const filter = input.value.toLowerCase();

        // 1. Filtrar filas de la tabla (Vista PC)
        const tableRows = document.querySelectorAll("tbody tr");
        tableRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });

        // 2. Filtrar tarjetas (Vista Móvil Android)
        const mobileCards = document.querySelectorAll(".md:hidden .bg-white.p-5");
        mobileCards.forEach(card => {
            const text = card.innerText.toLowerCase();
            // Si no hay resultados, ocultamos la tarjeta con una pequeña animación
            if (text.includes(filter)) {
                card.classList.remove("hidden");
            } else {
                card.classList.add("hidden");
            }
        });

        // Mostrar mensaje si no hay resultados
        const noResults = document.getElementById("no-results-msg");
        const visibleCards = document.querySelectorAll(".md:hidden .bg-white.p-5:not(.hidden)");

        // Si tienes un div para "no resultados", puedes activarlo aquí
    }
</script>

</body>
<div id="modalSlide" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm p-4 flex items-center justify-center">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md p-8 shadow-2xl scale-up-center">
        <h2 class="text-2xl font-black mb-6">Nuevo Banner</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="action" value="add_slide">
            <div class="bg-gray-50 p-6 rounded-3xl border-2 border-dashed border-gray-200 text-center">
                <input type="file" name="slide_image" required class="text-sm">
            </div>
            <input type="text" name="title" placeholder="Título del banner" class="w-full p-4 bg-gray-100 rounded-2xl outline-none font-bold" required>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-lg">GUARDAR</button>
                <button type="button" onclick="this.closest('#modalSlide').classList.add('hidden')" class="flex-1 py-4 bg-gray-100 text-gray-500 font-bold rounded-2xl">Cerrar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Deshabilitar click derecho
    document.addEventListener('contextmenu', event => event.preventDefault());

    // Deshabilitar combinaciones de teclas (F12, Ctrl+U, Ctrl+Shift+I)
    document.onkeydown = function(e) {
        if(e.keyCode == 123) return false; // F12
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) return false;
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) return false;
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) return false;
        if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) return false;
    };
</script>

<style>
    /* Evitar que seleccionen texto con el mouse */
    body {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>
<script>
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

// Detectar mensajes en la URL
const urlParams = new URLSearchParams(window.location.search);

if (urlParams.has('success') || urlParams.get('msg') === 'success') {
    Toast.fire({
        icon: 'success',
        title: '¡Operación exitosa!',
        background: '#ecfdf5',
        color: '#059669'
    });
}

if (urlParams.get('msg') === 'deleted') {
    Swal.fire({
        title: '¡Eliminado!',
        text: 'La cita ha sido borrada y el ID reordenado.',
        icon: 'success',
        confirmButtonColor: '#6366f1',
        showClass: { popup: 'animate__animated animate__fadeInUp' }
    });
}
</script>

</html>