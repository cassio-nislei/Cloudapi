<br>
<div class="card">
    <div class="card-header ccheader">
        CRIANÇAS APRESENTADAS
        <div class="btn btn-hti pull-right"
             v-on:click="addApresentacao()">
            <i class="bi bi-plus-lg"></i> Adicionar
        </div>
    </div>
    <div v-if="apresentacoes && apresentacoes.length > 0" class="card-body">
        <div v-if="loading_apresentacoes" style="text-align: center; padding: 10px;">
            Pesquisando. Aguarde...
        </div> 
        <div v-else>
            <table class="table table-light" style="width: 100%;">
                <tbody>
                    <tr v-for="p in apresentacoes">
                        <td>{{p.nome}}</td>
                        <td style="width: 100px; text-align: right;">
                            <span class="fa fa-edit span-editar"                                           
                                  title="Editar"
                                  v-on:click="editarApresentacao(p)"
                                  ></span>

                            <span class="fa fa-trash span-excluir"                                           
                                  title="Excluir"
                                  v-on:click="excluirApresentacao(p)"
                                  ></span>
                        </td>
                    </tr>
                </tbody>                        
            </table>            
        </div>        
    </div>
</div>

