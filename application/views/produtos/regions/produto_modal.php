<!-- NCM Pessoa -->
<div class="modal fade" id="produtoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="min-height: 600px;">
            <div class="modal-header">
                  <h5 class="modal-title float-left" id="exampleModalLabel" style="font-weight: bold; font-size: 20px;">
                      PRODUTO
                  </h5>
                  <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
            </div>            
            <div class="modal-body" style="padding-top: 0px;"> 
                <div class="row">
                    <div class="col-md-3">
                        <label class="caption">NCM</label>
                        <div class="input-group mb-3">                                            
                            <input type="text" id="imp_ncm" class="form-control"
                                   v-model="registro.NCM"
                                   onkeypress="return somenteNumeros(event);"
                                   maxlength="10" 
                                   v-on:blur="getNCM(registro.NCM)"
                               >   
                            <!--
                            <button class="btn btn-outline-secondary" type="button"
                                    v-on:click="getImpostos('NCM')"
                                    >
                                <span class="fa fa-search"></span>
                            </button>
                            -->
                        </div>
                    </div>
                    <div class="col-md-9">
                        <label class="caption">Descrição</label>
                        <input type="tel" class="form-control"
                               v-model="ncm.DESCRICAO"
                               style="text-transform: uppercase;"
                               disabled="true"
                               >
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="caption">Referência</label>
                        <input type="tel" v-model="registro.REFERENCIA" class="form-control"
                               onkeypress="return somenteNumeros(event);"
                               maxlength="14"
                               > 
                    </div>
                    <div class="col-md-7">
                        <label class="caption">Nome</label>
                        <input type="tel" class="form-control"
                               v-model="registro.DESCRICAO"
                               style="text-transform: uppercase;"
                               >
                    </div>
                    <div class="col-md-2">
                        <label class="caption">Medida</label>
                        <input type="tel" v-model="registro.MEDIDA" class="form-control" maxlength="3"
                               style="text-transform: uppercase;"
                               > 
                    </div>
                </div>
                <?php 
                    include_once APPPATH . 'views/produtos/regions/abas/impostos.php';              
                ?>   

            </div>                          
            <div class="modal-footer">        
                <button id="btnSalvarUsuario" type="button" class="btn btn-hti" style="width: 120px;"
                        v-on:click="salvar()"
                        >Salvar</button>
            </div>
        </div>
        
    </div>
</div>
