<main>
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="briefcase"></i></div>
                            <?=(!empty($is_editing) ? 'Atualizar' : 'Nova')?> Empresa
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="<?=base_url("dashboard/empresas")?>">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Empresas
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
                    <div class="card-header">Informações da Empresa</div>
                    <div class="card-body">
                        <form method="POST" action="<?= !empty($is_editing) ? base_url('empresas/api/update/' . ($empresa['id_empresa'] ?? '')) : base_url('empresas/api/create') ?>">
                            <?php if (!empty($is_editing)): ?>
                                <input type="hidden" name="is_editing" value="1" />
                                <input type="hidden" name="id_empresa" value="<?= esc($empresa['id_empresa'] ?? '') ?>" />
                            <?php endif; ?>

                           <!--  Form Group Disabled (ID Empresa)-->
                           <?php if (!empty($is_editing) && isset($empresa['id_empresa'])): ?>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-12">
                                        <label class="small mb-1" for="inputIdEmpresa">ID da Empresa</label>
                                        <input class="form-control" id="inputIdEmpresa" type="text" disabled
                                            value="<?= esc($empresa['id_empresa']) ?>" />
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Form Row-->
                            <div class="row gx-3 mb-3">
                                
                                <!-- Form Group (Razão Social)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputRazaoSocial">Razão Social</label>
                                    <input class="form-control" id="inputRazaoSocial" name="inputRazaoSocial" type="text"
                                        placeholder="Empresa XYZ LTDA" 
                                        value="<?= esc(old('inputRazaoSocial', (isset($is_editing) && $is_editing && isset($empresa['razao_social'])) ? $empresa['razao_social'] : '')) ?>" />
                                </div>
                                <!-- Form Group (CNPJ)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputCnpj">CNPJ</label>
                                    <input class="form-control" id="inputCnpj" name="inputCnpj" type="text"
                                        placeholder="00.000.000/0000-00" 
                                        value="<?= esc(old('inputCnpj', (isset($is_editing) && $is_editing && isset($empresa['cnpj'])) ? $empresa['cnpj'] : '')) ?>" />
                                </div>
                            </div>

                            <!-- Form Row-->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (Plano Ativo)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputPlanoAtivo">Plano Ativo</label>
                                    <select class="form-control" id="inputPlanoAtivo" name="inputPlanoAtivo">
                                        <option value="0" <?= (isset($is_editing) && isset($empresa['plano_ativo']) && $empresa['plano_ativo'] == 0) ? 'selected' : '' ?>>Inativo</option>
                                        <option value="1" <?= (isset($is_editing) && isset($empresa['plano_ativo']) && $empresa['plano_ativo'] == 1) ? 'selected' : '' ?>>Ativo</option>
                                    </select>
                                </div>
                                <!-- Form Group (Data Adesão) - Read Only-->
                                <?php if (!empty($is_editing) && isset($empresa['data_adesao'])): ?>
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputDataAdesao">Data de Adesão</label>
                                    <input class="form-control" id="inputDataAdesao" type="text" disabled
                                        value="<?= esc($empresa['data_adesao']) ?>" />
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Submit button-->
                            <button class="btn btn-primary" type="submit"><?=($is_editing) ? 'Atualizar' : 'Criar'?> Empresa</button>

                            <!-- Criar usuário -->
                             <?php
                                // Botão criar usuário vinculado à empresa, apenas se estiver editando e se for superadmin
                                 if (!empty($is_editing) && $permissions->user_is_superadmin() ): ?>
                                <a class="btn btn-warning ms-2" href="<?=base_url('dashboard/acessos/usuarios/?action=new&empresa_id=' . $empresa['id_empresa'])?>">
                                    Criar Usuário para esta Empresa
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php if(session()->getFlashdata('error')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            text: '<?= esc(session()->getFlashdata('error')) ?>',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            didOpen: (toast) => {
                Swal.hideLoading()
            }
        });
    });
</script>
<?php endif; ?>
