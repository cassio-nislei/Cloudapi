<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdmCloud</title>
    <meta name="description" content="AdmCloud">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="<?= base_url('images/favicon.png')?>">
    <link rel="shortcut icon" href="<?= base_url('images/favicon.png') ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/cs-skin-elastic.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->
</head>
<body class="bg-dark000">
    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <?php 
                include APPPATH . 'views/templates/regions/alert.php';
            ?>
            <div class="login-content">
                <div class="login-logo">
                    <a href="<?= base_url() ?>">
                         <img class="align-content" src="<?= base_url('images/logo_empresa.png') ?>" 
                             style="<?= (!isMobile()) ? 'width: 20rem' : 'width: 50%'?>"
                             >
                    </a>
                </div>                
                <div class="login-form">
                    <div style="font-size: 14px; text-align: justify;">                        
                    </div>
                    <form action="<?= base_url('Account/newpass') ?>" method="post">                        
                        <div class="form-group">
                            <label>Nova Senha</label>
                            <input id="senha" name="senha" type="password" class="form-control" placeholder="Digite sua nova senha aqui"
                                   value="<?= set_value('') ? : (isset($senha) ? $senha : '') ?>"
                                   >
                        </div>  
                        <div class="form-group">
                            <label>Repetir Senha</label>
                            <input id="senha_confirmacao" name="senha_confirmacao" type="password" class="form-control" placeholder="Confirme a nova senha aqui"
                                   value="<?= set_value('') ? : (isset($senha_confirmacao) ? $senha_confirmacao : '') ?>"
                                   >
                        </div>
                        <input type="hidden" name="token_request" value="<?= $token_request ?>">                        
                        <input id="confirmar" name="confirmar" type="submit" class="btn btn-hti btn-flat m-b-30 m-t-30" value="CONFIRMAR">                        
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>

</body>
</html>
