<!-- Modal Pessoa -->
<div class="modal fade" id="atendimentoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border: 1px solid silver;">
      <div class="modal-header">
        <h5 class="modal-title float-left" id="exampleModalLabel" style="font-weight: bold; font-size: 20px;">
            ATENDIMENTO
        </h5>
        <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding-top: 20px;">
            <textarea v-model="atendimento.TEXTO" rows="10" class="form-control"
                placeholder="Descreva aqui o atendimento..."></textarea>              
                        
            <div class="row" style="font-size: 12px;">
                <div class="col-md-6">
                    <label class="caption">Cadastro</label>
                    <input type="text" class="form-control" v-model="atendimento.USUARIO_INSERT" disabled="true">
                </div>
                <div class="col-md-6">
                     <label class="caption">Data/Hora</label>
                     <input type="text" class="form-control" v-model="atendimento.CREATED_AT" disabled="true">
                </div>
            </div>
            
            <div class="row" style="font-size: 12px;">
                <div class="col-md-6">
                    <label class="caption">Atualização</label>
                    <input type="text" class="form-control" v-model="atendimento.USUARIO_UPDATE" disabled="true">
                </div>
                <div class="col-md-6">
                     <label class="caption">Data/Hora</label>
                     <input type="text" class="form-control" v-model="atendimento.UPDATED_AT" disabled="true">
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <button id="btnSalvarUsuario" type="button" class="btn btn-success" style="width: 120px;"
                     v-on:click="salvarAtendimento()"
                     >
                    Salvar
                </button> 
            </div>
       </div>
      
    </div>
  </div>
</div>