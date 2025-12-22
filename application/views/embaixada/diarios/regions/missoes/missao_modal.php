<!-- Modal Grupos -->
<div class="modal fade" id="missaoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title float-left" id="exampleModalLabel" style="font-weight: bold; font-size: 20px;">
            OFERTA PARA MISSÕES
        </h5>
        <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <label for="dizimo_fp" class="caption" style="font-size: 14px;">Forma de Pagamento</label>
                    <select id="dizimo_fp" v-model="missao.forma_pag_id" class="form-control">
                        <option v-for="fp in formas_pag" v-bind:value="fp.id">
                            {{fp.nome}}
                        </option>
                    </select>
                </div>
            </div>
          
            <div class="row">
                <div class="col-md-12">
                    <label for="valor" class="caption" style="font-size: 14px;">Valor R$</label>
                    <input type="tel" id="missao_total"
                           class="form-control monetario" >
                </div>
            </div>
          
            <br>
      </div>
      <div class="modal-footer">        
        <button v-if="loading_missoes" type="button" class="btn btn-hti" style="width: 120px;"
                disabled="disabled">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Aguarde...
        </button>
        <button v-else id="btnSalvarUsuario" type="button" class="btn btn-hti" style="width: 120px;"
                v-on:click="salvarMissao()">
            Salvar
        </button>
      </div>
    </div>
  </div>
</div>