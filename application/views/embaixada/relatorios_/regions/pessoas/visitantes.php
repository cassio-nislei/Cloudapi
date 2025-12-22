<?php 
    if (isset($visitantes) && is5_count($visitantes) > 0) {
        ?>
        <br>
        <table class="ttable">
            <thead>
                <tr>
                    <th style="text-align: left;">VISITANTES</th>
                </tr>
            </thead>
            <tbody>                        
                <?php 
                    foreach($visitantes as $d) {
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

