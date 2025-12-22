<?php 
    if (!pode_ler('relatorios', FALSE)) {
        echo '<br><center>Sem permissão para acessar este recurso!</center>';
        return false;
    }
    
    if (!isset($diario)) {
        die('Registro não retornado.');
    }
    
    $dizimos_fp   = edf_totais_fp_dizimos($diario->id);
    $ofertas_fp   = edf_totais_fp_ofertas($diario->id);
    $especiais_fp = edf_totais_fp_especiais($diario->id);
    $missoes_fp   = edf_totais_fp_missoes($diario->id);
          
    $SIGLAS_FP = edf_lista_siglas_fp();
    $SIGLAS_FP[] = 'TOTAL';
    
    $totais = edf_get_totais_diario($diario->id);    
?>
<html>
    <head>
        <title>RELATÓRIO DIÁRIO</title>
        <style>
            .ttable {
                table-layout: fixed;
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #ddd;
                font-size: 14px;
            }

            .ttable > th, td {
                padding: 5px;
                border: 1px solid #ddd;
            }
            
            .llabel {
                /*text-transform: uppercase;*/
            }
            
            .vvalor {
                text-align: right;
            }
        </style>
    </head>
    <body>
        <?= get_relat_banner_filial($diario->filial_id); ?>
        <br>
        <table class="ttable" style="font-size: 14px!important;">
            <thead>
                <tr>
                    <th colspan="4" style="text-align: left;">DADOS BÁSICOS</th>
                </tr>            
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left; width: 200px;">
                        <div class="llabel">Data</div>
                        <?= dateToBr($diario->data) ?>
                    </td>
                    <td style="text-align: left; width: 100px;">
                        <div class="llabel">Hora</div>
                        <?= $diario->hora ?>
                    </td>
                    <td style="text-align: left; width: 100px;">
                        <div class="llabel">Adultos</div>
                        <?= $diario->adultos ?>
                    </td>
                    <td style="text-align: left; width: 25%;">
                        <div class="llabel">Crianças (até 12 anos)</div>
                        <?= $diario->criancas_ate12 ?>
                    </td>
                    <td style="text-align: left; width: 25%;">
                        <div class="llabel">Total Pessoas</div>
                        <?= $diario->total_pessoas ?>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="3">
                        <div class="llabel">Pregador</div>
                        <?= $diario->pregador ?>
                    </td>
                    <td style="text-align: left; width: 25%;">
                        <div class="llabel">Visitantes</div>
                        <?= $diario->visitantes ?>
                    </td>
                    <td style="text-align: left; width: 25%;">
                        <div class="llabel">Conversões</div>
                        <?= $diario->conversoes ?>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- PASTORES -->
        <?php 
            include_once APPPATH . 'views/embaixada/relatorios/regions/pessoas/pastores.php';
        ?>
                
        <!-- DIACONOS -->
        <?php 
            include_once APPPATH . 'views/embaixada/relatorios/regions/pessoas/diaconos.php';
        ?>
                
        <!-- VISITANTES -->
        <?php 
            include_once APPPATH . 'views/embaixada/relatorios/regions/pessoas/visitantes.php';
        ?>
                
        <!-- APRESENTACOES -->
        <?php 
            include_once APPPATH . 'views/embaixada/relatorios/regions/pessoas/apresentacoes.php';
        ?>
                
        <!-- DIZIMOS -->
        <?php
            include_once APPPATH . 'views/embaixada/relatorios/regions/valores/dizimos.php';
        ?>
                
        <!-- OFERTAS -->
        <?php
            include_once APPPATH . 'views/embaixada/relatorios/regions/valores/ofertas.php';
        ?>
                
        <!-- OFERTAS ESPECIAIS -->
        <?php
            include_once APPPATH . 'views/embaixada/relatorios/regions/valores/especiais.php';
        ?>
                
        <!-- OFERTAS MISSOES -->
        <?php
            include_once APPPATH . 'views/embaixada/relatorios/regions/valores/missoes.php';
        ?>
        
        <br>
        <table class="ttable">
            <tr>
                <td colspan="6" style="font-weight: bold;">
                    TOTAL POR FORMAS DE PAGAMENTO
                </td>
            </tr>
                <tr>
                <?php 
                    foreach($totais['pagamentos'] as $fp) {
                        ?>
                        <td>
                            <div class="llabel"><?= $fp['nome'] ?></div>
                            <div class="vvalor">
                                R$ <?= valorToBr($fp['valor']) ?>
                            </div>
                        </td>    
                        <?php
                    }
                ?>
                </tr>
        </table>   
                       
        <br>
        <table class="ttable">
            <tr>
                <td colspan="5" style="font-weight: bold;">
                    TOTAL POR ARRECADAÇÃO
                </td>
            </tr>
                <tr>
                <?php 
                    foreach($totais['categorias'] as $fp) {
                        ?>
                        <td>
                            <div class="llabel"><?= $fp['nome'] ?></div>
                            <div class="vvalor">
                                R$ <?= valorToBr($fp['valor']) ?>
                            </div>
                        </td>    
                        <?php
                    }
                ?>
                </tr>
        </table>   
        
    </body>
</html>

