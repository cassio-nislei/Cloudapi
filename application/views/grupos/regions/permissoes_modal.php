<!-- Modal Permissoes Grupo -->
<div class="modal fade" id="grupoPermissoesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title float-left" id="exampleModalLabel" style="font-weight: bold; font-size: 20px;">
            PERMISSÕES DO GRUPO
        </h5>
        <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row">
              <div class="col-md-9">
                  <select id="modulo_id" class="form-control">
                      <option v-for="m in modulos"                              
                              v-bind:value="m.id">
                          {{m.controle}}/{{m.acao}}
                      </option>
                  </select>
              </div>
              <div class="col-md-3">
                  <div class="btn btn-hti" style="width: 100%;"
                       v-on:click="vincularModulo()"
                       >                      
                      Vincular
                  </div>
              </div>
          </div>
          <br>
          <div class="row">
              <div class="col-md-12">
                  <table v-if="permissoes && permissoes.length > 0" class="table table-hover">
                      <thead style="font-weight: bold;">
                          <tr>
                              <td>Módulo</td>
                              <td style="text-align: center;">Ler</td>
                              <td style="text-align: center;">Gravar</td>
                              <td style="text-align: center;">Excluir</td>
                              <td style="text-align: center;"></td>
                          </tr>
                      </thead>
                      <tbody>
                          <tr v-for="p in permissoes">
                              <td>{{p.controle}}/{{p.acao}}</td>
                              <td style="width: 10px; text-align: center;">
                                  <span style="cursor: pointer;"
                                        v-bind:class="p.ler === 'S' ? 'bi bi-check-square':'bi bi-square'"
                                        v-on:click="setUnsetPermissao(p, 'ler')" />
                              </td>
                              <td style="width: 10px; text-align: center;">
                                  <span style="cursor: pointer;" 
                                        v-bind:class="p.gravar === 'S' ? 'bi bi-check-square':'bi bi-square'" 
                                        v-on:click="setUnsetPermissao(p, 'gravar')"
                                        />                                  
                              </td>
                              <td style="width: 10px; text-align: center;">
                                  <span style="cursor: pointer;"
                                        v-bind:class="p.excluir === 'S' ? 'bi bi-check-square':'bi bi-square'"
                                        v-on:click="setUnsetPermissao(p, 'excluir')"
                                        />
                              </td>
                              <td style="width: 10px; text-align: center;">
                                  <span class="fa fa-trash" style="color: red; cursor: pointer;"
                                        title="Excluir permissão."
                                        v-on:click="excluirPermissao(p.id)">                                            
                                  </span>
                              </td>
                          </tr>
                      </tbody>
                  </table>
                  <div v-else style="text-align: center;">
                      {{ loading ? 'Aguarde...' : 'Nenhum registro encontrado.'}}         
                  </div>
              </div>
          </div>
             
          <br>
      </div>      
    </div>
  </div>
</div>