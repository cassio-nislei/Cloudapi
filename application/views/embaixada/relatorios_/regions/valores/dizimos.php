<?php 
    if (isset($dizimos) && is5_count($dizimos) > 0) {
        ?>
        <br>
        <table class="ttable">
            <thead>
                <tr>
                    <th colspan="3" style="text-align: left;">DÍZIMOS</th>
                </tr>
                <tr>
                    <td style="font-weight: bold;">NOME</td>
                    <td style="font-weight: bold;">FORMA PAG.</td>
                    <td style="text-align: right; font-weight: bold;">VALOR R$</td>
                </tr>                        
            </thead>
            <tbody>                        
                <?php 
                    foreach($dizimos as $d) {
                        ?>
                        <tr>
                            <td>
                                <?= $d->nome ?>
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
        <table class="ttable" style="border-top: 3px solid silver;">                    
                <tr>
                <?php 
                    foreach($SIGLAS_FP as $fp) {
                        if ($dizimos_fp[$fp] > 0.00):
                        ?>
                        <td style="width: 100px; <?= ($fp === 'TOTAL') ? 'text-align: right;' : '' ?>">
                            <div class="llabel"><?= edf_fp_descricao($fp) ?></div>
                            <div>
                                R$ <?= valorToBr($dizimos_fp[$fp]) ?>
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

