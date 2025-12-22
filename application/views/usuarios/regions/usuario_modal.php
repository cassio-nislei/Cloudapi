<!-- Modal Usuarios -->
<div class="modal fade" id="usuarioModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title float-left" id="exampleModalLabel" style="font-weight: bold; font-size: 20px;">USUÁRIO</h5>
        <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <label for="usuario_nome" class="caption" style="font-size: 14px;">Nome</label>
                    <input type="text" id="usuario_nome" name="usuario_nome"
                           class="form-control"
                           v-model="registro.nome">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="usuario_email" class="caption" style="font-size: 14px;">E-mail</label>
                    <input v-if="registro.master !== 'S'" type="text" id="usuario_email" name="usuario_email"
                           class="form-control"
                           v-model="registro.email">
                    <input v-else type="text" id="usuario_email" name="usuario_email"
                           class="form-control" disabled="disabled"
                           v-model="registro.email">
                </div>
                <div class="col-md-6">
                    <label for="usuario_senha" class="caption" style="font-size: 14px;">Senha</label>
                    <input v-if="registro.master !== 'S'" type="password" id="usuario_senha" name="usuario_senha"
                           class="form-control"
                           v-model="registro.senha">
                    <input v-else type="password" id="usuario_senha" name="usuario_senha"
                           class="form-control" disabled="disabled"
                           v-model="registro.senha">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="filial_id" class="caption" style="font-size: 14px;">Filial</label>
                      <select id="filial_id" class="form-control" placeholeder="Selecionar"
                              v-model="registro.filial_id"
                              >                        
                          <option v-for="f in filiais" id="f.id" v-bind:value="f.id">{{f.nome}}</option>
                      </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="usuario_grupo_id" class="caption" style="font-size: 14px;">Grupo</label>
                    <select v-if="registro.master !== 'S'" id="usuario_grupo_id" class="form-control" placeholeder="Selecionar"
                            v-model="registro.grupo_id"
                            >                        
                        <option v-for="g in grupos" id="g.id" v-bind:value="g.id">{{g.nome}}</option>
                    </select>
                    <select v-else id="usuario_grupo_id" class="form-control" placeholeder="Selecionar"
                            style="font-size: 14px;"
                            v-model="registro.grupo_id"                            
                            disabled="disabled"
                            >                        
                        <option v-for="g in grupos" id="g.id" v-bind:value="g.id">{{g.nome}}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="usuario_status" class="caption" style="font-size: 14px;">Status</label>
                    <select v-if="registro.master !== 'S'" id="usuario_status" class="form-control"
                            v-model="registro.ativo"
                            >                        
                        <option id="S" value="S">Ativado</option>
                        <option id="N" value="N">Desativado</option>
                    </select>
                    <select v-else id="usuario_status" class="form-control" style="font-size: 14px;"
                            v-model="registro.ativo"
                            disabled="disabled"
                            >                        
                        <option id="S" value="S">Ativado</option>
                        <option id="N" value="N">Desativado</option>
                    </select>
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