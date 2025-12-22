<?php 
    if (isset($pastores) && is5_count($pastores) > 0) {
        ?>
        <br>
        <table class="ttable">
            <thead>
                <tr>
                    <th class="titulo">PASTORES</th>
                </tr>
            </thead>
            <tbody>                        
                <?php 
                    foreach($pastores as $p) {
                        ?>
                        <tr>
                            <td>
                                <?= is5_strtoupper($p->nome) ?>
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