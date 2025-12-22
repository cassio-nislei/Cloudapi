<?php   
    $status  = isset($status) ? $status : '';
    $cor     = ($status === 'OK') ? 'green' : 'red';               
    if (isset($mensagem) && $mensagem !== '') {
        ?>
        <div id="divAlert" class="row justify-content-lg-center" style="margin-bottom: 25px; margin-top: 25px;">
            <div class="col-md-6">
                <div style="padding: 5px; background-color: <?= $cor ?>; color: white; font-size: 14px; text-align: center; border-radius: 5px;">
                    <?= $mensagem ?>
                </div>                                
            </div>                        
        </div>
        <script>
            $("#divAlert").show(1000);
            $("#divAlert").click(function() {
                $(this).hide(1000);
            });
        </script>
        <?php
    }                                  
?>

