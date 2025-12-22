<?php 
    if (isset($dizimos) && is5_count($dizimos) > 0) {
        ?>
        <br>
        <table class="ttable">
            <thead>
                <tr>
                    <th colspan="3" class="titulo">RELAÇÃO DE DIZIMISTAS</th>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Nome</td>
                    <td style="font-weight: bold;">Tipo de Entrada</td>
                    <td style="text-align: right; font-weight: bold;">Valor</td>
                </tr>                        
            </thead>
            <tbody>                        
                <?php 
                    foreach($dizimos as $d) {
                        ?>
                        <tr>
                            <td>
                                <?= is5_strtoupper($d->nome) ?>
                            </td> 
                            <td>
                                <?= $d->forma_pag ?>
                            </td>
                            <td style="width: 150px; text-align: right;">
                                R$ <?= valorToBr($d->valor) ?>
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
                        if ($dizimos_fp[$fp] > 0.00):
                        ?>
                        <td style="width: 100px; background: #f8fafd; <?= ($fp === 'TOTAL') ? 'text-align: right;' : '' ?>">
                            <div class="llabel"><?= edf_fp_descricao($fp) ?></div>
                            <div>
                                <strong>R$ <?= valorToBr($dizimos_fp[$fp]) ?></strong>
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

