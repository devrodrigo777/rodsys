<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <script type="text/javascript">window.BURL = '<?=base_url()?>';</script>
        
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Rodsys - Entrar</title>
        
        <?php if(isset($custom_css) && is_array($custom_css)): ?>
            <?php foreach($custom_css as $css): ?>
                <link href="<?=$css?>" rel="stylesheet" />
            <?php endforeach; ?>
        <?php endif; ?>

        <link href="<?=base_url("assets/css/styles.css")?>" rel="stylesheet" />

        <link rel="icon" type="image/x-icon" href="<?=base_url("assets/img/favicon.png")?>" />
        <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
    </head>
    
    <body class='<?=isset($custom_body_class)?$custom_body_class:"bg-primary"?>'>