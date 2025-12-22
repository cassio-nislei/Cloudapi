<?php 
    if (isset($pastores) && is5_count($pastores) > 0) {
        ?>
        <br>
        <table class="ttable">
            <thead>
                <tr>
                    <th style="text-align: left;">PASTORES</th>
                </tr>
            </thead>
            <tbody>                        
                <?php 
                    foreach($pastores as $p) {
                        ?>
                        <tr>
                            <td>
                                <?= $p->nome ?>
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