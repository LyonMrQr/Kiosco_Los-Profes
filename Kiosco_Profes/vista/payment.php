<?php
// VISTA: Pago (Estudiante con current_appointment)

if (!$current_appointment) {
    // Si se accede directamente y no hay cita activa, redirigir al inicio
    header('Location: index.php');
    exit;
}

// Lógica para calcular tiempo restante, etc., se ejecuta aquí.
$is_expired = $current_appointment['expires_at'] && $current_appointment['expires_at'] < new DateTime();

// ... (El resto de la lógica de tiempo/estado para esta vista se mantiene) ...

?>

<div class="max-w-2xl mx-auto mt-10 p-8 bg-white rounded-xl shadow-2xl border-t-8 
    <?php echo ($current_appointment['status'] === 'PENDING_VALIDATION') ? 'border-green-500' : 'border-red-500'; ?>">
    
    <h2 class="text-3xl font-bold mb-6 text-gray-800 text-center">
        <?php echo ($current_appointment['status'] === 'PENDING_PAYMENT') ? '🕒 Pago Pendiente' : '✅ Comprobante Enviado (Validación)'; ?>
    </h2>
    </div>