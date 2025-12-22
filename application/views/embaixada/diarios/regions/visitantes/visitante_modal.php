<!-- Modal Grupos -->
<div class="modal fade" id="visitanteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title float-left" id="exampleModalLabel" style="font-weight: bold; font-size: 20px;">
            VISITANTE
        </h5>
        <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <label for="nome" class="caption" style="font-size: 14px;">Nome</label>
                    <input type="text" id="nome"
                           class="form-control"
                           v-model="visitante.nome">
                </div>
            </div>
            <br>
      </div>
      <div class="modal-footer">        
        <button v-if="loading_visitantes" type="button" class="btn btn-hti" style="width: 120px;"
                disabled="disabled">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Aguarde...
        </button>
        <button v-else id="btnSalvarUsuario" type="button" class="btn btn-hti" style="width: 120px;"
                v-on:click="salvarVisitante()">
            Salvar
        </button>
      </div>
    </div>
  </div>
</div>