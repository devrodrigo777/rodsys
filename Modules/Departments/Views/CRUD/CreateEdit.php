<?php

$is_superadmin = false;
$readonly = false;

if(isset($departamento)) {
$is_superadmin = ($departamento['slug'] == 'superadmin') ?? false;
$readonly = ($departamento['readonly'] ?? false) || $is_superadmin;
}


?>
<main>
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="user-plus"></i></div>
                            <?=(!empty($is_editing) ? 'Atualizar' : 'Novo')?> Departamento
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="<?=base_url("dashboard/departamentos")?>">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Departamentos
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
                                <input type="hidden" name="id_cargo" value="<?= esc($departamento['id_cargo'] ?? '') ?>" />
                            <?php endif; ?>

                            <!-- Form Row-->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (cargo id) - only when editing-->
                                <?php if (!empty($is_editing) && isset($departamento['id_cargo'])): ?>
                                <div class="col-md-12 mb-2">
                                    <label class="small mb-1" for="inputCargoId">ID do Cargo</label>
                                    <input class="form-control" id="inputCargoId" type="text" disabled
                                        value="<?= esc(session()->get('id_empresa') . '-' . $departamento['id_cargo']) ?>" />
                                </div>
                                <?php endif; ?>
                                
                                
                                <!-- Form Group (first name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputNome">Nome</label>
                                    <input class="form-control" id="inputNome" name="inputNome" type="text"
                                        <?=($readonly) ? 'readonly disabled' : '' ?>
                                        placeholder="Recursos Humanos"
                                        value="<?= esc(old('inputNome', (isset($is_editing) && $is_editing && isset($departamento['nome'])) ? $departamento['nome'] : '')) ?>" />
                                </div>
                                <!-- Form Group (last name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputDescricao">Descrição</label>
                                    <input class="form-control" id="inputDescricao" name="inputDescricao" type="text"
                                        <?=($readonly) ? 'readonly disabled' : '' ?>
                                        placeholder="Departamento responsável pela gestão de recursos humanos."
                                        value="<?= esc(old('inputDescricao', (isset($is_editing) && $is_editing && isset($departamento['descricao'])) ? $departamento['descricao'] : '')) ?>" />
                                </div>
                            </div>
                            <!-- Checkboxes de permissões abaixo -->
                            <?php
                            // Use provided `$permissoes` array if available, otherwise fall back to sensible defaults.
                            $permissoes = isset($permissoes) && is_array($permissoes) ? $permissoes : [
                                'departments.view'   => 'Visualizar Departamentos',
                                'departments.create' => 'Criar Departamentos',
                                'departments.edit'   => 'Editar Departamentos',
                                'departments.delete' => 'Excluir Departamentos',
                                'users.manage'       => 'Gerenciar Usuários',
                                'settings.access'    => 'Acessar Configurações',
                            ];

                            // Optional: array of selected permission keys, can be passed from controller
                            $selectedPermissions = isset($selectedPermissions) && is_array($selectedPermissions) ? $selectedPermissions : [];
                            // Old input (from back()->withInput()) for permissions
                            $oldPerms = old('permissoes');
                            ?>

                            <div class="mb-3">
                                <label class="small mb-1">Permissões</label>
                                <div class="row">
                                    <?php
                                    
                                    $currentGroup = '';
                                    $lastGroup = '';

                                    foreach ($permissoes as $perm): ?>
                                        
                                        <?php
                                        $permKey = $perm['slug'];
                                        $permLabel = $perm['descricao'];
                                        $permID = $perm['id_permissao'];
                                        // Lógica para agrupar permissões (exemplo simples baseado em prefixos)
                                        $parts = explode('.', $permKey);

                                        // $group = $parts[0]; // Pega o primeiro segmento como grupo
                                        $group = $perm['grupo'] ?? 'Diversas';

                                        if ($group !== $lastGroup) {
                                            if ($lastGroup !== ' ') {
                                                // Fecha a div do grupo anterior
                                                echo '</div></div>';
                                                echo  '<div class="col-12"><strong>' . ucfirst($group) . ':</strong></div>';
                                            }
                                            // Inicia um novo grupo
                                            // echo '<div class="col-12 pb-2"><div class="row"><strong>' . ucfirst($group) . ':</strong>';
                                            echo '<div class="col-12 pb-2"><div class="row">';
                                            $lastGroup = $group;
                                        }

                                        ?>
                                        
                                        <?php
                                        // Determine if checkbox should be checked: prefer old input, otherwise use $selectedPermissions
                                        $isChecked = false;
                                        if (is_array($oldPerms)) {
                                            if (in_array((string)$permID, $oldPerms, true) || in_array((int)$permID, $oldPerms, true)) {
                                                $isChecked = true;
                                            }
                                        } else {
                                            if (!empty($selectedPermissions) && (in_array($permKey, $selectedPermissions) || in_array($permID, $selectedPermissions))) {
                                                $isChecked = true;
                                            }
                                        }
                                        ?>
                                        <div class="col-md-4 mt-1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                        <?=($readonly) ? 'disabled' : '' ?>
                                                       name="permissoes[]" id="perm_<?= esc($permKey) ?>"
                                                       value="<?= esc($permID) ?>" <?= $isChecked ? 'checked' : '' ?> />
                                                <label class="form-check-label" for="perm_<?= esc($permKey) ?>"><?= esc($permLabel) ?></label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <hr class="mt-3"/>
                                </div>
                            </div>
                            <?php
                            if ($readonly): ?>
                            
                                <div class="alert alert-info alert-sm">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    Este departamento é protegido e não pode ser editado.
                                </div>
                                <a class="btn btn-sm btn-light text-primary" href="<?=base_url("dashboard/departamentos")?>">
                                    <i class="me-1" data-feather="arrow-left"></i>
                                    Departamentos
                                </a>
                            <?php else: ?>
                                <!-- Submit button-->
                                <button class="btn btn-primary" type="submit"><?=($is_editing) ? 'Atualizar' : 'Criar'?> Departamento</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
    // Verificar se há o flashdata error para exibir o swal.fire pro usuario
    if(session()->getFlashdata('department.feedback.error')):
    $message = session()->getFlashdata('department.feedback.error');
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