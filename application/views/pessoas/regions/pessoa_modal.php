<!-- Modal Pessoa -->
<div class="modal fade" id="pessoaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="min-height: 600px;">
      <div class="modal-header">
        <h5 class="modal-title float-left" id="exampleModalLabel" style="font-weight: bold; font-size: 20px;">
            PESSOA
        </h5>
        <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding-top: 0px;">
            <div class="custom-tab">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="emit-basico-tab" data-toggle="tab" href="#emit-basico" role="tab" aria-controls="custom-nav-home" aria-selected="true">Dados Básicos</a>
                        <a class="nav-item nav-link" id="emit-financeiro-tab" data-toggle="tab" href="#emit-financeiro" role="tab" aria-controls="custom-nav-profile" aria-selected="false">FrontBox</a>     
                        <!--
                        <a class="nav-item nav-link" id="emit-atualizacoes-tab" data-toggle="tab" href="#emit-atualizacoes" role="tab" aria-controls="custom-nav-profile" aria-selected="false">Atualizações</a>  
                        -->
                        <a class="nav-item nav-link" id="emit-licencas-tab" data-toggle="tab" href="#emit-licencas" role="tab" aria-controls="custom-nav-profile" aria-selected="false">Licenças</a>
                        <a class="nav-item nav-link" id="emit-atendimentos-tab" data-toggle="tab" href="#emit-atendimentos" role="tab" aria-controls="custom-nav-profile" aria-selected="false">Atendimentos</a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="emit-basico" role="tabpanel" aria-labelledby="perfil-loja-tab">
                        <?php 
                            include_once APPPATH . 'views/pessoas/regions/abas/basico.php';              
                        ?>                            
                    </div>
                    
                    <div class="tab-pane fade show" id="emit-financeiro" role="tabpanel" aria-labelledby="perfil-loja-tab">
                        <?php 
                           include_once APPPATH . 'views/pessoas/regions/abas/financeiro.php';              
                        ?> 
                    </div>
                    
                    <!--
                    <div class="tab-pane fade show" id="emit-atualizacoes" role="tabpanel" aria-labelledby="perfil-loja-tab">
                        <?php 
                           include_once APPPATH . 'views/pessoas/regions/abas/atualizacoes.php';              
                        ?> 
                    </div>
                    -->
                    
                    <div class="tab-pane fade show" id="emit-licencas" role="tabpanel" aria-labelledby="perfil-loja-tab">
                        <?php 
                            include_once APPPATH . 'views/pessoas/regions/abas/licencas.php';              
                        ?> 
                    </div>

                    <!--
                    <div class="tab-pane fade show" id="emit-outros" role="tabpanel" aria-labelledby="perfil-loja-tab">
                        <?php 
                            include_once APPPATH . 'views/pessoas/regions/abas/outros.php';              
                        ?> 
                    </div>
                    -->   
                    
                    <div class="tab-pane fade show" id="emit-atendimentos" role="tabpanel" aria-labelledby="perfil-loja-tab">
                        <?php 
                            include_once APPPATH . 'views/pessoas/regions/abas/atendimentos.php';              
                        ?> 
                    </div>
                    
                </div>    
            </div>
              
            <br>
      </div>
      <div class="modal-footer">        
        <button id="btnSalvarUsuario" type="button" class="btn btn-hti" style="width: 120px;"
                v-on:click="salvar()"
                >Salvar</button>
      </div>
    </div>
  </div>
</div>