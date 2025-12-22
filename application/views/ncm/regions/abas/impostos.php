<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="produto-icms-tab" data-toggle="tab" href="#produto-icms" role="tab" aria-controls="custom-nav-home" aria-selected="true">ICMS</a>                                
        <a class="nav-item nav-link" id="produto-ipi-tab" data-toggle="tab" href="#produto-ipi" role="tab" aria-controls="custom-nav-profile" aria-selected="false">IPI</a>                                
        <a class="nav-item nav-link" id="produto-piscofins-tab" data-toggle="tab" href="#produto-piscofins" role="tab" aria-controls="custom-nav-profile" aria-selected="false">PIS/COFINS</a>                                
        <a class="nav-item nav-link" id="produto-outros-tab" data-toggle="tab" href="#produto-outros" role="tab" aria-controls="custom-nav-profile" aria-selected="false">Outros</a>                                
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="produto-icms" role="tabpanel" aria-labelledby="produto-icms-tab">
        <?php 
            include_once APPPATH . 'views/ncm/regions/abas/icms.php';
        ?>
    </div>
    <div class="tab-pane fade" id="produto-ipi" role="tabpanel" aria-labelledby="produto-ipi-tab">
        <?php 
            include_once APPPATH . 'views/ncm/regions/abas/ipi.php';
        ?>
    </div>
    <div class="tab-pane fade" id="produto-piscofins" role="tabpanel" aria-labelledby="produto-piscofins-tab">
        <?php 
            include_once APPPATH . 'views/ncm/regions/abas/pis.php';
        ?>
    </div>
    <div class="tab-pane fade" id="produto-outros" role="tabpanel" aria-labelledby="produto-outros-tab">
        <?php 
            include_once APPPATH . 'views/ncm/regions/abas/outros.php';
        ?>
    </div>
</div>