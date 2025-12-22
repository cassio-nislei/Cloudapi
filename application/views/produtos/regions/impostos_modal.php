<!-- Modal Impostos -->
<div class="modal fade" id="modalImpostos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="margin-top: 50px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="text-transform: uppercase;">SELECIONAR {{caption}} </h5>
                <span class="fa fa-close cclose" data-bs-dismiss="modal" aria-label="Close" style="color: #000;"></span>
            </div>
            <div class="modal-body" style="height: calc(100vh - 200px); overflow-y: auto;"> 
                <div class='row'>
                    <div class='col-md-12'>
                        <input type="text" id="inputFiltrarProduto" 
                               placeholder="Pesquisar Código/Descrição" 
                               class="form-control"
                               style="margin-bottom: 15px;"
                               v-model="filtro_imposto"
                               >
                    </div>
                </div>
                
                    <table class="table table-hover table-responsive" 
                           style="font-size: 14px; cursor: pointer; width: 100%;">
                        <thead>
                            <tr>
                                <td><b>Código</b></td>
                                <td><b>Descrição</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="i in impostos" v-if="i.visible !== false" v-on:click="selecionar_imposto(i.CODIGO)">
                                <td>{{i.CODIGO}}</td>
                                <td>{{i.DESCRICAO}}</td>
                            </tr>
                        </tbody>
                    </table>                   

            </div>            
        </div>
    </div>
</div>
