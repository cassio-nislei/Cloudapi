<?php
session_start();
if (isset($_REQUEST['InputImagem'])) {
    $inputimagem = @$_REQUEST['InputImagem'];
}
$avatar = null;
if (isset($_REQUEST['avatar'])) {
    $avatar = @$_REQUEST['avatar'];
}


//desabilita news. Mesmo comentando em pluginnfo.js continuava aparecendo.
setcookie('show_news', 'no');
?>



<!-- Copyright (c) 2015, Fujana Solutions - Moritz Maleck. All rights reserved. -->
<!-- For licensing, see LICENSE.md -->

<?php

// Don't remove the following two rows
$link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$root = "https://$_SERVER[HTTP_HOST]";

// checking lang value
if(isset($_COOKIE['sy_lang'])) {
    $load_lang_code = $_COOKIE['sy_lang'];
} else {
    $load_lang_code = "en";
}

// including lang files
switch ($load_lang_code) {
    case "en":
        require(__DIR__ . '/lang/en.php');
        break;
    case "pl":
        require(__DIR__ . '/lang/pl.php');
        break;
}

// Including the plugin config file, don't delete the following row!
require(__DIR__ . '/pluginconfig.php');
// Including the functions file, don't delete the following row!
require(__DIR__ . '/function.php');
// Including the check_permission file, don't delete the following row!
require(__DIR__ . '/check_permission.php');

if (!isset($_SESSION['usuario'])){
    echo "Voce nao tem permissao para acessar este recurso.";
    exit;
}else {
    if (!TemPermissao(base64_decode($_SESSION['usuario']))) {
        echo "Usuario nao autorizado.";
        exit;
    }
}

/* retiro a validacao de login
if ($username == "" and $password == "") {
    if(!isset($_SESSION['username'])){
        include(__DIR__ . '/new.php');
        exit;	
    }
} else {
    if(!isset($_SESSION['username'])){
        include(__DIR__ . '/loginindex.php');
        exit;	
    }
}
 */
$_SESSION["username"] = "disabled_pw"; /* e com essa linha desabilito a exigencia de senha */
$show_settings = FALSE; /* bloqueio o acesso ao painel de controle */

?>

<!DOCTYPE html>
<html lang="en"
      ondragover="toggleDropzone('show')"
      ondragleave="toggleDropzone('hide')">
<head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title><?php echo $imagebrowser1; ?></title>
    <meta name="author" content="Moritz Maleck">
    <link rel="icon" href="img/cd-ico-browser.ico">
    
    <link rel="stylesheet" href="styles.css">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://www.maleck.org/imageuploader/plugininfo.js"></script>
    
    <script src="dist/jquery.lazyload.min.js"></script>
    <script src="dist/js.cookie-2.0.3.min.js"></script>
    
    
    <script src="dist/sweetalert.min.js"></script>
    <link rel="stylesheet" type="text/css" href="dist/sweetalert.css">
    
    <script src="../../../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <link href="../../../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../../bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
    
    
    <script src="function.js"></script>
    
    <script>
        // Plugin version
        var currentpluginver = "<?php echo $currentpluginver; ?>";
        // ajax request to register the plugin for better support
        /*$.ajax({
          method: "POST",
          url: "http://ibm.bplaced.com/imageuploader/register.php",
          data: { root: "<?php echo $root; ?>", link: "<?php echo $link; ?>", ver: ""+ currentpluginver +"" }
        })*/
    </script>
    
</head>
<body ontouchstart="">
    
<div id="header">
    <!--<a class="" href="http://imageuploaderforckeditor.altervista.org/" target="_blank"><img src="img/cd-icon-image.png" class="headerIconLogo"></a>-->
    <img onclick="Cookies.remove('qEditMode');window.close();" src="img/cd-icon-close-grey.png" class="headerIconRight iconHover">
    <img onclick="reloadImages();" src="img/cd-icon-refresh.png" class="headerIconRight iconHover">
    <button class="btn btn-success pull-left"
         onclick="$('#imageFullSreen').hide(); $('#background').slideUp(250, 'swing');"
         data-toggle="modal" data-target="#uploadModal">
        <span class="glyphicon glyphicon-cloud-upload"></span> Enviar Arquivo
    </button><!-- onclick='uploadImg()' -->
    <?php if($show_settings): ?>
    <img onclick="pluginSettings();" src="img/cd-icon-settings.png" class="headerIconRight iconHover">
    <?php endif; ?>
</div>
    
<div id="editbar">
    <div id="editbarView" onclick="#" class="editbarDiv">
        <!--<img src="img/cd-icon-images.png" class="editbarIcon editbarIconLeft">-->
        <!--<p class="editbarText"><?php echo $buttons1; ?></p>-->
        <button class="btn btn-default" style="width: 100px;"><span class="glyphicon glyphicon-eye-open"></span> Visualizar</button>
    </div>
    
    <a href="#" id="editbarDownload" download>
        <div class="editbarDiv">
            <!--<img src="img/cd-icon-download.png" class="editbarIcon editbarIconLeft">
            <p class="editbarText"> <?php echo $buttons2; ?> </p>-->
            <button class="btn btn-default" style="width: 100px;"><span class="glyphicon glyphicon-cloud-download"></span> Download</button>
        </div>
    </a>
    <?php if (isset($inputimagem)) { ?>
        <button class="btn btn-primary" style="width: 100px;" onclick="Utilizar('<?= $inputimagem ?>', '<?= $avatar ?>');">
            <span class="glyphicon glyphicon-thumbs-up"></span>
            Utilizar
        </button>
    <?php } else { ?>
    <div id="editbarUse" onclick="#" class="editbarDiv">
        <!--<img src="img/cd-icon-use.png" class="editbarIcon editbarIconLeft">
        <p class="editbarText"> <?php echo $buttons3; ?> </p>-->
        <button class="btn btn-default" style="width: 100px;"><span class="glyphicon glyphicon-thumbs-up"></span> Utilizar</button>
    </div>
    <?php } ?>
    <div id="editbarDelete" onclick="#" class="editbarDiv">
        <!--<img src="img/cd-icon-qtrash.png" class="editbarIcon editbarIconLeft">
        <p class="editbarText"> <?php echo $buttons4; ?> </p>-->
        <button class="btn btn-default" style="width: 100px;"><span class="glyphicon glyphicon-trash"></span> Excluir</button>
    </div>

    <img onclick="hideEditBar();" src="img/cd-icon-close-black.png" class="editbarIcon editbarIconRight">
</div>
    
<div id="updates" class="popout"></div>
    
<div id="dropzone" class="dropzone" 
     ondragenter="return false;"
     ondragover="return false;"
     ondrop="drop(event)">
    <p>
        <img src="img/cd-icon-upload-big.png"><br>
        <?php echo $imagebrowser4; ?>
    </p>
</div>

<!--
<p class="folderInfo"><?php echo $imagebrowser2; ?> <span id="finalcount"></span> <?php echo $imagebrowser3; ?> - <span id="finalsize"></span>
-->
    <?php if($file_style == "block") { ?>
        <img title="List" src="img/cd-icon-list.png" class="headerIcon floatRight" onclick="window.location.href = 'pluginconfig.php?file_style=list';">
    <?php } elseif($file_style == "list") { ?>
        <img title="Block" src="img/cd-icon-block.png" class="headerIcon floatRight" onclick="window.location.href = 'pluginconfig.php?file_style=block';">
        <img title="Quick Edit" id="qEditBtnOpen" src="img/cd-icon-qedit.png" class="headerIcon floatRight" onclick="toogleQEditMode();">
        <img title="Quick Edit" id="qEditBtnDone" src="img/cd-icon-done.png" class="headerIcon floatRight" onclick="toogleQEditMode();">
    <?php } ?>
</p>

<?php 
    if (isset($_SESSION['usuario'])) {
        echo "<p align='right'>Usuário: <b>" . base64_decode( $_SESSION['usuario'] );
        echo "</b> | Diretório: <b>" . base64_decode( $_SESSION['diretorio'] );
        echo "</b></p>";
    }else {
        echo "Usuário nao identificado";
    }
        /*
        echo "useruploadroot: $useruploadroot<br>"
            . "browserfolder: $browserfolder<br>"
            . "useruploadfolder: $useruploadfolder<br>"            
            . "usersiteroot: $usersiteroot<br>"
            . "useruploadpath: $useruploadpath<br>";
        */
    ?>
<div id="content">
    <div class="row">
        <div class="col-md-12">            
                <div id="tabs">
                    <ul class="nav nav-tabs" data-tabs="tabs">
                        <li><a href="#imagens" data-toggle="tab">Imagens</a></li>
                        <li><a href="#arquivos" data-toggle="tab">Arquivos</a></li>
                    </ul>

                    <div id="my-tab-content" class="tab-content">
                        <div id="imagens" class="tab-pane active">
                            <div id="files">
                            <?php
                                loadImages();
                            ?>
                            </div>
                        </div>
                        <div id="arquivos" class="tab-pane">
                            <div id="files2">
                            <?php
                                loadFiles();
                            ?>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<?php /* if($file_style == "block") { ?>
    <div class="fileDiv" onclick="window.location.href = 'http://imageuploaderforckeditor.altervista.org';">
        <div class="imgDiv">Image Uploader for CKEditor</div>
        <p class="fileDescription">&copy; 2016 by Moritz Maleck</p>
        <p class="fileTime">imageuploaderforckeditor.altervista.org</p>
        <p class="fileTime">180 KB</p>
    </div>
<?php } elseif($file_style == "list") { ?>
    <div class="fullWidthFileDiv" onclick="window.location.href = 'http://imageuploaderforckeditor.altervista.org';">
        <div class="fullWidthimgDiv"><img class="fullWidthfileImg lazy" data-original="img/cd-icon-credits.png"></div>
        <p class="fullWidthfileDescription">Image Uploader for CKEditor</p>
        <p class="fullWidthfileTime fullWidthfileMime">png</p>
        <p class="fullWidthfileTime">180 KB</p>
        <p class="fullWidthfileTime fullWidth30percent">imageuploaderforckeditor.altervista.org</p>
    </div>
<?php } */ ?>

<div id="imageFullSreen" class="lightbox popout">
    <div class="buttonBar">
        <button id="imageFullSreenClose" class="btn btn-success" onclick="$('#imageFullSreen').hide(); $('#background').slideUp(250, 'swing');"><span class="glyphicon glyphicon-remove"></span> </button>
        <a href="#" id="imgActionDownload" download><button class="btn btn-success"><span class="glyphicon glyphicon-cloud-download"></span></button></a>
        <button id="imgActionUse" onclick="#" class="btn btn-success"><span class="glyphicon glyphicon-thumbs-up"></span></button>
    </div><br><br>
    <img id="imageFSimg" src="#" style="#"><br>
</div>
    


<!-- JANELA MODAL -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="modalLoginLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <!-- header -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Enviar Arquivo</h4>
        </div>
        <div class="modal-body">
            <!-- conteudo inicio -->
            <div id="uploadImgDiv" <!--class="lightbox popout"-->
                <div class="buttonBar">
                    <!--<button class="headerBtn" onclick="$('#uploadImgDiv').hide(); $('#background2').slideUp(250, 'swing');"><img src="img/cd-icon-close.png" class="headerIcon"></button>-->
                    <button class="btn btn-success" name="submit" onclick="$('#uploadImgForm').submit();"><span class="glyphicon glyphicon-cloud-upload"></span> <?php echo $buttons7; ?></button>
                </div><br><br><br>
                <form action="imgupload.php" method="post" enctype="multipart/form-data" id="uploadImgForm" onsubmit="return checkUpload();">
                    <center>
                    <p>
                    <h3> <?php echo $uploadpanel1; ?> </h3>                    
                    </p>
                    <!--<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxFileSize ?>">-->
                    <input type="file" name="upload" id="upload">
                    <!--<br><h3 class="settingsh3" style="font-size:12px;font-weight:lighter;"><?php echo $uploadpanel2; ?><br><span style="font-weight:bolder;">"<?php echo $useruploadfolder; ?>"</span> (<?php echo $uploadpanel3; ?>)</h3>-->
                    <h3 style="font-size:12px;">Tamanho máximo: <?php echo ($maxFileSize/1024) . " KB (" . round(($maxFileSize/1024/1024),1) . " MB )"; ?></h3>
                    <h3 style="font-size:12px;font-weight:lighter;">Após selecionar a Imagem ou Arquivo clique em Upload para enviar ao servidor.<br>
                        <br><font color="gray">Imagens: .jpg, .png, .jpeg, .gif e .ico | Arquivos .zip, .rar e .pdf</font></h3>
                    <br>
                    </center>
                </form>
            </div>
            <!-- conteudo fim -->
        </div>
    </div>
</div>
<!-- FIM JANELA MODAL -->

<?php if($show_settings) { ?>
    <div id="settingsDiv" class="lightbox popout">
        <div class="buttonBar">
            <button class="headerBtn" onclick="$('#settingsDiv').hide(); $('#background3').slideUp(250, 'swing');"><img src="img/cd-icon-close.png" class="headerIcon"></button>
        </div><br><br><br>

        <h3 class="settingsh3"><?php echo $panelsettings1; ?></h3>
        <!--choose existing folder text-->
        <p class="settingsh3 saveUploadPathP"><?php echo $panelsettings2; ?></p>
        <!--editable upload path-->
        <p class="uploadP editable" id="uploadpathEditable"><?php echo $useruploadfolder; ?></p>
        <!--path history-->
        <p class="settingsh3 saveUploadPathP"><?php echo $panelsettings3; ?></p>
        <?php
        pathHistory();
        ?>
        <!--cancel btn-->
        <button class="headerBtn greyBtn saveUploadPathA" id="pathCancel"><?php echo $buttons5; ?></button>
        <!--save btn-->
        <button class="headerBtn saveUploadPathA" onclick="updateImagePath();"><?php echo $buttons6; ?></button><br class="saveUploadPathA">

        <br><h3 class="settingsh3"><?php echo $panelsettings4; ?></h3>
        <!--Hide/show news section-->
        <?php if($news_sction == "yes"){ ?>
            <p class="uploadP" onclick="disableNews()"><img src="https://www.maleck.org/imageuploader/img/hide.png" class="headerIcon"> <?php echo $panelsettings21; ?></p>
        <?php } elseif($news_sction == "no") { ?>
            <p class="uploadP" onclick="enableNews()"><img src="https://www.maleck.org/imageuploader/img/show.png" class="headerIcon"> <?php echo $panelsettings22; ?></p>
        <?php } ?>
        <!--Hide/show file extension-->
        <?php if($file_extens == "yes"){ ?>
            <p class="uploadP" onclick="extensionSettings('no');"><img src="img/cd-icon-hideext.png" class="headerIcon"> <?php echo $panelsettings5; ?></p>
        <?php } elseif($file_extens == "no") { ?>
            <p class="uploadP" onclick="extensionSettings('yes');"><img src="img/cd-icon-showext.png" class="headerIcon"> <?php echo $panelsettings6; ?></p>
        <?php } ?>
        <!--change language-->
        <p class="uploadP" onclick="openLangPanel();"><img src="img/cd-icon-translate.png" class="headerIcon"> <?php echo $panelsettings20; ?></p>

        <!--show if password is enabled-->
        <?php if($_SESSION["username"] != "disabled_pw"){ ?>
            <br><h3 class="settingsh3"><?php echo $panelsettings7; ?></h3>
            <!--logout-->
            <p class="uploadP" onclick="logOut();"><img src="img/cd-icon-logout.png" class="headerIcon"> <?php echo $panelsettings8; ?></p>
            <!--disable password-->
            <p class="uploadP" onclick="window.open('https://imageuploaderforckeditor.altervista.org/disable_pw.html','about:blank', 'toolbar=no, scrollbars=yes, resizable=no, width=900, height=600');"><img src="img/cd-icon-disable.png" class="headerIcon"> <?php echo $panelsettings9; ?></p>
        <?php } ?>

        <br><h3 class="settingsh3"><?php echo $panelsettings10; ?></h3>
        <!--donate button-->
        <p class="uploadP" onclick="$( '#donate' ).submit();"><img src="img/cd-icon-coffee.png" class="headerIcon"> <?php echo $panelsettings11; ?></p>

        <br><h3 class="settingsh3"><?php echo $panelsettings12; ?></h3>
        <!--FAQ button-->
        <p class="uploadP" onclick="window.open('https://imageuploaderforckeditor.altervista.org/support/','_blank');"><img src="img/cd-icon-faq.png" class="headerIcon"> <?php echo $panelsettings13; ?></p>
        <!--report a bug-->
        <p class="uploadP" onclick="window.open('https://ibm.bplaced.com/contact/index.php?cdproject=Image%20Uploader%20and%20Browser%20for%20CKEditor&cdlink=<?php echo $link; ?>&cdver='+currentpluginver,'_blank');"><img src="img/cd-icon-bug.png" class="headerIcon"> <?php echo $panelsettings14; ?></p>

        <br><h3 class="settingsh3"><?php echo $panelsettings15; ?></h3>
        <!--current version-->
        <p class="uploadP"><img src="img/cd-icon-version.png" class="headerIcon"> <?php echo $currentpluginver; ?></p>

        <br><h3 class="settingsh3"><?php echo $panelsettings16; ?></h3>
        <!--credits-->
        <p class="uploadP"><img src="img/cd-icon-credits.png" class="headerIcon"> <?php echo $panelsettings17; ?></p>

        <br><h3 class="settingsh3"><?php echo $panelsettings18; ?></h3>
        <!--icon refr-->
        <p class="uploadP" onclick="window.open('https://icons8.com','_blank');"><img src="img/cd-icon-images.png" class="headerIcon"> <?php echo $panelsettings19; ?></p>

        <br>
    </div>
<?php } ?>
    
<form id="donate" target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="BTEL7F2ZLR3T6">
</form> 
    
<div id="background" class="background" onclick="$('#imageFullSreenClose').trigger('click');"></div>
<div id="background2" class="background" onclick="$('#uploadImgDiv').hide(); $('#background2').slideUp(250, 'swing');"></div>
<div id="background3" class="background" onclick="$('#settingsDiv').hide(); $('#background3').slideUp(250, 'swing');"></div>
<div id="background4" class="background" onclick="$('#setLangDiv').hide(); $('#background4').slideUp(250, 'swing');"></div>

<!--Noscript part if js is disabled-->
<noscript> <div class="noscript"> <div id="folderError" class="noscriptContainer popout"> <b><?php echo $alerts1; ?></b><br><br><?php echo $alerts5; ?> <a href="https://www.enable-javascript.com/" target="_blank"><?php echo $alerts6; ?></a><br><br><?php echo $alerts4; ?> </div></div></noscript>
    
<?php   
// Including the language file, don't delete the following row!
require(__DIR__ . '/lang/lang.php');
?>

<script type="text/javascript">
    function Utilizar(inputImagem, avatar) {
        var input = window.opener.document.getElementById(inputImagem);
        input.value = $("#editbarDownload").attr("href");
        if (avatar) {
            var a = window.opener.document.getElementById(avatar);
            a.src = $("#editbarDownload").attr("href");
        }
        window.close();
    }
</script>

</body>
</html>