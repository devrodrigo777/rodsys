$(document).ready(function() {
    new DataTable('#datatablesSimple', {
    ajax: window.BURL + 'dashboard/acessos/usuarios/api/list',
    processing: true,
    serverSide: true,
    language: {
        url: 'https://cdn.datatables.net/plug-ins/2.3.5/i18n/pt-BR.json',
    }
    });
    
    $('#datatablesSimple').on("draw.dt", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
});
// window.addEventListener('DOMContentLoaded', event => {
//     // Simple-DataTables
//     // https://github.com/fiduswriter/Simple-DataTables/wiki

//     const datatablesSimple = document.getElementById('datatablesSimple');
//     if (datatablesSimple) {
//         new simpleDatatables.DataTable(datatablesSimple);
//     }
// });
