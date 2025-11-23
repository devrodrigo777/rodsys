$(document).ready(function() {

    let cid = $('#company_id').val();

    let dataTable = $('#datatablesSimple').DataTable({
        ajax: {
            url: window.BURL + 'dashboard/modules/api/list/' + cid,
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
            emptyTable: "Nenhum módulo adquirido.",
            infoEmpty: "Nenhum módulo disponível.",
            zeroRecords: "Nenhum módulo disponível.",
        }
    });
});