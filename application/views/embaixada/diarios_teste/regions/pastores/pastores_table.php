<br>
<div class="card">
    <div class="card-header ccheader">
        PASTORES
        <?php if($editavel): ?>
        <div class="btn btn-hti pull-right"
             v-on:click="addPastor()"><i class="bi bi-plus-lg"></i>
            Adicionar
        </div>
        <?php endif; ?>
    </div>
    <div v-if="pastores && pastores.length > 0" class="card-body">
        <div v-if="loading_pastores" style="text-align: center; padding: 10px;">
            Pesquisando. Aguarde...
        </div> 
        <div v-else>
            <table class="table table-light" style="width: 100%;">
                <tbody>
                    <tr v-for="p in pastores">
                        <td>{{p.nome}}</td>
                        <?php if($editavel): ?>
                        <td style="width: 100px; text-align: right;">
                            <span class="fa fa-edit span-editar"                                           
                                  title="Editar"
                                  v-on:click="editarPastor(p)"
                                  ></span>

                            <span class="fa fa-trash span-excluir"                                           
                                  title="Excluir"
                                  v-on:click="excluirPastor(p)"
                                  ></span>
                        </td>
                        <?php endif; ?>
                    </tr>
                </tbody>                        
            </table>            
        </div>        
    </div>
</div>

