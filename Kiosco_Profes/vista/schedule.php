<?php
// VISTA: Agendar Asesoría (Estudiante)

// Este archivo solo contiene el HTML y las funciones de ayuda de la vista.

// --- 1. Datos para el Slider Show (Se mantienen aquí o se mueven a config.php) ---
$SLIDER_IMAGES = [
    // ... (Tu array $SLIDER_IMAGES se mantiene aquí) ...
];

// --- 2. Función para Renderizar el Slider Show (Se mantiene aquí o se mueve a helpers.php) ---
function render_slider_show($images) {
    // ... (Tu función render_slider_show se mantiene aquí) ...
}

// --- 3. Función para Renderizar las Tarjetas de Asignaturas (Se mantiene aquí) ---
function render_subject_cards($cards) {
    // ... (Tu función render_subject_cards se mantiene aquí) ...
}
?>

<div class="schedule-bg min-h-screen relative pt-16 pb-16">
    <div class="absolute inset-0 bg-black/50 z-0"></div> 
    
    <div class="relative z-10">
        <div class="max-w-4xl mx-auto mb-8 text-center">
            </div>

        <?php render_slider_show($SLIDER_IMAGES); ?>

        <?php render_subject_cards($ASSIGNATURE_CARDS); ?>

        <div class="max-w-xl mx-auto bg-white/95 p-8 md:p-10 rounded-xl shadow-2xl border-t-8 border-indigo-600">
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="schedule">
                </form>
        </div>
    </div>
</div>