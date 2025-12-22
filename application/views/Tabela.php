<?php
    echo json_encode(['status' => (is_array($data) && is5_count($data) > 0), 
                      'msg'    => 'Registros encontrados: '. is5_count($data), 
                      'data'   => $data]);
?>