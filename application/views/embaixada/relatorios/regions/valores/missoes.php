<?php 
    if (isset($missoes) && is5_count($missoes) > 0) {
        ?>
        <br>
        <table class="ttable">
            <thead>
                <tr>
                    <th colspan="2" class="titulo">OFERTAS MISSÕES</th>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Tipo de Entrada</td>
                    <td style="text-align: right; font-weight: bold;">Valor</td>
                </tr>                        
            </thead>
            <tbody>                        
                <?php 
                    foreach($missoes as $d) {
                        ?>
                        <tr>                                    
                            <td >
                                <?= $d->forma_pag ?>
                            </td>
                            <td style="width: 150px; text-align: right;">
                                R$ <?= valorToBr($d->total) ?>
                            </td>
                        </tr>
                        <?php
                    }
                ?>                        
            </tbody>
        </table>   
        <table class="ttable">                    
                <tr>
                <?php 
                    foreach($SIGLAS_FP as $fp) {
                        if ($missoes_fp[$fp] > 0.00):
                        ?>
                        <td style="width: 100px; background: #f8fafd; <?= ($fp === 'TOTAL') ? 'text-align: right;' : '' ?>">
                            <div class="llabel"><?= edf_fp_descricao($fp) ?></div>
                            <div class="vvalor">
                                <strong>R$ <?= valorToBr($missoes_fp[$fp]) ?></strong>
                            </div>
                        </td>    
                        <?php
                        endif;
                    }
                ?>
                </tr>
        </table>   
        <?php
    }
?>