<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//https://github.com/picqer/php-barcode-generator
//require APPPATH . '../tools/php-barcode-generator/src/BarcodeGenerator.php';
//require APPPATH . '../tools/php-barcode-generator/src/BarcodeGeneratorHTML.php';

function get_filial($filial_id) {
    $CI =& get_instance();
    
    $resp = $CI->db->where(['id' => (int)$filial_id, 'emitente_id' => (int)$CI->session->userdata('emit.id')])
                   ->get('edf_filiais');
    
    if ($resp->num_rows()) {
        return $resp->result_object()[0];
    }
    
    return NULL;
}

function get_relat_banner_filial($filial_id) {
    $CI =& get_instance();
    
    $resp = $CI->db->where(['id' => (int)$filial_id, 'emitente_id' => (int)$CI->session->userdata('emit.id')])
                   ->get('edf_filiais');
    
    if (!$resp->num_rows()) {
        die('Erro ao retornar banner filial.');
    }
    
    $filial = $resp->result_object()[0];
    
    $logo = $CI->session->userdata('emit.logo');
    
    $table = "<table>
                <tr>";  
                if (!empty($logo)) {   
                    $table .= "<td style='border: 0px!important;'>
                        <img src='$logo' style='max-height: 80px'>
                    </td>";
                }    
         $table .=
                "<td style='border: 0px!important;'>
                        <h3>$filial->nome</h3>";
                        if (!empty($filial->cgc)) {
                            $table .= "CNPJ: " . formata_cgc($filial->cgc) . "<br>";    
                        }    
                        $table .=  "Endereço: $filial->endereco $filial->numero<br>
                        Cidade: $filial->cidade/$filial->estado
                    </td>
                </tr>
            </table>";
    
    echo $table;
}


function showPDF($conteudo, $header = NULL, $footer = NULL, $landscap = FALSE) {
    //http://www.universidadecodeigniter.com.br/gerando-pdf-no-codeigniter-com-mpdf/
    $CI =& get_instance();
    
    if ($landscap) {
        /*
         $mpdf = new \Mpdf\Mpdf([
             'mode' => 'utf-8', 
             'format' => [190, 236], 
             'orientation' => 'L']);
         */
        $mpdf = new Mpdf\Mpdf([
                            'mode' => 'utf-8', 
                            'format' => 'A4', 
                            'orientation' => 'L']);
    } else {
        $mpdf = new Mpdf\Mpdf();
    }    
        
    // Define um Cabeçalho para o arquivo PDF
    if ($header) {
        $mpdf->SetHeader($CI->session->userdata('emit.nome') . ' - ' . $header);
    } else {
        $mpdf->SetHeader($CI->session->userdata('emit.nome') . ' - RELATÓRIO');
    }

    // Define um rodapé para o arquivo PDF, nesse caso inserindo o número da
    // página através da pseudo-variável PAGENO
    $mpdf->SetFooter('{PAGENO}');

    // Insere o conteúdo da variável $html no arquivo PDF
    $mpdf->writeHTML($conteudo);  
    
    // Cria uma nova página no arquivo
    //$mpdf->AddPage();

    // Insere o conteúdo na nova página do arquivo PDF
    //$mpdf->WriteHTML('<p><b>Minha nova página no arquivo PDF</b></p>');

    // Gera o arquivo PDF
    $mpdf->Output();        
}