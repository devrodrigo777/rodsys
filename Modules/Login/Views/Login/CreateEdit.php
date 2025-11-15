<main>
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="user-plus"></i></div>
                            <?=(!empty($is_editing) ? 'Editar' : 'Novo')?> Usuário
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="<?=base_url("dashboard/acessos/usuarios")?>">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Gerenciador de Usuários
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <div class="row">
            <div class="col-xl-12">
                <!-- Account details card-->
                <div class="card mb-4">
                    <div class="card-header">Informações</div>
                    <div class="card-body">
                        <form method="POST">
                            <?php if (!empty($is_editing)): ?>
                                <input type="hidden" name="is_editing" value="1" />
                                <input type="hidden" name="id_usuario" value="<?= esc($usuario['id_usuario'] ?? '') ?>" />
                            <?php endif; ?>

                            <!-- Form Row-->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (user id) - only when editing-->
                                <?php if (!empty($is_editing) && isset($usuario['id_usuario'])): ?>
                                <div class="col-md-12 mb-2">
                                    <label class="small mb-1" for="inputUserId">ID do Usuário</label>
                                    <input class="form-control" id="inputUserId" type="text" disabled
                                        value="<?= esc($usuario['id_usuario']) ?>" />
                                </div>
                                <?php endif; ?>
                                
                                <!-- Form Group (first name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputNome">Nome</label>
                                    <input class="form-control" id="inputNome" name="inputNome" type="text"
                                        placeholder="João de Araujo Neto"
                                        value="<?= esc(old('inputNome', (isset($is_editing) && $is_editing && isset($usuario['nome_completo'])) ? $usuario['nome_completo'] : '')) ?>" required />
                                </div>
                                <!-- Form Group (login)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputLogin">Login</label>
                                    <input class="form-control" id="inputLogin" name="inputLogin" type="text"
                                        placeholder="JOAO.NETO"
                                        value="<?= esc(old('inputLogin', (isset($is_editing) && $is_editing && isset($usuario['usuario'])) ? $usuario['usuario'] : '')) ?>" required />
                                </div>
                            </div>

                            <!-- Form Group (senha)-->
                            <div class="mb-3">
                                <label class="small mb-1" for="inputSenha">Senha <?=(!empty($is_editing)) ? '(deixe vazio para manter a atual)' : '' ?></label>
                                <input class="form-control" id="inputSenha" name="inputSenha" type="password" 
                                    placeholder="<?=(!empty($is_editing)) ? 'Nova senha (opcional)' : 'Digite a senha' ?>"
                                    <?=(!empty($is_editing)) ? '' : 'required' ?> />
                            </div>
                            
                            <!-- Form Group (Empresa)-->
                            <div class="mb-3">
                                <label class="small mb-1" for="inputEmpresa">Empresa</label>
                                <select class="form-select" id="inputEmpresa" name="inputEmpresa" required>
                                    <option selected disabled>Selecione:</option>
                                    <?php foreach($lista_empresas as $empresa): ?>
                                        <option value="<?=$empresa['id_empresa']?>" 
                                            <?=(isset($usuario['id_empresa']) && $usuario['id_empresa'] == $empresa['id_empresa']) ? 'selected' : (old('inputEmpresa') == $empresa['id_empresa'] ? 'selected' : '') ?>>
                                            <?=$empresa['id_empresa']?> - <?=$empresa['razao_social']?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Form Group (Cargo)-->
                            <div class="mb-3">
                                <label class="small mb-1" for="inputCargo">Cargo</label>
                                <select class="form-select" id="inputCargo" name="inputCargo" required>
                                    <option selected disabled>Selecione:</option>
                                    <?php foreach($lista_cargos as $cargo): ?>
                                        <option value="<?=$cargo['id_cargo']?>"
                                            <?=(isset($usuario['id_cargo']) && $usuario['id_cargo'] == $cargo['id_cargo']) ? 'selected' : (old('inputCargo') == $cargo['id_cargo'] ? 'selected' : '') ?>>
                                            <?=$cargo['nome']?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Submit button-->
                            <button class="btn btn-primary" type="submit"><?=(!empty($is_editing)) ? 'Atualizar' : 'Criar'?> Usuário</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
    // Verificar se há o flashdata error para exibir o swal.fire pro usuario
    if(session()->getFlashdata('user.feedback.error')):
    $message = session()->getFlashdata('user.feedback.error');
    ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
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