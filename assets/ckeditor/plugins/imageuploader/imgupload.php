<?php

// Copyright (c) 2015, Fujana Solutions - Moritz Maleck. All rights reserved.
// For licensing, see LICENSE.md

session_start();

if(!isset($_SESSION['username'])) {
    exit;
}

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

if (!isset($_SESSION['usuario'])){
    echo "Voce nao tem permissao para acessar este recurso.";
    exit;
}else {
    if (!TemPermissao(base64_decode($_SESSION['usuario']))) {
        echo "Usuario nao autorizado.";
        exit;
    }
}

$info = pathinfo($_FILES["upload"]["name"]);
$ext = $info['extension'];
$target_dir = $useruploadpath;
$ckpath = "ckeditor/plugins/imageuploader/$useruploadpath";
//$randomLetters = $rand = substr(md5(microtime()),rand(0,26),6);
$imgnumber = count(scandir($target_dir));
//$filename = "$imgnumber$randomLetters.$ext";
//$filename = $info['basename']; //add isso aki e comentei as linhas anteriores para o arquivo ficar com mesmo nome
$filename = strtolower( normalizar_string($info['filename']) . '_' . $imgnumber . '.' . $ext ); //agora assim: nome_1.ext (tudo em minusculo)
//$filename = normalizar_filename($filename); //remove caracteres especiais, espacos, etc.
$target_file = $target_dir . $filename;
$ckfile = $ckpath . $filename;
$uploadOk = 1;
$imageFileType = strtolower( pathinfo($target_file,PATHINFO_EXTENSION) );
if (($imageFileType != "zip") && ($imageFileType != "rar") && ($imageFileType != "pdf")) //ADICIONEI ISSO PARA PERMITIR UPLOAD DE RAR E ZIP
{
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["upload"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<script>alert('".$uploadimgerrors1."');</script>";
        $uploadOk = 0;
    }    
} //SE NAO FOR PARA PERMITIR RAR E ZIP TIRAR ESSE CONDICIONAL
// Check if file already exists
if (file_exists($target_file)) {
    echo "<script>alert('".$uploadimgerrors2."');</script>";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["upload"]["size"] > $maxFileSize) {
    echo "<script>alert('".$uploadimgerrors3."');</script>";            
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" && $imageFileType != "ico" 
    && $imageFileType != "zip" && $imageFileType != "rar" && $imageFileType != "pdf" ) { //ADICIONEI RAR E ZIP AKI.
    echo "<script>alert('".$uploadimgerrors4."');</script>";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "<script>alert('".$uploadimgerrors5."');</script>";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
        if(isset($_GET['CKEditorFuncNum'])){
            $CKEditorFuncNum = $_GET['CKEditorFuncNum'];
            echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$ckfile', '');</script>";
        }
    } else {
        echo "<script>alert('".$uploadimgerrors6." ".$target_file." ".$uploadimgerrors7."');</script>";
    }
}
//Back to previous site
if(!isset($_GET['CKEditorFuncNum'])){
    echo '<script>history.back();</script>';
}