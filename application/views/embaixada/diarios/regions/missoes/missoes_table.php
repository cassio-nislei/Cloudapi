<br>
<div class="card">
    <div class="card-header ccheader">
        OFERTA PARA MISSÕES
        <div class="btn btn-hti pull-right"
             v-on:click="addMissao()">
            <i class="bi bi-plus-lg"></i> Adicionar
        </div>
    </div>
    <div v-if="missoes && missoes.length > 0" class="card-body">
        <div v-if="loading_missoes" style="text-align: center; padding: 10px;">
            Pesquisando. Aguarde...
        </div> 
        <div v-else>
            <table class="table table-light" style="width: 100%;">
                <tbody>
                    <tr v-for="d in missoes">                        
                        <td style="width: 200px;">{{d.forma_pag}}</td>
                        <td style="width: 150px; text-align: right;">{{toCurrency(d.total)}}</td>                        
                        <td style="width: 100px; text-align: right;">
                            <span class="fa fa-edit span-editar"                                           
                                  title="Editar"
                                  v-on:click="editarMissao(d)"
                                  ></span>

                            <span class="fa fa-trash span-excluir"                                           
                                  title="Excluir"
                                  v-on:click="excluirMissao(d)"
                                  ></span>
                        </td>
                    </tr>
                </tbody>                        
            </table>           
        </div>        
    </div>
    <div v-if="missoes && (missoes.length > 0) && missoes_fp" class="card-footer ccfooter">        
        <table style="width: 100%;" border="0">
            <tr style="text-align: left;">
                <td v-if="missoes_fp.DINH > 0.00" style="width: 100px;">
                    <small>Dinheiro</small>
                    <br>
                    {{toCurrency(missoes_fp.DINH)}}
                </td>
                <td v-if="missoes_fp.DEPTO > 0.00" style="width: 100px;">
                    <small>Depósito</small>
                    <br>
                    {{toCurrency(missoes_fp.DEPTO)}}
                </td>
                <td v-if="missoes_fp.PMAQ > 0.00" style="width: 100px;">
                    <small>Pix Máq.</small>
                    <br>
                    {{toCurrency(missoes_fp.PMAQ)}}
                </td>
                <td v-if="missoes_fp.PBCO > 0.00" style="width: 100px;">
                    <small>Pix Banco</small>
                    <br>
                    {{toCurrency(missoes_fp.PBCO)}}
                </td>
                <td v-if="missoes_fp.CCRED > 0.00" style="width: 100px;">
                    <small>Cartão Créd.</small>
                    <br>
                    {{toCurrency(missoes_fp.CCRED)}}
                </td>
                <td v-if="missoes_fp.CDEB > 0.00" style="width: 100px;">
                    <small>Cartão Déb.</small>
                    <br>
                    {{toCurrency(missoes_fp.CDEB)}}
                </td>
                <td style="text-align: right;">
                    <small>Total</small>
                    <br>
                    {{toCurrency(missoes_fp.TOTAL)}}
                </td>
            </tr>
        </table>        
    </div>
</div>

