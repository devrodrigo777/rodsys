<?= view("Partials/Client_Header", [
    'custom_css' => [
        ...($before_css ?? []),
        // 'https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css',
        ...($after_css ?? [])
    ],
    'custom_body_class' => 'nav-fixed',
]) ?>

<?= module_view('Dashboard', 'Partials/Topnav', $GLOBAL_ERPVARS) ?>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sidenav shadow-right sidenav-light">
            <div class="sidenav-menu">
                <div class="nav accordion" id="accordionSidenav">
                    <!-- Sidenav Menu Heading (Account)-->
                    <!-- * * Note: * * Visible only on and above the sm breakpoint-->
                    <div class="sidenav-menu-heading d-sm-none">Conta</div>
                    <!-- Sidenav Link (Alerts)-->
                    <!-- * * Note: * * Visible only on and above the sm breakpoint-->
                    <a class="nav-link d-sm-none" href="#!">
                        <div class="nav-link-icon"><i data-feather="bell"></i></div>
                        Alertas
                        <span class="badge bg-warning-soft text-warning ms-auto">4 Novos!</span>
                    </a>
                    <!-- Sidenav Link (Messages)-->
                    <!-- * * Note: * * Visible only on and above the sm breakpoint-->
                    <a class="nav-link d-sm-none" href="#!">
                        <div class="nav-link-icon"><i data-feather="mail"></i></div>
                        Messages
                        <span class="badge bg-success-soft text-success ms-auto">2 New!</span>
                    </a>

                    <?=module_view('Dashboard', 'Partials/Sidenav', [$modules], [])?>
                    
                </div>
            </div>
            <!-- Sidenav Footer-->
            <div class="sidenav-footer">
                <div class="sidenav-footer-content">
                    <div class="sidenav-footer-subtitle">Logado como:</div>
                    <div class="sidenav-footer-title"><?=$usuario['nome_completo']?></div>
                </div>
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <?php if(isset($service_view)) {
            // Aqui chamaremos o service que foi enviado e rodaremos a função render
            // Como se fosse um use
            if(is_object($service_view)) {    
                // $service_instance = new $service_view();
                echo $service_view->render($GLOBAL_ERPVARS);
            } else {
                echo "<div class='container-xl px-4'>Serviço não encontrado.</div>";
            }
        } ?>
    </div>
</div>

<?php
    /**
     * Verificar se há o flashdata swal.feedback para exibir o swal.fire pro usuario
     */
    if(session()->getFlashdata('swal.feedback')):
        $swal_feedback = session()->getFlashdata('swal.feedback'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: '<?= esc($swal_feedback['icon'] ?? 'info') ?>',
            title: '<?= esc($swal_feedback['title'] ?? '') ?>',
            text: '<?= esc($swal_feedback['message'] ?? '') ?>',
            <?= isset($swal_feedback['timer']) ? 'timer: ' . intval($swal_feedback['timer']) . ',' : '' ?>
            timerProgressBar: <?= isset($swal_feedback['timer']) ? 'true' : 'false' ?>,
            showConfirmButton: <?= isset($swal_feedback['showConfirmButton']) ? ($swal_feedback['showConfirmButton'] ? 'true' : 'false') : 'true' ?>,
            didOpen: (toast) => {
                Swal.hideLoading()
            }
        });
    });
</script>
<?php endif; ?>

<?= view(
    "Partials/Client_Footer",
    [
        'afterall_js' => // carregar vários customs js after all
     [
        // 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js',
        // base_url('assets/demo/chart-area-demo.js'),
        // base_url('assets/demo/chart-bar-demo.js'),
        // base_url('assets/demo/chart-pie-demo.js'),
        // 'https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js',
        // base_url('assets/js/litepicker.js'),
        'https://cdn.jsdelivr.net/npm/sweetalert2@11',
        base_url('assets/dashboard/js/dashboard.js'),
        ...($custom_js ?? []),
    ]
]) ?>