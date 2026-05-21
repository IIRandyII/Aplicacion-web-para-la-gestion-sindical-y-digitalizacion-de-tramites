// ===============================
// UTILS.JS
// Funciones globales reutilizables
// para cualquier rol / página
// ===============================

/**
 * Abre un modal agregando la clase "activo".
 * @param {string} id - ID del elemento modal en el DOM
 */
function abrirModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add("activo");
}

/**
 * Cierra un modal quitando la clase "activo".
 * @param {string} id - ID del elemento modal en el DOM
 */
function cerrarModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove("activo");
}

// -----------------------------------------------
// Delegación global de cierre de modales:
// - Botones con data-cerrar-modal="idDelModal"
// - Click en el fondo oscuro del modal
// -----------------------------------------------
document.addEventListener("click", (e) => {

    // Botón con data-cerrar-modal
    const btnCerrar = e.target.closest("[data-cerrar-modal]");
    if (btnCerrar) {
        cerrarModal(btnCerrar.dataset.cerrarModal);
        return;
    }

    // Click en el fondo del modal (el overlay, no el contenido)
    if (e.target.classList.contains("activo") && e.target.dataset.modalId) {
        cerrarModal(e.target.dataset.modalId);
    }
});