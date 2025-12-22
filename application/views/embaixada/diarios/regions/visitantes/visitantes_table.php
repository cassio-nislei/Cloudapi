<br>
<div class="card">
    <div class="card-header ccheader">
        VISITANTES ESPECIAIS
        <div class="btn btn-hti pull-right"
             v-on:click="addVisitante()">
            <i class="bi bi-plus-lg"></i> Adicionar
        </div>
    </div>
    <div v-if="visitantes && visitantes.length > 0" class="card-body">
        <div v-if="loading_visitantes" style="text-align: center; padding: 10px;">
            Pesquisando. Aguarde...
        </div> 
        <div v-else>
            <table class="table table-light" style="width: 100%;">
                <tbody>
                    <tr v-for="d in visitantes">
                        <td>{{d.nome}}</td>
                        <td style="width: 100px; text-align: right;">
                            <span class="fa fa-edit span-editar"                                           
                                  title="Editar"
                                  v-on:click="editarVisitante(d)"
                                  ></span>

                            <span class="fa fa-trash span-excluir"                                           
                                  title="Excluir"
                                  v-on:click="excluirVisitante(d)"
                                  ></span>
                        </td>
                    </tr>
                </tbody>                        
            </table>            
        </div>        
    </div>
</div>

