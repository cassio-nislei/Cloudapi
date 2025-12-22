<?php 
    if (isset($diaconos) && is5_count($diaconos) > 0) {
        ?>
        <br>
        <table class="ttable">
            <thead>
                <tr>
                    <th style="text-align: left;">DIÁCONOS</th>
                </tr>
            </thead>
            <tbody>                        
                <?php 
                    foreach($diaconos as $d) {
                        ?>
                        <tr>
                            <td>
                                <?= $d->nome ?>
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