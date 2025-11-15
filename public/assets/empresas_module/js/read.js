$(document).ready(function() {
    let dataTable = $('#datatablesSimple').DataTable({
        ajax: {
            url: window.BURL + 'empresas/api/list',
            type: 'GET',
            data: function(d) {
                // Verifica se o usuário está digitando CNPJ/CPF (apenas números e pontos)
                if (d.search && d.search.value) {
                    let searchInput = d.search.value;
                    // Permite apenas números e pontos
                    if (/^[0-9.]*$/.test(searchInput)) {
                        // Se contém apenas números e pontos, remove os pontos para a busca
                        d.search.value = searchInput.replace(/\D/g, '');
                    } else {
                        // Se contém outros caracteres, não faz nada (mantém como está)
                        d.search.value = searchInput;
                    }
                }
            }
        },
        processing: true,
        serverSide: true,
        columns: [
            { data: 0 },
            { 
                data: 1,
                render: function(data) {
                    return formatCNPJorCPF(data);
                }
            },
            { data: 2 },
            { data: 3 },
            { data: 4, searchable: false, orderable: false }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.5/i18n/pt-BR.json',
        }
    });
});

function formatCNPJorCPF(value) {
    if (!value) return '';
    
    let cnpj_cpf = value.replace(/\D/g, '');
    
    if (cnpj_cpf.length === 11) {
        // CPF: XXX.XXX.XXX-XX
        return cnpj_cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    } else if (cnpj_cpf.length === 14) {
        // CNPJ: XX.XXX.XXX/XXXX-XX
        return cnpj_cpf.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }
    
    return value;
}

function editEmpresa(id) {
    window.location.href = window.BURL + 'dashboard/empresas/' + id;
}

function deleteEmpresa(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Esta ação não pode ser desfeita!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, deletar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: window.BURL + 'empresas/api/delete/' + id,
                type: 'DELETE',
                success: function(response){
                    Swal.fire({
                        title: 'Sucesso!',
                        text: 'Empresa deletada com sucesso.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function(){
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Houve um erro ao deletar a empresa.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}
