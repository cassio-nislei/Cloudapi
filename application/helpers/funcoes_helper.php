<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    function is5_validar_periodo($data_inicial, $data_final) {
        try {
            if (!empty($data_inicial)) {
                $data_inicial = dateToDb($data_inicial);
                if (!is5_data_valida_db($data_inicial)) {
                    throw new Exception('Especifique uma Data Inicial válida.');
                }
            }

            if (!empty($data_final)) {
                $data_final = dateToDb($data_final);
                if (!is5_data_valida_db($data_final)) {
                    throw new Exception('Especifique uma Data Final válida.');
                }
            }

            if ( (!empty($data_inicial) && empty($data_final)) || (empty($data_inicial) && !empty($data_final)) ) {
                throw new Exception('Especifique Data Inicial e Final ou deixe ambas em branco.');
            }
            
            return [TRUE, 'OK'];
            
        } catch (Exception $ex) {
            return [FALSE, $ex->getMessage()];
        }
        return [NULL, NULL];
    }

    function get_horas_range() {
        $horas = [];        
        for($i = 0; $i <= 24; $i++) {
            $hora  = Zeros($i, 2);
            $horas[] = "$hora:00";
            if ($i < 24) {
                $horas[] = "$hora:30";
            }
        }
        return $horas;
    }

    function is_seted($arr, $index) {
        if (!isset($arr[$index])) {
            return FALSE;
        }
        
        return !embranco($arr[$index]);
    }

    function gerar_slug($tabela, $txt) {
        $CI &= get_instance();        
        try {
            //gera slug
            $this->load->library('Slug','slug');
            $slug = $this->slug->gen($txt);
            
            //verifica se jah existe
            $count = $CI->db->where(['slug' => $slug])
                            ->count_all_results($tabela);
            
            //se jah existir, add "-X" no final
            if ($count) {
                $slug = $slug.'-'.$count++;
            }
              
            return $slug;
            
        } catch (Exception $ex) {
            return null;
        }
    }

    function is5_get_primeiro_dia_mes() {
        $month_start = strtotime('first day of this month', time());
        return date('Y-m-d', $month_start);
    }
    
    function is5_get_ultimo_dia_mes() {
        $month_end = strtotime('last day of this month', time());
        return date('Y-m-d', $month_end);
    }

    function is5_count($var) {
        return is_array($var) ? count($var) : 0;	
    }

    function is5_is_letter_or_number($val) {
        return preg_match('/^[a-zA-Z0-9]+$/', $val);
    }

    function is5_get_numeric($val) {
        //https://www.php.net/manual/pt_BR/function.is-numeric.php
        //Se você quiser o valor numérico de uma string, isso retornará um valor float ou int
        if (is_numeric($val)) {
          return $val + 0;
        }
        return 0;
    }

    function salvarImagem($imagem, $params = []) {
        $status   = FALSE;
        $mensagem = '';
        $img_name = '';
        $img_path = '';
        try {
            $size    = (int) isset($params['size'])   ?  : 5 * 1024 * 1024;
            $width   = (int) isset($params['width'])  ?  : 500;
            $height  = (int) isset($params['height']) ?  : 375;
            $prefixo = isset($params['prefixo'])      ?  : 'imagem_';

            $b64_img = extractB64($imagem);

            if (empty($b64_img)) {
                throw new Exception('Impossível recuperar imagem.');
            }

            //gera nome destino
            $img_name = uniqid($prefixo).'.jpg';
            $img_path = $this->session->userdata('_emitente_home_local').'/'.$img_name;

            //salva arquivos
            file_put_contents($img_path, base64_decode($b64_img));

            //verificar se realmente eh uma imagem
            $r = getimagesize($img_path);
            if (!$r) {
                unlink($img_path);
                throw new Exception('Especifique uma imagem válida.');
            }

            //verificar tamanho 
            if (filesize($img_path) > $size) {
                unlink($img_path);
                throw new Exception('A imagem não pode ser maior que .'.($size/1024/1024).'MB');
            }

            //verificar resolucao
            if ( ($r[0] < $width) || ($r[1] < $height) ) {
                //$r[0] = width
                //$r[1] = height
                unlink($img_path);
                throw new Exception('A imagem deve ter uma resolução maior ou igual a '.$width.'x'.$height."\n\rResolução da imagem enviada: $r[0]x$r[1].");
            }

            //verificar tipo
            $mime = getMimeType($img_path);
            if (!in_array($mime, ['image/png','image/jpeg','image/jpg'])) {
                unlink($img_path);
                throw new Exception('A imagem deve estar no formato JPG, JPEG ou PNG.');
            }

            $status = TRUE;
            $mensagem = 'Imagem salva com sucesso!';            
        } catch (Exception $ex) {
            $status = FALSE;
            $mensagem = $ex->getMessage();
        }        
        $obj = new stdClass();
        $obj->status   = $status;
        $obj->mensagem = $mensagem;
        $obj->img_name = $img_name;
        $obj->img_path = $img_path;
        return $obj;
    }

    function getMimeType($arquivo, $toLower = TRUE) {
        try {
            if (file_exists($arquivo)) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $arquivo);
                if ($toLower) {
                    $mime = is5_strtolower($mime);
                }
                return $mime;
            }
            return NULL;
        } catch (Exception $ex) {
            return NULL;
        } 
    }

    //https://www.geeksforgeeks.org/php-startswith-and-endswith-functions/
    function is5_startsWith ($string, $startString) 
    { 
        $len = strlen($startString); 
        return (substr($string, 0, $len) === $startString); 
    } 
    
    //https://www.geeksforgeeks.org/php-startswith-and-endswith-functions/
    function is5_endsWith($string, $endString) 
    { 
        $len = strlen($endString); 
        if ($len == 0) { 
            return true; 
        } 
        return (substr($string, -$len) === $endString); 
    } 

    function url_get_id_video($url) {
        //https://www.youtube.com/watch?v=xeu5CLX8ngY&list=RDHC3GlU9FjVQ&index=2" 
        //https://www.youtube.com/watch?v=HC3GlU9FjVQ&list=RDMMxeu5CLX8ngY&index=5
        //https://www.youtube.com/watch/seiLaOQue/WzZ1TJK5TOg
        $url = str_replace('http://', '', $url);
        $url = str_replace('https://', '', $url);
        $url = str_replace('?v=', '/', $url);
        $url = str_replace('&list', '/', $url);
        $url = str_replace('&index=', '/', $url); //www.youtube.com/watch/HC3GlU9FjVQ/RDMMxeu5CLX8ngY/5 
        $p = explode('/', $url);                  //       0          1        2            3         4
        return $p[2];
    }

    function downloadXLS($filename_prefix, $dados) {
        $arquivo = $filename_prefix . uniqid() . ".xls";
        
        header('Cache-Control: no-cache, must-revalidate'); 
        header('Pragma: no-cache');
        header('Content-Type: application/x-msexcel; charset=uft-8');
        header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
        
        echo $dados;
    }
    
    function downloadXML($filename, $dados, $forceDownload = FALSE) {
        $arquivo = $filename . ".xml";
        
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Content-Type: application/xml; charset=uft-8');
        
        if ($forceDownload) {
            header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
        }
        
        echo $dados;
    }
    
    function is5_hora_valida($hora) {
        return isHora($hora);
    }
    
    function isHora($hora) {
        try {
            $arr = explode(':', $hora);
            
            if (is5_count($arr) !== 2) {
                return FALSE;
            }
            
            $h = (int)$arr[0];
            $m = (int)$arr[1];
            
            if ($h < 0 || $h > 23) {
                return FALSE;
            }
            
            if ($m < 0 || $m > 59) {
                return FALSE;
            }
            
            return TRUE;
            
        } catch (Exception $ex) {
            return FALSE;
        }        
    }

    function is5_data_valida_br($date) {
        if (empty($date)) {
            return false;
        }
        $data = explode("/", $date); 
        
        if (is5_count($data) !== 3) {
            return 0;
        }
        
	$d = $data[0];
	$m = $data[1];
	$y = $data[2];
        
	// verifica se a data é válida!
	// 1 = true (válida)
	// 0 = false (inválida)
	return checkdate($m, $d, $y);	
    }
    
    function is5_data_valida_db($date) {
        if ($date === '0000-00-00') {
            return false;
        }
        
        if (empty($date)) {
            return false;
        }
        $data = explode("-", $date); 
        
        if (is5_count($data) !== 3) {
            return 0;
        }
        
        //yyyy-mm-dd
	$d = $data[2];
	$m = $data[1];
	$y = $data[0];
        
	// verifica se a data é válida!
	// 1 = true (válida)
	// 0 = false (inválida)
	return checkdate($m, $d, $y);	
    }

    function getEmbed($url) {
        $u = str_replace('youtube.com/watch?v=', 'youtube.com/embed/', $url); //https://www.youtube.com/watch?v=gVmmlR8XfWc 
        $u = str_replace('youtu.be/', 'youtube.com/embed/', $u); //https://youtu.be/gVmmlR8XfWc
        $u = str_replace('&feature=youtu.be', '', $u);
        $u = str_replace('http://','https://', $u);
        $a = explode('&',$u); //remove qualquer outro &var q tiver
        if (is5_count($a)) {
            return $a[0];
        }
        return $u;        
    }
    
    function getThumbVideo($url) {
        //https://stackoverflow.com/questions/2068344/how-do-i-get-a-youtube-video-thumbnail-from-the-youtube-api
        //https://img.youtube.com/vi/<ID VIDEO>/sddefault.jpg
        $u = getEmbed($url);
        $u = str_replace('youtube.com/embed/', 'img.youtube.com/vi/', $u) . '/mqdefault.jpg';
        $u = str_replace('www.', '', $u);
        return $u;        
    }

    function is5_strtoupper($str) {
        //apt-get install php-mbstring
        return mb_strtoupper($str, 'UTF-8');
    }
    
    function is5_strtolower($str) {
        //apt-get install php-mbstring
        return mb_strtolower($str, 'UTF-8');
    }

    function get_text_limit($str, $limit, $reticencias = TRUE) {
        //trunca uma string no limite informado
        //mas retorna do ultimo espaco em branco para o inicio,
        //para nao deixar texto quebrado.    
        if (strlen($str) > $limit) {
            $str = substr($str, 0, $limit);
            for($i = strlen($str)-1; $i >= 0; $i--) {
                if ($str[$i] === ' ') {
                    $ret = substr($str, 0, $i);
                    return ($reticencias) ? "$ret..." : $ret;
                }
            }
            return $str;
        } else {
            return $str;
        }        
    }

    function get_current_url() {
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $url;
    }
    
    function getIP(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    function write_text_img($img_origem, $img_destino, $text, $x = '100', $y = '100') {
        $CI =& get_instance();
        
        $info = pathinfo($img_origem);
        //$nova_imagem = $info['dirname'] . '/certificados/' . $info['filename'] . '_' . uniqid() . '.png';
        //$nova_imagem = $info['dirname'] . '/certificados/' . $info['filename'] . '_5d4d9f5a2b16a.png';
        
        $config['image_library'] = 'GD2';
        $config['source_image'] = $img_origem;
        
        if ($img_origem !== $img_destino) {
            $config['new_image'] = $img_destino;
        }
        
        $config['wm_hor_offset'] = $x; // '-240'; //deslocamento horizontal (em pixels)
        $config['wm_vrt_offset'] = $y; // '1530'; //deslocamento vertical (em pixels)
        
        $config['wm_text'] = $text;
        $config['wm_type'] = 'text';
        $config['wm_font_path'] = '/var/www/html/drebes/fontes/BellMT.ttf';
        $config['wm_font_size'] = '40';
        $config['wm_font_color'] = '242B40';
               
        //$config['wm_vrt_alignment'] = 'bottom';
        //$config['wm_hor_alignment'] = 'center';
        //$config['wm_padding'] = '20';
                
        $CI->load->library('image_lib',$config);
        $CI->image_lib->initialize($config);
        
        if (!$CI->image_lib->watermark()) {
            echo "Erro: " . $CI->image_lib->display_errors() . "<br>";
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function isMobile() {
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
            return TRUE; //"EH MOBILE";
        else 
            return FALSE; // "NAO EH MOBILE";
    }

    function normalizar_post($str, $lower = FALSE) {
        if ($lower) {
            return addslashes(is5_strtolower($str)); //retirei o remover_acentos() 
        } else {
            return addslashes(is5_strtoupper($str)); 
        }        
    }
    
    function get_rootdir() {
        $root = str_replace('application', '', APPPATH);
        return str_replace('//', '/', $root);
    }
    
    function dir_home_exist($home) {
        $root = get_rootdir(); 
        $dir  = $root.$home; 
        $dir  = str_replace('//', '/', $dir);
        
        return is_dir($dir);
    }
    
    function criar_home($home) {
        //cria diretorio com um index.html e permissoes default
        try {            
            $root = get_rootdir(); 
            $dir = $root.$home; 
            $dir = str_replace('//', '/', $dir);            
            $ret = mkdir($dir, 0755);
            if ($ret) {                
                $filename = $dir.'/index.html'; 
                $filename = str_replace('//', '/', $filename);
                $content = file_get_contents($root.'assets/index.html');
                file_put_contents($filename, $content);                
            } 
            return $dir;
        } catch (Exception $ex) {
            return FALSE;
        }
    }
    
    function criar_diretorio($dir, $home = '') {
        //cria diretorio com um index.html e permissoes default
        try {
            $root = get_rootdir(); 
            if ($home !== '') {
                $dir = $root.$home.$dir;
            } else {
                $dir = $root."home/".$dir;
            }            
            $ret = mkdir($dir, 0755);
            if ($ret) {                
                $filename = $dir.'/index.html'; 
                $filename = str_replace('//', '/', $filename);
                $content = file_get_contents($root.'assets/index.html');
                file_put_contents($filename, $content);                
            } 
            return $dir;
        } catch (Exception $ex) {
            return FALSE;
        }
    }

    function array2string($data) {
        //https://stackoverflow.com/questions/7490488/array-to-string-php
        $log_a = "";
        foreach ($data as $key => $value) {
            if(is_array($value))    
                $log_a .= array2string($value). "<br>";
            else
                $log_a .= $value . "<br>";
        }
        return $log_a;
    }

    function Zeros($input, $size) {
        //add zeros a eskerda
        return str_pad($input, $size, '0', STR_PAD_LEFT);
    }

    function strToFloat($valor, $casas_decimais = 2) {
        /* transforma uma variavel em float com duas casas decimais.
         * sempre que for realizar operacao matematica em um valor em PHP, utilizar
         * essa funcao para converter $var em um tipo valido.
         * 
         * Fonte: http://php.net/manual/pt_BR/function.number-format.php
         */
        if($valor) {
            return number_format($valor, $casas_decimais, '.','');
        }        
    }
    
    function getDateTimeZone() {
        //date_default_timezone_set('America/Sao_Paulo');
        return date(DATE_RFC3339);
    }
    
    function getDateCurrent() {
        //date_default_timezone_set('America/Sao_Paulo');
        return date('Y-m-d');
    }
    
    function getTimeCurrent($seconds = FALSE) {
        //date_default_timezone_set('America/Sao_Paulo');
        if ($seconds) {
            return date('H:i:s');
        }
        return date('H:i');
    }
    
    function getDateCurrentBr() {
        //date_default_timezone_set('America/Sao_Paulo');
        return date('d/m/Y');
    }
    
    function getDateTimeCurrent() {
        //date_default_timezone_set('America/Sao_Paulo');
        return date('Y-m-d H:i:s');
    }
    
    function getDateTimeCurrentBr() {
        //date_default_timezone_set('America/Sao_Paulo');
        return date('d/m/Y H:i:s');
    }
    
    
    function floatToStr($valor) {
        //utilizado somente para mostrar dados.
        return valorToBr($valor);
    }
    
    function dateIncDay($data, $dias) {
        /* Outra forma de realizar essa tarefa é utlizar a classe DateTime e passar o período 
         * a ser adicionado a data, ele é especificado no construtor de DateInterval, 
         * deve iniciar com P (de periodo) e seguido de um número por último a unidade:
         
            Y | Ano
            M | Mês
            D | Dias
            W | Semanas
            H | Horas
            M | Minutos
            S | Segundos  
          
            $data = '17/11/2014';

            $data = DateTime::createFromFormat('d/m/Y', $data);
            $data->add(new DateInterval('P2D')); // 2 dias
            echo $data->format('d/m/Y');
          
            Fonte: https://pt.stackoverflow.com/questions/40731/adicionar-dias-%C3%A0-uma-data
         */
        
        $d = DateTime::createFromFormat('d/m/Y', $data);
        $d->add(new DateInterval('P'.$dias.'D'));
        return $d->format('d/m/Y');        
    }
    
    function dateFormatBeforeSave($dateString) {
        return date('Y-m-d', strtotime($dateString)); // Direction is from
    }
    
    function dateFormatAfterSave($dateString) {
        return date('d/m/Y', strtotime($dateString)); // Direction is from
    }
    
    function dateTimeFormatBeforeSave($dateString) {
        if ($dateString) {
            return date('Y-m-d H:i:s', strtotime($dateString)); // Direction is from
        }else {
            return $dateString;
        }        
    }
    
    function getDataAtualExtenso() {
        $mes = ['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro',
                'outubro','novembro', 'dezembro'];
        
        return date('d') . ' de ' . $mes[(int)date('m')-1] . ' de ' . date('Y');
    }
    
    function dateTimeFormatAfterSave($dateString) {
        if ($dateString) {
            return date('d/m/Y H:i:s', strtotime($dateString)); // Direction is from
        }else {
            return $dateString;
        }
    }
    
    function dateToMySQL($date)
    {
        if (!empty($date) && strlen($date) >= 10)
        {

            //0123456789    0123456789
            //dd/mm/yyyy -> yyyy-mm-dd
            $datec = $date[6].$date[7].$date[8].$date[9].'-'.$date[3].$date[4].'-'.$date[0].$date[1];
            
            if (strlen($date) == 19) {
                //0123456789 11 12 13 14 15 16 17 18  0123456789
                //dd/mm/yyyy 0  0  :  0  0  :  0  0   -> yyyy-mm-dd 00:00:00
                $datec .= substr($date, 10, 8);
            }
            
            return($datec);            
        } else {
            return $date;
        }
    }   
    
    function dateToDb($date) {
        return dateToMySQL($date);
    }
    
    function dateToBr($date)
    {
        if (!empty($date) && strlen($date) >= 10)
        {
            //0123456789    0123456789
            //yyyy-mm-dd -> dd/mm/yyyy
            $datec = $date[8].$date[9].'/'.$date[5].$date[6].'/'.$date[0].$date[1].$date[2].$date[3];
            
            if (strlen($date) == 19) {
                //0123456789 11 12 13 14 15 16 17 18  0123456789
                //dd/mm/yyyy 0  0  :  0  0  :  0  0   -> yyyy-mm-dd 00:00:00
                $datec .= substr($date, 10, 9);
            }
            
            return($datec);
        } else {
            return $date;
        }
    }
    
    /*function valorToMySQL($valor) {
        if ($valor) {
            $valor = str_replace('.', '', $valor);
            return str_replace(',', '.', $valor);
        }        
    }*/
    
    function valorToDb($valor) {
        if ($valor) {
            $valor = str_replace('.', '', $valor);
            return str_replace(',', '.', $valor);
        } else {
            return $valor;
        }  
    }
    
    function valorToBr($valor) {
        if (empty($valor)) {
            $valor = '0.00';
        }
        //trocar '.' por ','
        //return str_replace('.', ',', $valor);
        if (is_numeric($valor)) {
            return number_format($valor, 2, ',', '.'); //(numero, casas decimais, separador_decimal, separador_milhar)
        } else {
            return $valor;
        }        
    }
    
    function anti_sql_injection($str) 
    {
        //fonte: http://www.maisumblog.com/2009/02/06/como-evitar-ataques-de-sql-injection-no-php-e-mysql/
        if (!is_numeric($str)) 
        {
            $str = get_magic_quotes_gpc() ? stripslashes($str) : $str;
            $str = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($str) : mysql_escape_string($str);

            //agora soh pra reforcar - I.S.V
            $str = strip_tags($str); //tira tags html e php
            $str = addslashes($str); //Adiciona barras invertidas a uma string
        }
        return $str;
    }
	
    function anti($str) 
    {
        if (!is_numeric($str)) 
        {
            $str = get_magic_quotes_gpc() ? stripslashes($str) : $str;
            $str = strip_tags($str); //tira tags html e php
            $str = addslashes($str); //Adiciona barras invertidas a uma string
        }
        return $str;
    }
	
    function sqli($str)
    {
            return anti_sql_injection($str);
    }
    
    /**
    * Remove espacos de uma string
    * @param string $texto Texto a ser filtrado
    * @return string Texto sem espacos
    */
   function remover_espacos($texto) {
       return preg_replace('/\s/', '', $texto);
   }


   /**
    * Remove acentos de uma string
    * @param string $texto Texto a ser filtrado
    * @return string Texto sem acentos
    */
   function remover_acentos($string) {
        //return iconv($charset, 'ASCII//TRANSLIT', $string);
        $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ü', 'Ú');
        $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');

        return str_replace($comAcentos, $semAcentos, $string);      
   }
   
   /* gera thumbnail
    * utilizacao: <img src="imagem.php?img=imagens/familia.gif&x=120&y=40&q=90" />
    * Fonte: http://codigofonte.uol.com.br/codigos/gerando-thumbnails-de-imagens-com-php
    */
    function gera_thumb($nome_img, $lar_maxima, $alt_maxima, $qualidade) 
    { 
        if($qualidade == ''){ $qualidade = 100; }

        $size = getimagesize($nome_img);
        $tipo = $size[2];

        # Pega onde está a imagem e carrega	 
        if($tipo == 2){ // 2 é o JPG
        $img = imagecreatefromjpeg($nome_img);	   
        } if($tipo == 1){ // 1 é o GIF
        $img = imagecreatefromgif($nome_img);	   
        } if($tipo == 3){ // 3 é PNG
        $img = imagecreatefrompng($nome_img);	   
        }


        // Se a imagem foi carregada com sucesso, testa o tamanho da mesma
        if ($img) {
                 // Pega o tamanho da imagem e proporção de resize
                 $width  = imagesx($img);
                 $height = imagesy($img);
                 $scale  = min($lar_maxima/$width, $alt_maxima/$height);

                 // Se a imagem é maior que o permitido, encolhe ela!
                 if ($scale < 1) {
                         $new_width  = floor($scale*$width);
                         $new_height = floor($scale*$height);

                         // Cria uma imagem temporária
                         $tmp_img = imagecreatetruecolor($new_width, $new_height);

                         // Copia e resize a imagem velha na nova
                         imagecopyresampled ($tmp_img, $img, 0, 0, 0, 0,
                         $new_width, $new_height, $width, $height);

                        // imagedestroy($img);
                         $img = $tmp_img;

                  }

        }	

        header("Content-type:image/gif");

        imagejpeg($img,'',$qualidade);

        imagedestroy($img);

    }
    
    function normalizar_filename($string){
       // pegando a extensao do arquivo
       $partes 	= explode(".", $string);
       $extensao 	= $partes[is5_count($partes)-1];	
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

    //reescrevi nessa funcao pra nao procurar por extensao e preservando lower e upper
    function normalizar_string($str){
       // removendo simbolos, acentos etc
       $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýýþÿŔŕ?';
       $b = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYBSaaaaaaaceeeeiiiidnoooooouuuuyybyRr-';
       $str = strtr($str, utf8_decode($a), $b);
       $str = str_replace(".","-",$str);
       $str = preg_replace( "/[^0-9a-zA-Z\.]+/",'-',$str);
       return utf8_decode($str);
    }
    
    function somenteLetrasNumeros($str){
       // removendo simbolos, acentos etc
       $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýýþÿŔŕ?';
       $b = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYBSaaaaaaaceeeeiiiidnoooooouuuuyybyRr-';
       $str = strtr($str, utf8_decode($a), $b);       
       $str = preg_replace( "/[^0-9a-zA-Z\.]+/",'',$str);
       return utf8_decode($str);
    }
    
    function get_img_locate($path_server) {
        //retorna o path da imagem no servidor
        //http://www.is5.com.br/Uploads/ivan/imagem123.png
        //retorna: /Uploads/ivan/imagem123.png
        $pos = strpos($path_server, "/Uploads/");
	return substr($path_server, $pos);
    }
    
    function get_img_path($locate) {
        if (isset($locate) && ($locate != '')) {
            //retorna a localizacao com base na url
            $locate = get_img_locate($locate); //para casos de migracao
            if ($locate[0] == "/") {
                $locate = substr($locate, 1);
            }
            return base_url($locate);
        }else {
            return base_url('/Uploads/shared/sem_imagem.jpg');
        }        
    }
    
    function mascara($val, $mask)
    {
        /*
         * Fonte: http://blog.clares.com.br/php-mascara-cnpj-cpf-data-e-qualquer-outra-coisa/
         * 
         * Exemplo
         *  $cnpj = "11222333000199";
            $cpf = "00100200300";
            $cep = "08665110";
            $data = "10102010";

            echo mask($cnpj,'##.###.###/####-##');
            echo mask($cpf,'###.###.###-##');
            echo mask($cep,'#####-###');
            echo mask($data,'##/##/####');
         */
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++)
        {
            if($mask[$i] == '#')
            {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            }
            else
            {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }
        
    function is5_enviar_email($destino, $assunto, $mensagem) {
        $destino = urlencode($destino);
        $assunto = urlencode($assunto);
        $mensagem = urlencode($mensagem);
        
        $urlmail = "http://www.is5.com.br/sendmailgeneric.php?destino=$destino&assunto=$assunto&mensagem=$mensagem";
        
        $retorno = file_get_contents($urlmail);
        
        return ($retorno == "sucesso");
    }
    
    function formata_cgc($cgc) {
        $cgc = somenteNumeros($cgc);
        switch(strlen($cgc)) {
            case 11:
                return mascara($cgc, "###.###.###-##"); 
            case 14:
                return mascara($cgc, "##.###.###/####-##");
            default:
                return $cgc;
        }
    }
    
    function formata_cep($cep) {
        $cgc = somenteNumeros($cep);
        if ($cgc !== '') {
            return mascara($cep, "#####-###");                 
        } 
        return '';        
    }
    
    function formata_celular($celular){
        $celular = somenteNumeros($celular);        
        if (($celular) && (strlen($celular) == 10)) {
            return mascara($celular, '(##)####-####');            
        }else if (($celular) && (strlen($celular) == 11)) {
            return mascara($celular, '(##)#####-####');            
        }
        else {
            return $celular;
        }
    }

    function validar_url($url, $verificar_dns = False) {
        $format_ok = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED);
        if ($verificar_dns) {
            if ($format_ok) {
                $host = explode("://", $url);
                return checkdnsrr($host[1],'A');
            }else {
                return False;
            }
        }else {
            return $format_ok;
        }
    }

    function validar_email($email){
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    }
        
    function embranco($str) {
        return (empty($str) || trim($str) == "");
    }

    function somenteNumeros($str) {
        return preg_replace("/[^0-9]/", "", $str);
    }
    
    function valida_cgc($cgc) {
        return (valida_cpf($cgc) || valida_cnpj($cgc));
    }
    
    //fonte: http://www.geradorcpf.com/script-validar-cpf-php.htm
    function valida_cpf($cpf = null) {
        // Verifica se um número foi informado
        if(empty($cpf)) {
            return false;
        }

        // Elimina possivel mascara
        $cpf = somenteNumeros($cpf);
        
        // Verifica se o numero de digitos informados é igual a 11 
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verifica se nenhuma das sequências invalidas abaixo 
        // foi digitada. Caso afirmativo, retorna falso
        else if ($cpf == '00000000000' || 
            $cpf == '11111111111' || 
            $cpf == '22222222222' || 
            $cpf == '33333333333' || 
            $cpf == '44444444444' || 
            $cpf == '55555555555' || 
            $cpf == '66666666666' || 
            $cpf == '77777777777' || 
            $cpf == '88888888888' || 
            $cpf == '99999999999') {
            return false;
         // Calcula os digitos verificadores para verificar se o
         // CPF é válido
         } else {   
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) {
                    return false;
                }
            }
            return true;
        }
    }
    
    /**
    * Valida CNPJ
    *
    * @author Luiz Otávio Miranda <contato@todoespacoonline.com/w>
    * @param string $cnpj 
    * @return bool true para CNPJ correto
    *
    */
   function valida_cnpj ( $cnpj ) {
       // Deixa o CNPJ com apenas números
       $cnpj = somenteNumeros($cnpj); 
       
       // Verifica se o numero de digitos informados é igual a 14
        if (strlen($cnpj) != 14) {
            return false; 
        }

       // Garante que o CNPJ é uma string
       $cnpj = (string)$cnpj;

       // O valor original
       $cnpj_original = $cnpj;

       // Captura os primeiros 12 números do CNPJ
       $primeiros_numeros_cnpj = substr( $cnpj, 0, 12 );

       /**
        * Multiplicação do CNPJ
        *
        * @param string $cnpj Os digitos do CNPJ
        * @param int $posicoes A posição que vai iniciar a regressão
        * @return int O
        *
        */
       if ( ! function_exists('multiplica_cnpj') ) {
           function multiplica_cnpj( $cnpj, $posicao = 5 ) {
               // Variável para o cálculo
               $calculo = 0;

               // Laço para percorrer os item do cnpj
               for ( $i = 0; $i < strlen( $cnpj ); $i++ ) {
                   // Cálculo mais posição do CNPJ * a posição
                   $calculo = $calculo + ( $cnpj[$i] * $posicao );

                   // Decrementa a posição a cada volta do laço
                   $posicao--;

                   // Se a posição for menor que 2, ela se torna 9
                   if ( $posicao < 2 ) {
                       $posicao = 9;
                   }
               }
               // Retorna o cálculo
               return $calculo;
           }
       }

       // Faz o primeiro cálculo
       $primeiro_calculo = multiplica_cnpj( $primeiros_numeros_cnpj );

       // Se o resto da divisão entre o primeiro cálculo e 11 for menor que 2, o primeiro
       // Dígito é zero (0), caso contrário é 11 - o resto da divisão entre o cálculo e 11
       $primeiro_digito = ( $primeiro_calculo % 11 ) < 2 ? 0 :  11 - ( $primeiro_calculo % 11 );

       // Concatena o primeiro dígito nos 12 primeiros números do CNPJ
       // Agora temos 13 números aqui
       $primeiros_numeros_cnpj .= $primeiro_digito;

       // O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
       $segundo_calculo = multiplica_cnpj( $primeiros_numeros_cnpj, 6 );
       $segundo_digito = ( $segundo_calculo % 11 ) < 2 ? 0 :  11 - ( $segundo_calculo % 11 );

       // Concatena o segundo dígito ao CNPJ
       $cnpj = $primeiros_numeros_cnpj . $segundo_digito;

       // Verifica se o CNPJ gerado é idêntico ao enviado
       if ( $cnpj === $cnpj_original ) {
           return true;
       }
   }
   
    //TRANSFORMAR IMAGEM EM BASE64 PARA SER TRANSFERIDA POR JSON
    function imgTo64($image) {
        $type = pathinfo($image, PATHINFO_EXTENSION);
        $data = file_get_contents($image);
        $base64 = "data:image/$type;base64," . base64_encode($data);
        return $base64;
    }
    
    function getMes($mes) {
        $i = (int)$mes;
        
        switch($i) {
            case 1: return 'JANEIRO';
            case 2: return 'FEVEREIRO';
            case 3: return 'MARÇO';
            case 4: return 'ABRIL';
            case 5: return 'MAIO';
            case 6: return 'JUNHO';
            case 7: return 'JULHO';
            case 8: return 'AGOSTO';
            case 9: return 'SETEMBRO';
            case 10: return 'OUTUBRO';
            case 11: return 'NOVEMBRO';
            case 12: return 'DEZEMBRO';
        }
    }
    
    function get_thumb($src, $width, $height) {
        if (empty($src)) {
            $src="SEM_IMAGEM";
        }
        $src = base64_encode($src);
        $src = str_replace("=", "", $src);
        return base_url() . "Thumbnail/resize/$width/$height/$src";
    }
    
    function isB64($info) {
        return substr_count($info, 'data:image/') > 0;
    }
    
    function extractB64($imagem) {
        //data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAIAAAAiOjnJAAAAAXNSR0IArs4c6QAAA
        $arr = explode(',', $imagem);
        if (is5_count($arr) == 2) {
            return $arr[1];
        } 
        return '';
    }
    
    /**
     * Verifica se o usuário está autenticado
     * Se não estiver e for uma requisição AJAX, retorna JSON com erro
     * Se não estiver e não for AJAX, redireciona para login
     * 
     * @param object $CI CodeIgniter instance
     * @return bool true se autenticado, false caso contrário
     */
    function check_authenticated(&$CI) {
        if ($CI->session->userdata('logado') !== TRUE) {
            // Se for requisição AJAX, retorna JSON
            if ($CI->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => FALSE, 'msg' => 'Não autenticado', 'data' => []]);
                exit;
            }
            // Caso contrário, redireciona para login
            redirect(base_url('Account/login'));
            return false;
        }
        return true;
    }
    