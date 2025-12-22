<div style="text-align: left; margin-top: 10px;">
    <div class="btn btn-hti" v-on:click="novoAtendimento()" style="width: 120px;">
        <span class="fa fa-add"></span>
        Novo
    </div> 
</div>

<div class="row" style="max-height: 350px; overflow-y: auto;">
    <div class="col-md-12">
        <table v-if="atendimentos && (atendimentos.length ?? 0) > 0" class="table table-condensed table-hover"
               style="font-size: 12px;"
               >            
            <thead>
                <tr>
                    <th style="border-top: none;">Data/Hora</th>
                    <th style="border-top: none;">Descrição</th>
                    <th style="width: 100px; border-top: none;">Usuário</th> 
                    <th style="width: 120px; text-align: right; border-top: none;"></th>
                </tr>
            </thead>           
            <tbody>
                <tr v-for="a in atendimentos">
                    <td>{{a.CREATED_AT}}</td>
                    <td>{{a.TEXTO}}</td>
                    <td>{{a.USUARIO_INSERT}}</td>                                   
                    <td style="text-align: right;">
                        <span class="fa fa-edit" v-on:click="editarAtendimento(a)"
                              title="Editar Atendimento"
                              style="cursor: pointer; font-size: 18px; color: green;"                              
                              ></span>
                        
                        <span class="fa fa-trash" v-on:click="excluirAtendimento(a.ID)"
                              title="Excluir Atendimento"
                              style="cursor: pointer; font-size: 18px; color: red; margin-left: 10px;"
                              ></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-else style="text-align: center;">
            <br>
            Nenhum atendimento encontrado.
        </div>
    </div>
</div>