<br>
<div class="card">
    <div class="card-header ccheader">
        DÍZIMOS
        <div class="btn btn-hti pull-right"
             v-on:click="addDizimo()">
            <i class="bi bi-plus-lg"></i> Adicionar
        </div>
    </div>
    <div v-if="dizimos && dizimos.length > 0" class="card-body">
        <div v-if="loading_dizimos" style="text-align: center; padding: 10px;">
            Pesquisando. Aguarde...
        </div> 
        <div v-else>
            <table class="table table-light" style="width: 100%;">
                <tbody>
                    <tr v-for="d in dizimos">
                        <td>{{d.nome}}</td>
                        <td style="width: 150px; text-align: right;">{{toCurrency(d.valor)}}</td>
                        <td style="width: 200px;">{{d.forma_pag}}</td>
                        <td style="width: 100px; text-align: right;">
                            <span class="fa fa-edit span-editar"                                           
                                  title="Editar"
                                  v-on:click="editarDizimo(d)"
                                  ></span>

                            <span class="fa fa-trash span-excluir"                                           
                                  title="Excluir"
                                  v-on:click="excluirDizimo(d)"
                                  ></span>
                        </td>
                    </tr>
                </tbody>                        
            </table>           
        </div>        
    </div>
    <div v-if="dizimos && (dizimos.length > 0) && dizimos_fp" class="card-footer ccfooter">        
        <table style="width: 100%;" border="0">
            <tr style="text-align: left;">
                <td v-if="dizimos_fp.DINH > 0.00" style="width: 100px;">
                    <small>Dinheiro</small>
                    <br>
                    {{toCurrency(dizimos_fp.DINH)}}
                </td>
                <td v-if="dizimos_fp.DEPTO > 0.00" style="width: 100px;">
                    <small>Depósito</small>
                    <br>
                    {{toCurrency(dizimos_fp.DEPTO)}}
                </td>
                <td v-if="dizimos_fp.PMAQ > 0.00" style="width: 100px;" >
                    <small>Pix Máq.</small>
                    <br>
                    {{toCurrency(dizimos_fp.PMAQ)}}
                </td>
                <td v-if="dizimos_fp.PBCO > 0.00" style="width: 100px;">
                    <small>Pix Banco</small>
                    <br>
                    {{toCurrency(dizimos_fp.PBCO)}}
                </td>
                <td v-if="dizimos_fp.CCRED > 0.00" style="width: 100px;">
                    <small>Cartão Créd.</small>
                    <br>
                    {{toCurrency(dizimos_fp.CCRED)}}
                </td>
                <td v-if="dizimos_fp.CDEB > 0.00" style="width: 100px;">
                    <small>Cartão Déb.</small>
                    <br>
                    {{toCurrency(dizimos_fp.CDEB)}}
                </td>
                <td style="text-align: right;">
                    <small>Total</small>
                    <br>
                    {{toCurrency(dizimos_fp.TOTAL)}}
                </td>
            </tr>
        </table>        
    </div>
</div>

