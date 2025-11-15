<main>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-5">
        <!-- Custom page header alternative example-->
        <div class="d-flex justify-content-between align-items-sm-center flex-column flex-sm-row mb-4">
            <div class="me-4 mb-3 mb-sm-0">
                <h1 class="mb-0">Painel</h1>
                <div class="small">
                    <span class="fw-500 text-primary"><?= utf8_encode(strftime('%A')) ?></span> &middot;
                    <?= date('d/m/Y') ?> &middot; <?= date('H:i') ?>
                </div>
            </div>
        </div>
        <!-- Illustration dashboard card example-->
        <div class="card card-waves mb-4 mt-5">
            <div class="card-body p-5">
                <div class="row align-items-center justify-content-between">
                    <div class="col">
                        <h2 class="text-primary">Seja bem vindo ao painel RodSys</h2>
                        <p class="text-gray-700">Seus insigts, resumos e funcionalidades do sistema estarão por
                            aqui!<br />Sinta-se à vontade.</p>
                        <a class="btn btn-primary p-3" href="#!">
                            Saiba Mais
                            <i class="ms-1" data-feather="arrow-right"></i>
                        </a>
                    </div>
                    <div class="col d-none d-lg-block mt-xxl-n4"><img class="img-fluid px-xl-4 mt-xxl-n5"
                            src="assets/img/illustrations/statistics.svg" /></div>
                </div>
            </div>
        </div>
    </div>
</main>