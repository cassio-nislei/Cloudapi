<br>
<div class="card">
    <div class="card-header ccheader">
        OFERTAS ESPECIAIS
        <?php if($editavel): ?>
        <div class="btn btn-hti pull-right"
             v-on:click="addEspecial()"><i class="bi bi-plus-lg"></i>
            Adicionar
        </div>
        <?php endif; ?>
    </div>
    <div v-if="(especiais && especiais.length > 0) || (especiais_alt && especiais_alt.length > 0)" class="card-body">
        <div v-if="loading_especiais" style="text-align: center; padding: 10px;">
            Pesquisando. Aguarde...
        </div> 
        <div v-else>
            <table class="table table-light" style="width: 100%;">
                <tbody>
                    <tr v-for="d in especiais">                        
                        <td style="width: 200px;">{{d.forma_pag}}</td>
                        <td style="width: 150px; text-align: right;">{{toCurrency(d.total)}}</td>                        
                        <?php if($editavel): ?>
                        <td style="width: 100px; text-align: right;">
                            <span class="fa fa-edit span-editar"                                           
                                  title="Editar"
                                  v-on:click="editarEspecial(d)"
                                  ></span>

                            <span class="fa fa-trash span-excluir"                                           
                                  title="Excluir"
                                  v-on:click="excluirEspecial(d)"
                                  ></span>
                        </td>
                        <?php endif; ?>
                    </tr>
                </tbody>                        
            </table>            
        </div>  
        <div v-if="especiais_alt.length > 0" class="div-alteracoes">
            <div v-for="d in especiais_alt">
                {{formataDataHora(d.dh_insert)}} - {{d.historico}} 
                <?php if (isAdmin()): ?>
                <span class="fa fa-close pull-right" title="Excluir" 
                      style="cursor: pointer; margin-top: 3px;"
                      v-on:click="excluirAlteracao(d.id, 'especiais')"
                      ></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div v-if="especiais && (especiais.length > 0) && especiais_fp" class="card-footer ccfooter">        
        <table style="width: 100%;" border="0">
            <tr style="text-align: left;">
                <td v-if="especiais_fp.DINH > 0.00" style="width: 100px;">
                    <small>Dinheiro</small>
                    <br>
                    {{toCurrency(especiais_fp.DINH)}}
                </td>
                <td v-if="especiais_fp.DEPTO > 0.00" style="width: 100px;">
                    <small>Depósito</small>
                    <br>
                    {{toCurrency(especiais_fp.DEPTO)}}
                </td>
                <td v-if="especiais_fp.PMAQ > 0.00" style="width: 100px;">
                    <small>Pix Máq.</small>
                    <br>
                    {{toCurrency(especiais_fp.PMAQ)}}
                </td>
                <td v-if="especiais_fp.PBCO > 0.00" style="width: 100px;">
                    <small>Pix Banco</small>
                    <br>
                    {{toCurrency(especiais_fp.PBCO)}}
                </td>
                <td v-if="especiais_fp.CCRED > 0.00" style="width: 100px;">
                    <small>Cartão Créd.</small>
                    <br>
                    {{toCurrency(especiais_fp.CCRED)}}
                </td>
                <td v-if="especiais_fp.CDEB > 0.00" style="width: 100px;">
                    <small>Cartão Déb.</small>
                    <br>
                    {{toCurrency(especiais_fp.CDEB)}}
                </td>
                <td style="text-align: right;">
                    <small>Total</small>
                    <br>
                    {{toCurrency(especiais_fp.TOTAL)}}
                </td>
            </tr>
        </table>        
    </div>
</div>

