<main>
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-fluid px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="user"></i></div>
                            Gerenciador de Usuários
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="user-management-groups-list.html">
                            <i class="me-1" data-feather="users"></i>
                            Gerenciar Cargos
                        </a>
                        <?php if($permissoes->user_has_permission('user.create')): ?>
                        <button class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="me-1" data-feather="user-plus"></i>
                            Criar Usuário
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card">
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Cargo</th>
                            <th>Empresa</th>
                            <th>Ações</th>  
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</main>

<?php if($permissoes->user_has_permission('user.create')): ?>
<!-- Modal para criar novo usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Criar Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                            <!-- Form Row-->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (first name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputNome">Nome</label>
                                    <input class="form-control" id="inputNome" type="text"
                                        required style="text-transform: uppercase;"
                                        placeholder="João de Araujo Neto" value="" />
                                </div>
                                <!-- Form Group (last name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputLogin">Login</label>
                                    <input class="form-control" id="inputLogin" type="text"
                                        required style="text-transform: uppercase;"
                                        placeholder="JOAO.NETO" value="" />
                                </div>
                            </div>
                            <!-- Form Group (email address)-->
                            <div class="mb-3">
                                <label class="small mb-1" for="inputSenha">Senha</label>
                                
                                <input class="form-control" required id="inputSenha" type="password" value="" />
                            </div>
                            
                            <!-- Form Group (Roles)-->
                            <div class="mb-3">
                                <label class="small mb-1">Empresa</label>
                                <select id="inputEmpresa" name="inputEmpresa" class="form-select" required aria-label="Default select example">
                                    <option selected disabled>Selecione:</option>
                                    <?php foreach($lista_empresas as $empresa): ?>
                                        <option value="<?=$empresa['id_empresa']?>"><?=$empresa['id_empresa']?> - <?=$empresa['razao_social']?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Form Group (Roles)-->
                            <div class="mb-3">
                                <label class="small mb-1">Cargo</label>
                                <select id="inputCargo" name="inputCargo" class="form-select" required aria-label="Default select example">
                                    <option selected disabled>Selecione:</option>
                                    <?php foreach($lista_cargos as $cargo): ?>
                                        <option value="<?=$cargo['id_cargo']?>"><?=$cargo['nome']?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Submit button-->
                                <button class="btn btn-primary" id="user_createnew" type="submit">Criar Usuário</button>
                        </form>
            </div>
        </div>
    </div>

<?php
    // Verificar se há o flashdata success para exibir o swal.fire pro usuario
    if(
        session()->getFlashdata('user.feedback.success')):
    
        $message = session()->getFlashdata('user.feedback.success');
    ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            text: '<?= esc($message) ?>',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            didOpen: (toast) => {
                Swal.hideLoading()
            },
            willClose: () => {
                // Optional: reload page or refresh datatable
            }
        });
    });
</script>
<?php endif; ?>

    <?php endif; ?>

