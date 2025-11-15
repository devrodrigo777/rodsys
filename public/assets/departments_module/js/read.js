function editDepartment(departmentId) {
    window.location.href = window.BURL + 'dashboard/departamentos/' + departmentId;
}

function deleteDepartment(departmentId, departmentName) {

    if(!departmentName)
        departmentName = '';
    else
        departmentName = ' "' + departmentName + '"';


        Swal.fire({
            title: 'Remover Departamento' + departmentName + '?',
            text: "Todos os usuários que possuem este departamento ficarão automaticamente com o departamento 'Nenhum'. Esta ação não pode ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, deletar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {

                // swal.fire comfirmando a deleção. pedindo para aguardar um segundo
                Swal.fire({
                    title: 'Aguarde...',
                    text: 'Deletando departamento...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // ajax request to delete user
                $.ajax({
                    url: window.BURL + 'dashboard/departamentos/' + departmentId,
                    type: 'DELETE',
                    success: function(response){
                        // swal.fire
                        Swal.fire({
                            title: 'Sucesso!',
                            text: 'Departamento deletado com sucesso.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr, status, error){
                        // swal.fire
                        Swal.fire({
                            title: 'Erro!',
                            text: 'Houve um erro ao deletar o departamento: ' + xhr.responseText,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

// Auto-generate description using Gemini API
$(document).ready(function() {
    $('#inputDescricao').on('focus', function() {
        const nome = $('#inputNome').val().trim();
        
        // Only proceed if nome is not empty and description is empty
        if (nome && !$(this).val().trim()) {
            generateDescriptionWithGemini(nome);
        }
    });
});

