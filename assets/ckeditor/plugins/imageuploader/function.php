<?php
if(isset($_POST["f"])){
    $f = filter_input(INPUT_GET, 'f', FILTER_SANITIZE_STRING);
    if($f == "loadImages") {
        loadImages();
    }else if($f == "loadFiles") {
        loadFiles();
    }
}

function TemPermissao($usuario) {
    $return = FALSE;
    try {
        $con = new PDO('pgsql:host=35.247.195.46;dbname=bemplease', 'bemplease', 'dbs3cr3t!');  
    
        $q = $con->prepare("SELECT count(*) FROM public.usuarios WHERE ativo='S' and username=?");
        $q->bindValue(1, $usuario);

        if ($q->execute()) {
            $row = $q->fetchAll();
            $return = $row[0][0] > 0;
        }

        return $return;
    } catch (Exception $ex) {
        return FALSE;
    }
}

function normalizar_filename($string){
   // pegando a extensao do arquivo
   $partes 	= explode(".", $string);
   $extensao 	= $partes[count($partes)-1];	
   // somente o nome do arquivo
   $nome	= preg_replace('/\.[^.]*$/', '', $string);	
   // removendo simbolos, acentos etc
   $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýýþÿŔŕ?';
   $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuuyybyRr-';
   $nome = strtr($nome, utf8_decode($a), $b);
   $nome = str_replace(".","-",$nome);
   $nome = preg_replace( "/[^0-9a-zA-Z\.]+/",'-',$nome);
   return utf8_decode(strtolower($nome.".".$extensao));
}

//reescrevi nessa funcao pra nao procurar por exstensao
function normalizar_string($str){
   // removendo simbolos, acentos etc
   $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýýþÿŔŕ?';
   $b = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYBSaaaaaaaceeeeiiiidnoooooouuuuyybyRr-';
   $str = strtr($str, utf8_decode($a), $b);
   $str = str_replace(".","-",$str);
   $str = preg_replace( "/[^0-9a-zA-Z\.]+/",'-',$str);
   return utf8_decode($str);
}

function loadImages() {
    require(__DIR__ . '/pluginconfig.php');
    
    if(file_exists($useruploadpath)){
        
        $filesizefinal = 0;
        $count = 0;
        
        $dir = $useruploadpath;
        $files = glob("$dir*.{jpg,jpe,jpeg,png,gif,ico}", GLOB_BRACE);
        usort($files, create_function('$a,$b', 'return filemtime($a) - filemtime($b);'));
        for($i=count($files)-1; $i >= 0; $i--):
            $image = $files[$i];
            $image_pathinfo = pathinfo($image);
            $image_extension = $image_pathinfo['extension'];
            $image_filename = $image_pathinfo['filename'];
            $image_basename = $image_pathinfo['basename'];
        
            // image src/url
            //$protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $protocol = 'https://';
            $site = $protocol. $_SERVER['SERVER_NAME'] . '/' ;
            $image_url = $site.$useruploadfolder."/".$image_basename;
        
            $size = getimagesize($image);
            $image_height = $size[0];
            $file_size_byte = filesize($image);
            $file_size_kilobyte = ($file_size_byte/1024);
            $file_size_kilobyte_rounded = round($file_size_kilobyte,1);
            $filesizetemp = $file_size_kilobyte_rounded;
            $filesizefinal = @round($filesizefinal + $filesizetemp) . " KB";
            $calcsize = @round($filesizefinal + $filesizetemp);
            $count = ++$count;
            
            if($file_style == "block") { ?>
                <div class="fileDiv"
                     onclick="showEditBar('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo $count; ?>','<?php echo $image_basename; ?>');"
                     ondblclick="showImage('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo $image_basename; ?>');"
                     data-imgid="<?php echo $count; ?>">
                    <div class="imgDiv"><img class="fileImg lazy" data-original="<?php echo $image_url; ?>"></div>
                    <p class="fileDescription"><span class="fileMime"><?php echo $image_extension; ?></span> <?php echo $image_filename; ?><?php if($file_extens == "yes"){echo ".$image_extension";} ?></p>
                    <p class="fileTime"><?php echo date ("F d Y H:i", filemtime($image)); ?></p>
                    <p class="fileTime"><?php echo $filesizetemp; ?> KB</p>
                </div>
            <?php } elseif($file_style == "list") { ?>
                <div class="fullWidthFileDiv"
                     onclick="showEditBar('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo $count; ?>','<?php echo $image_basename; ?>');"
                     ondblclick="showImage('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo $image_basename; ?>');"
                     data-imgid="<?php echo $count; ?>">
                    <div class="fullWidthimgDiv"><img class="fullWidthfileImg lazy" data-original="<?php echo $image_url; ?>"></div>
                    <p class="fullWidthfileDescription"><?php echo $image_filename; ?><?php if($file_extens == "yes"){echo ".$image_extension";} ?></p>
                    
                    <div class="qEditIconsDiv">
                        <img title="Delete File" src="img/cd-icon-qtrash.png" class="qEditIconsImg" onclick="window.location.href = 'imgdelete.php?img=<?php echo $image_basename; ?>'">
                    </div>
                    
                    <p class="fullWidthfileTime fullWidthfileMime fullWidthlastChild"><?php echo $image_extension; ?></p>
                    <p class="fullWidthfileTime"><?php echo $filesizetemp; ?> KB</p>
                    <p class="fullWidthfileTime fullWidth30percent"><?php echo date ("F d Y H:i", filemtime($image)); ?></p>
                </div>
            <?php }

        endfor;
        if($count == 0){
            echo "<div class='fileDiv' style='display:none;'></div>";
            $calcsize = 0;
        }
        if($calcsize == 0){
            $filesizefinal = "0 KB";
        }
        if($calcsize >= 1024){
            $filesizefinal = round($filesizefinal/1024,1) . " MB";
        }
        
        echo "
        <script>
            $( '#finalsize' ).html('$filesizefinal');
            $( '#finalcount' ).html('$count');
        </script>
        ";
    } else {
        echo '<div id="folderError">'.$alerts9.' <b>'.$useruploadfolder.'</b> '.$alerts10;
    } 
}


function loadFiles() {
    require(__DIR__ . '/pluginconfig.php');
    
    if(file_exists($useruploadpath)){
        
        $filesizefinal = 0;
        $count = 0;
        
        $dir = $useruploadpath;
        $files = glob("$dir*.{rar,zip,pdf}", GLOB_BRACE);
        usort($files, create_function('$a,$b', 'return filemtime($a) - filemtime($b);'));
        for($i=count($files)-1; $i >= 0; $i--):
            $image = $files[$i];
            $image_pathinfo = pathinfo($image);
            $image_extension = $image_pathinfo['extension'];
            $image_filename = $image_pathinfo['filename'];
            $image_basename = $image_pathinfo['basename'];
        
            // image src/url
            //$protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $protocol = 'https://';
            $site = $protocol. $_SERVER['SERVER_NAME'] . '/';
            $image_url = $site.$useruploadfolder."/".$image_basename;
            
            $size = getimagesize($image);
            $image_height = $size[0];
            $file_size_byte = filesize($image);
            $file_size_kilobyte = ($file_size_byte/1024);
            $file_size_kilobyte_rounded = round($file_size_kilobyte,1);
            $filesizetemp = $file_size_kilobyte_rounded;
            $filesizefinal = round($filesizefinal + $filesizetemp) . " KB";
            $calcsize = round($filesizefinal + $filesizetemp);
            $count = ++$count;
            
            ?>
                <div class="fullWidthFileDiv"
                     onclick="showEditBar('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo $count; ?>','<?php echo $image_basename; ?>');"
                     ondblclick="showImage('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo $image_basename; ?>');"
                     data-imgid="<?php echo $count; ?>">
                    
                    <!-- thumbnail da imagem -->
                    <div class="fullWidthimgDiv">
                        <!--<img class="fullWidthfileImg lazy" data-original="<?php echo $image_url; ?>">-->
                        <img src="<?= "img/icon_" . $image_extension . ".png" ?>" width="25" height="25">
                    </div>
                    
                    <p class="fullWidthfileDescription"><?php echo $image_filename; ?><?php if($file_extens == "yes"){echo ".$image_extension";} ?></p>
                    
                    <div class="qEditIconsDiv">
                        <img title="Delete File" src="img/cd-icon-qtrash.png" class="qEditIconsImg" onclick="window.location.href = 'imgdelete.php?img=<?php echo $image_basename; ?>'">
                    </div>
                    
                    <p class="fullWidthfileTime fullWidthfileMime fullWidthlastChild"><?php echo $image_extension; ?></p>
                    <p class="fullWidthfileTime"><?php echo $filesizetemp; ?> KB</p>
                    <p class="fullWidthfileTime fullWidth30percent"><?php echo date ("F d Y H:i", filemtime($image)); ?></p>
                </div>
            <?php

        endfor;
        if($count == 0){
            echo "<div class='fileDiv' style='display:none;'></div>";
            $calcsize = 0;
        }
        if($calcsize == 0){
            $filesizefinal = "0 KB";
        }
        if($calcsize >= 1024){
            $filesizefinal = round($filesizefinal/1024,1) . " MB";
        }
        
        echo "
        <script>
            $( '#finalsize' ).html('$filesizefinal');
            $( '#finalcount' ).html('$count');
        </script>
        ";
    } else {
        echo '<div id="folderError">'.$alerts9.' <b>'.$useruploadfolder.'</b> '.$alerts10;
    } 
}

function pathHistory() {
    require(__DIR__ . '/pluginconfig.php');
    $latestpathes = array_slice($foldershistory, -3);
    $latestpathes = array_reverse($latestpathes);
    foreach($latestpathes as $folder) {
        echo '<p class="pathHistory" onclick="useHistoryPath(\''.$folder.'\');">'.$folder.'</p>';
    }
}