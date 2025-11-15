<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="<?=base_url("assets/js/scripts.js")?>"></script>

<?php if(isset($afterall_js) && is_array($afterall_js)): ?>
    <?php foreach($afterall_js as $script): ?>
        <script src="<?=$script?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

    </body>
</html>