<?php 
    if (isset($apresentacoes) && is5_count($apresentacoes) > 0) {
        ?>
        <br>
        <table class="ttable">
            <thead>
                <tr>
                    <th style="text-align: left;">CRIANÇAS APRESENTADAS</th>
                </tr>
            </thead>
            <tbody>                        
                <?php 
                    foreach($apresentacoes as $d) {
                        ?>
                        <tr>
                            <td>
                                <?= $d->nome ?> (<?= $d->nome_mae ?> / <?= $d->nome_pai ?>)
                            </td>    
                        </tr>
                        <?php
                    }
                ?>                        
            </tbody>
        </table>                    
        <?php
    }
?>