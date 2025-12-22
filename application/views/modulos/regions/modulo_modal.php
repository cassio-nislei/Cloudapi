<!-- Modal Modulos -->
<div class="modal fade" id="moduloModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title float-left" id="exampleModalLabel" style="font-weight: bold; font-size: 20px;">
            MÓDULO
        </h5>
        <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <label for="nome" class="caption" style="font-size: 14px;">Controle</label>
                    <input type="text" id="nome"
                           class="form-control"
                           v-model="registro.controle">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="descricao" class="caption" style="font-size: 14px;">Ação</label>
                    <input type="text" id="descricao" 
                           class="form-control"
                           v-model="registro.acao">
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