<br>
<div class="card">
    <div class="card-header ccheader">
        DIÁCONOS
        <div class="btn btn-hti pull-right"
             v-on:click="addDiacono()">
            <i class="bi bi-plus-lg"></i> Adicionar
        </div>
    </div>
    <div v-if="diaconos && diaconos.length > 0" class="card-body">
        <div v-if="loading_diaconos" style="text-align: center; padding: 10px; ">
            Pesquisando. Aguarde...
        </div> 
        <div v-else>
            <table class="table table-light" style="width: 100%;">
                <tbody>
                    <tr v-for="d in diaconos">
                        <td>{{d.nome}}</td>
                        <td style="width: 100px; text-align: right;">
                            <span class="fa fa-edit span-editar"                                           
                                  title="Editar"
                                  v-on:click="editarDiacono(d)"
                                  ></span>

                            <span class="fa fa-trash span-excluir"                                           
                                  title="Excluir"
                                  v-on:click="excluirDiacono(d)"
                                  ></span>
                        </td>
                    </tr>
                </tbody>                        
            </table>            
        </div>        
    </div>
</div>

