<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index() {
        echo "=== TESTE DE PASSAPORT COM DEBUG DETALHADO ===\n\n";
        
        // Simular uma chamada ao Passport
        $this->load->model('Pessoas_model');
        $this->load->model('PessoaLicencas_model');
        
        $cgc = '92702067000181';
        $hostname = 'TEST-HOST-' . time();
        $guid = 'TEST-GUID-' . time();
        
        echo "Testando com:\n";
        echo "  CGC: $cgc\n";
        echo "  Hostname: $hostname\n";
        echo "  GUID: $guid\n\n";
        
        // Passo 1: Buscar pessoa
        echo "1. Buscando pessoa com CGC: $cgc\n";
        $pessoa = $this->Pessoas_model->findByCGC($cgc);
        
        if (!$pessoa) {
            echo "✗ Pessoa não encontrada\n";
            return;
        }
        
        echo "✓ Pessoa encontrada: ID=" . $pessoa->ID_PESSOA . "\n\n";
        
        // Passo 2: Tentar gravar licença
        echo "2. Tentando gravar licença...\n";
        
        $dados_licenca = [
            'ID_PESSOA'  => $pessoa->ID_PESSOA,
            'HOSTNAME'   => addslashes($hostname),
            'GUID'       => addslashes($guid),
            'CREATED_AT' => date('Y-m-d H:i:s'),
            'LAST_LOGIN' => date('Y-m-d H:i:s')
        ];
        
        echo "Dados a gravar:\n";
        echo "  " . json_encode($dados_licenca) . "\n\n";
        
        $resultado = $this->PessoaLicencas_model->gravar($dados_licenca);
        
        echo "Resultado da gravação: ";
        var_dump($resultado);
        echo "\n";
        
        // Passo 3: Verificar se foi realmente gravado
        echo "3. Verificando se foi gravado no banco...\n";
        
        $check = $this->db->get_where('PESSOA_LICENCAS', [
            'ID_PESSOA' => $pessoa->ID_PESSOA,
            'GUID'      => $guid
        ])->result_array();
        
        if (!empty($check)) {
            echo "✓ SUCESSO! Licença foi gravada:\n";
            foreach ($check as $row) {
                echo "  " . json_encode($row) . "\n";
            }
        } else {
            echo "✗ FALHA! Licença NÃO foi gravada no banco de dados.\n";
            echo "\nVerificando quantos registros existem em PESSOA_LICENCAS:\n";
            
            $count = $this->db->count_all('PESSOA_LICENCAS');
            echo "  Total: $count\n";
            
            if ($count > 0) {
                $recent = $this->db->order_by('ID', 'DESC')->limit(3)->get('PESSOA_LICENCAS')->result_array();
                echo "\n  Últimos 3 registros:\n";
                foreach ($recent as $row) {
                    echo "    " . json_encode($row) . "\n";
                }
            }
        }
        
        // Passo 4: Verificar logs
        echo "\n4. Verificando arquivo de log...\n";
        $logDir = APPPATH . 'logs/';
        if (is_dir($logDir)) {
            $files = scandir($logDir);
            rsort($files);
            
            $logFile = $logDir . $files[0];
            echo "Arquivo de log: " . $files[0] . "\n\n";
            
            $content = file_get_contents($logFile);
            $lines = explode("\n", $content);
            
            $debugLines = [];
            foreach (array_reverse($lines) as $line) {
                if (stripos($line, 'DEBUG') !== false || stripos($line, 'PESSOA_LICENCAS') !== false) {
                    $debugLines[] = $line;
                    if (count($debugLines) >= 20) break;
                }
            }
            
            if (!empty($debugLines)) {
                echo "Últimas linhas de DEBUG:\n";
                foreach (array_reverse($debugLines) as $line) {
                    echo $line . "\n";
                }
            }
        }
    }
}
