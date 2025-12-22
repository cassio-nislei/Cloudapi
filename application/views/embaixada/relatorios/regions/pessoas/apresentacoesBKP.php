<?php 
    if (isset($apresentacoes) && is5_count($apresentacoes) > 0) {
        ?>
        <br>
        <table class="ttable">
            <thead>
                <tr>
                    <th class="titulo">CRIANÇAS APRESENTADAS</th>
                </tr>
            </thead>
            <tbody>                        
                <?php 
                    foreach($apresentacoes as $d) {
                        ?>
                        <tr>
                            <td>
                                <?= is5_strtoupper($d->nome) ?> <br>
                                <span style="font-size: 8px;"> <?= is5_strtoupper($d->nome_mae) ?> / <?= is5_strtoupper($d->nome_pai) ?> </span>
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