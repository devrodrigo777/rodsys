$(document).ready(function() {
    let dataTable = $('#datatablesSimple').DataTable({
        ajax: {
            url: window.BURL + 'dashboard/modules/api/list',
            type: 'GET',
        },
        processing: true,
        serverSide: true,
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2,searchable: false, orderable: false },
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.5/i18n/pt-BR.json',
        }
    });
});