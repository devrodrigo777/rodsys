<main>
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-fluid px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="user"></i></div>
                            Gerenciar Módulos
                        </h1>
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
                            <th>Descrição</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</main>

<?php
    // Verificar se há o flashdata success para exibir o swal.fire pro usuario
    if(
        session()->getFlashdata('department.feedback.success')):
    
        $message = session()->getFlashdata('department.feedback.success');
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