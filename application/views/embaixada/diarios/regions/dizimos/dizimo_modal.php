<!-- Modal Grupos -->
<div class="modal fade" id="dizimoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title float-left" id="exampleModalLabel" style="font-weight: bold; font-size: 20px;">
            DÍZIMO
        </h5>
        <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          
            <div class="row">
                <div class="col-md-12">
                    <label for="nome" class="caption" style="font-size: 14px;">Nome</label>
                    <input type="text" id="dizimo_nome"
                           class="form-control"
                           v-model="dizimo.nome">
                </div>
            </div>
          
            <div class="row">
                <div class="col-md-12">
                    <label for="dizimo_fp" class="caption" style="font-size: 14px;">Forma de Pagamento</label>
                    <select id="dizimo_fp" v-model="dizimo.forma_pag_id" class="form-control">
                        <option v-for="fp in formas_pag" v-bind:value="fp.id">
                            {{fp.nome}}
                        </option>
                    </select>
                </div>
            </div>
          
            <div class="row">
                <div class="col-md-12">
                    <label for="valor" class="caption" style="font-size: 14px;">Valor R$</label>
                    <input type="tel" id="dizimo_valor"
                           class="form-control monetario" >
                </div>
            </div>
          
            <br>
      </div>
      <div class="modal-footer">        
        <button v-if="loading_dizimos" type="button" class="btn btn-hti" style="width: 120px;"
                disabled="disabled">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Aguarde...
        </button>
        <button v-else id="btnSalvarUsuario" type="button" class="btn btn-hti" style="width: 120px;"
                v-on:click="salvarDizimo()">
            Salvar
        </button>
      </div>
    </div>
  </div>
</div>