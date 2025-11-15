<?= view("Partials/Client_Header") ?>
<div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
        <main>
            <div class="container-xl px-4">
                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <!-- Basic login form-->
                        <div class="card shadow-lg border-0 rounded-lg mt-5">
                            <div class="card-header justify-content-center">
                                <h3 class="fw-light my-4">Acesso - RodSys</h3>
                            </div>
                            <div class="card-body">
                                <!-- Login form-->
                                <form method="POST" action="<?= base_url('login/authenticate') ?>">

                                    <?php if (session()->getFlashdata('login_message')) : ?>
                                        <div class="alert alert-warning" role="alert">
                                            <?= session()->getFlashdata('login_message') ?>
                                        </div>
                                    <?php endif; ?>



                                    <!-- ID da Empresa-->
                                    <div class="mb-3">
                                        <label class="small mb-1" for="inputCompanyID">ID da Empresa</label>
                                        <input name="id_empresa" class="form-control" id="inputCompanyID" type="text"
                                            value="<?= old("id_empresa") ?>"
                                            placeholder="Ex.: 23501" maxlength="8" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" />
                                    </div>
                                    <!-- Usuario -->
                                    <div class="mb-3">
                                        <label class="small mb-1" for="inputUsername">Usuario</label>
                                        <input name="usuario" class="form-control" id="inputUsername" type="text"
                                            value="<?= old("usuario") ?>"
                                            placeholder="Ex.: JOAO.SILVA" style="text-transform: uppercase;" />
                                    </div>
                                    <!-- Senha -->
                                    <div class="mb-3">
                                        <label class="small mb-1" for="inputPassword">Senha</label>
                                        <input name="senha" class="form-control" id="inputPassword" type="password"
                                            placeholder="Insira sua senha" />
                                    </div>
                                    <!-- Form Group (login box)-->
                                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                        <!-- <a class="small" href="auth-password-basic.html">Esqueceu sua senha?</a> -->
                                         <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Esqueceu sua senha?</button>
                                        <button type="submit" class="btn btn-primary">ACESSAR</button>
                                    </div>
                                </form>
                            </div>
                            <!-- <div class="card-footer text-center">
                                <div class="small"><a href="auth-register-basic.html">Precisa de uma conta? Clique aqui</a>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div id="layoutAuthentication_footer">
        <footer class="footer-admin mt-auto footer-dark">
            <div class="container-xl px-4">
                <div class="row">
                    <div class="col-md-6 small">Copyright &copy; RodSys 2025</div>
                    <div class="col-md-6 text-md-end small">
                        <a href="#!">Política de Privacidade</a>
                        &middot;
                        <a href="#!">Termos &amp; Condições</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Modal Forgot Password -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Esqueci minha senha. E agora?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Para redefinir sua senha, entre em contato com o administrador do sistema ou com o suporte técnico da RodSys.</p>
                <p>Você pode enviar um e-mail para <a href="mailto:rodrigolca@gmail.com">RodSys</a> solicitando a redefinição da sua senha.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendi</button>
        </div>

    </div>

<?= view("Partials/Client_Footer") ?>