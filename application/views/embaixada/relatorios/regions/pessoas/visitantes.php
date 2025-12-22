<?php 
    if (isset($visitantes) && is5_count($visitantes) > 0) {
        ?>
        <br>
        <table class="ttable">
            <thead>
                <tr>
                    <th class="titulo">VISITANTES ESPECIAIS</th>
                </tr>
            </thead>
            <tbody>                        
                <?php 
                    foreach($visitantes as $d) {
                        ?>
                        <tr>
                            <td>
                                <?= is5_strtoupper($d->nome) ?>
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

