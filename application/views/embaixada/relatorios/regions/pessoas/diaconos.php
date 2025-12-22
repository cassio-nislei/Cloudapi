<?php 
    if (isset($diaconos) && is5_count($diaconos) > 0) {
        ?>
        <br>
        <table class="ttable">
            <thead>
                <tr>
                    <th class="titulo">DIÁCONOS</th>
                </tr>
            </thead>
            <tbody>                        
                <?php 
                    foreach($diaconos as $d) {
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