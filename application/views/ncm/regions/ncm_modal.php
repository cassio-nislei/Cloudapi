<!-- NCM Pessoa -->
<div class="modal fade" id="ncmModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="min-height: 600px;">
            <div class="modal-header">
                  <h5 class="modal-title float-left" id="exampleModalLabel" style="font-weight: bold; font-size: 20px;">
                      NCM
                  </h5>
                  <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body" style="padding-top: 0px;"> 
                <div class="row">
                    <div class="col-md-3">
                        <label class="caption">Código</label>
                        <input type="tel" v-model="registro.NCM" class="form-control" maxlength="8"> 
                    </div>
                    <div class="col-md-9">
                        <label class="caption">Descrição</label>
                        <input type="tel" class="form-control"
                               v-model="registro.DESCRICAO"
                               style="text-transform: uppercase;"
                               >
                    </div>
                </div>
                
                <?php 
                    include_once APPPATH . 'views/ncm/regions/abas/impostos.php';              
                ?>  

            </div>                          
            <div class="modal-footer d-flex justify-content-between">  
                
                <button id="btnAuditado" 
                        type="button" class="btn btn-success" style="width: 200px;"
                        v-if="registro.ID && registro.ID > 0"
                        v-on:click="setAuditado()"                                                
                        >
                    {{ registro.AUDITADO === 'S' ? 'Atualizar Data Auditado' : 'Marcar como Auditado' }} 
                </button>
                
                <button v-else disabled="true" class="btn btn-success">
                    Marcar como Auditado
                </button>                
                
                <button id="btnSalvarUsuario" type="button" class="btn btn-primary" style="width: 120px;"
                        v-on:click="salvar()"
                        >Salvar</button>
            </div>
        </div>
        
    </div>
</div>
