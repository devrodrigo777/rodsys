var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
});

$(document).ready(function() {
    
});

function editUser(userId) {
    window.location.href = window.BURL + 'dashboard/acessos/usuarios/' + userId;
}

function deleteUser(userId) {
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

                // swal.fire comfirmando a deleção. pedindo para aguardar um segundo
                Swal.fire({
                    title: 'Aguarde...',
                    text: 'Deletando usuário...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // ajax request to delete user
                $.ajax({
                    url: window.BURL + 'login/api/delete-user/' + userId,
                    type: 'DELETE',
                    success: function(response){
                        // swal.fire
                        Swal.fire({
                            title: 'Sucesso!',
                            text: 'Usuário deletado com sucesso.',
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
                            text: 'Houve um erro ao deletar o usuário: ' + xhr.responseText,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }


$(document).ready(function(){

    
    $("#user_createnew").click(function(event){
        event.preventDefault();
        let buttonTitle = $(this).html();

        $(this).prop("disabled", true);
        $(this).html('<i class="fa-solid fa-spinner fa-spin-pulse"></i>');

        let formData = {
            usuario: $("#inputNome").val(),
            login: $("#inputLogin").val(),
            password: $("#inputSenha").val(),
            id_empresa: $("#inputEmpresa").val(),
            id_cargo: $("#inputCargo").val()
        };  

        // ajax request to here to create user
        $.ajax({
            url: window.BURL + 'login/api/create-user',
            type: 'POST',
            data: formData,
            success: function(response){
                // swal.fire
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Usuário criado com sucesso.',
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
                    text: 'Houve um erro ao criar o usuário: ' + xhr.responseText,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            },
            complete: function(){
                $("#user_createnew").prop("disabled", false);
                $("#user_createnew").html(buttonTitle);
            }
        });
        
    });
});