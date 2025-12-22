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
    
    $cidade = '';
    $filial = get_filial($diario->filial_id);
    if ($filial) {
        $cidade = $filial->cidade;
    }
?>
<html>
    <head>

        <title>Relatório Diário</title>        
        <style>
            .ttable {
                table-layout: fixed;
                width: 100%;
                border-collapse: collapse;
                font-size: 11px;
                font-family: "Open Sans",sans-serif;
            }

            .ttable > th, td {
                padding: 5px;
                border-bottom: 1px dotted silver;
                font-family: "Open Sans",sans-serif;
            }
            
            .llabel {
                /*text-transform: uppercase;*/
                font-family: "Open Sans",sans-serif;
            }
            
            .vvalor {
                text-align: right;
                font-family: "Open Sans",sans-serif;
            }
            .titulo {
                padding: 7px 7px 7px 1px;
                text-align: left;
                background-color: #f8fafd;
                font-size: 12px;
                font-family: "Open Sans",sans-serif;
                font-weight: bold;
            }

        </style>

    </head>
    <body>
        <?= get_relat_banner_filial($diario->filial_id); ?>
        <br>
        <table class="ttable">
            <thead>
                <tr>
                    <th class="titulo" colspan="5">DADOS BÁSICOS</th>
                </tr>            
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left; width: 200px;">
                        <div class="llabel">Data</div>
                        <strong><?= dateToBr($diario->data) ?></strong>
                    </td>
                    <td style="text-align: left; width: 100px;">
                        <div class="llabel">Hora</div>
                        <strong><?= $diario->hora ?></strong>
                    </td>
                    <td style="text-align: left; width: 100px;">
                        <div class="llabel">Adultos</div>
                        <strong><?= $diario->adultos ?></strong>
                    </td>
                    <td style="text-align: left; width: 25%;">
                        <div class="llabel">Crianças (até 12 anos)</div>
                        <strong><?= $diario->criancas_ate12 ?></strong>
                    </td>
                    <td style="text-align: left; width: 25%;">
                        <div class="llabel">Total Pessoas</div>
                        <strong><?= $diario->total_pessoas ?></strong>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="3">
                        <div class="llabel">Pregador</div>
                        <strong><?= is5_strtoupper($diario->pregador) ?></strong>
                    </td>
                    <td style="text-align: left; width: 25%;">
                        <div class="llabel">Visitantes</div>
                        <strong><?= $diario->visitantes ?></strong>
                    </td>
                    <td style="text-align: left; width: 25%;">
                        <div class="llabel">Conversões</div>
                        <strong><?= $diario->conversoes ?></strong>
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
                <td class="titulo" colspan="6" style="background-color: #096e97; color: #fff;">
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
                                <strong>R$ <?= valorToBr($fp['valor']) ?></strong>
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
                <td class="titulo" colspan="5" style="background-color: #096e97; color: #fff;">
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
                                <strong>R$ <?= valorToBr($fp['valor']) ?></strong>
                            </div>
                        </td>    
                        <?php
                    }
                ?>
                </tr>
        </table> 
        
        <br><br><br>
        <div style="text-align: right;">
            <?= $cidade ?>, <?= getDataAtualExtenso() ?>
        </div>
        
        <br><br><br>
        <!-- ASSINATURAS -->
        <table border="0" style="width: 100%;">
            <tr>
                <td style="text-align: center; border-bottom: none;">
                    <br><br>_____________________________________
                    <br>
                    <?= is5_strtoupper($diario->usuario_insert) ?>
                    <br>
                    <small>
                    <?= is5_strtoupper($diario->usuario_insert_grupo) ?>
                    </small>
                </td>
                <?php 
                    $j = 1;
                    foreach($diaconos as $d) {
                        if ($j > 1) {
                            $j = 0;
                            echo "</tr><tr>";
                        }
                        ?>                        
                        <td style="text-align: center; border-bottom: none;">
                            <br><br>
                            _____________________________________
                            <br>
                            <?= is5_strtoupper($d->nome) ?>
                            <br>
                            <small>DIÁCONO</small>
                        </td>    
                        <?php
                        $j++;
                    }
                ?>  
            </tr>
        </table>
        
        <?php if((int)$diario->user_edit_id): ?>
        <br><br>
        <div style="text-align: center;">
            Este relatório foi alterado em <?= dateToBr($diario->dh_edit) ?> por <?= is5_strtoupper($diario->usuario_edit) ?> (<?= $diario->usuario_edit_grupo ?>).
        </div>        
        <?php endif; ?>
        
    </body>
</html>

