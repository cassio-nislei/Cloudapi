<!-- Modal Impostos -->
<div class="modal fade" id="produtosModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="margin-top: 50px;">
        <div class="modal-content">
            <div class="modal-header">
                  <h5 class="modal-title float-left" id="exampleModalLabel" style="font-weight: bold; font-size: 20px;">
                      NCM
                  </h5>
                  <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
            </div>            
            <div class="modal-body" style="height: calc(100vh - 200px); overflow-y: auto;"> 
                <table class="table table-hover table-responsive" 
                       style="font-size: 14px; cursor: pointer; width: 100%;">
                    <thead>
                        <tr>
                            <td><b>Código</b></td>
                            <td><b>Referência</b></td>
                            <td><b>Descrição</b></td>
                            <td><b>NCM</b></td>
                            <td><b>CEST</b></td>
                            <td><b>CFOP</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in produtos">
                            <td>{{p.cProd}}</td>
                            <td>{{p.cEAN}}</td>
                            <td>{{p.xProd}}</td>
                            <td>{{p.NCM}}</td>
                            <td>{{p.CEST}}</td>
                            <td>{{p.CFOP}}</td>
                        </tr>
                    </tbody>
                </table>                   
            </div>            
        </div>
    </div>
</div>
