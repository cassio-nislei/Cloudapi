<!-- Modal Grupos -->
<div class="modal fade" id="apresentacaoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title float-left" id="exampleModalLabel" style="font-weight: bold; font-size: 20px;">
            APRESENTAÇÃO
        </h5>
        <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <label for="nome" class="caption" style="font-size: 14px;">Nome da criança</label>
                    <input type="text" id="nome"
                           class="form-control"
                           v-model="apresentacao.nome">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="nome_mae" class="caption" style="font-size: 14px;">Nome da mãe</label>
                    <input type="text" id="nome_mae"
                           class="form-control"
                           v-model="apresentacao.nome_mae">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="nome_pai" class="caption" style="font-size: 14px;">Nome do pai</label>
                    <input type="text" id="nome_pai"
                           class="form-control"
                           v-model="apresentacao.nome_pai">
                </div>
            </div>
            <br>
      </div>
      <div class="modal-footer">        
        <button v-if="loading_apresentacoes" type="button" class="btn btn-hti" style="width: 120px;"
                disabled="disabled">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Aguarde...
        </button>
        <button v-else id="btnSalvarUsuario" type="button" class="btn btn-hti" style="width: 120px;"
                v-on:click="salvarApresentacao()">
            Salvar
        </button>
      </div>
    </div>
  </div>
</div>