<div class="row">
    <div class="col-md-4">
        <label class="caption">Aliq. ICMS %</label>
        <input id="imp_aliq_icms" type="tel" class="form-control monetario">
    </div>
    
    <div class="col-md-4">
        <label class="caption">CFOP</label>
        <div class="input-group mb-3">                                            
            <input type="text" id="imp_cfop" class="form-control"
                   v-model="registro.CFOP"
                   onkeypress="return somenteNumeros(event);"
                   maxlength="5"                       
               >                    
            <button class="btn btn-outline-secondary" type="button"
                    v-on:click="getImpostos('CFOP')"
                    >
                <span class="fa fa-search"></span>
            </button>
        </div>
    </div>
    
    <div class="col-md-4">
        <label class="caption">CEST</label>
        <div class="input-group mb-3">                                            
            <input type="text" id="imp_cest" class="form-control"
                   v-model="registro.CEST"
                   onkeypress="return somenteNumeros(event);"                   
                   maxlength="7"                           
                   >

            <button class="btn btn-outline-secondary" type="button"
                    v-on:click="getImpostos('CEST')"
                    >
                <span class="fa fa-search"></span>
            </button>
        </div>
    </div>
</div>

<div class="row" style="margin-top: -15px;">
    <div class="col-md-12">
        <label class="caption">Origem</label>
        <select id="imp_origem" v-model="registro.ORIGEM" class="form-control">
            <option v-for="i in lista_impostos.origem" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <label class="caption">CST</label>
        <select id="imp_cst" v-model="registro.CST" class="form-control">
            <option v-for="i in lista_impostos.cst" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <label class="caption">CSOSN</label>
        <select id="imp_csosn" v-model="registro.CSOSN" class="form-control">                    
            <option v-for="i in lista_impostos.csosn" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>
</div>

<div class="row">            
    <div class="col-md-12">
        <label class="caption">Tipo</label>
        <select id="imp_tipo" v-model="registro.TIPO_ITEM" class="form-control">
            <option v-for="i in lista_impostos.tipo" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>
</div>


<div class="row">
    <?php /*
    <div class="col-md-4">
        <label class="caption">NCM</label>
        <div class="input-group mb-3">                                            
            <input type="text" id="imp_ncm" class="form-control"
                   onkeypress="return somenteNumeros(event);"                           
                   maxlength="8"
                   >                                        
                <button class="btn btn-outline-secondary" type="button"
                        v-on:click="getImpostos('ncm')"
                        >
                    <span class="fa fa-search"></span>
                </button>
                <a href="http://www.sefaz.go.gov.br/netaccess/Exportacao/constabpauta.asp" target="_blank" 
                   class="btn btn-outline-secondary"
                   title="Pesquisar no site da SEFAZ"                                                   
                   >
                    <span class="fa fa-external-link"></span>
                </a>

        </div>
    </div>
    */ ?>
    <div class="col-md-3">
        <label class="caption">Redução BC %</label>
        <input id="imp_red_bc" type="tel" class="form-control monetario">
    </div>
    <div class="col-md-3">
        <label class="caption">Crédito SN %</label>
        <input id="imp_cred_sn" type="tel" class="form-control monetario">
    </div>
    <div class="col-md-3">
        <label class="caption">Código Benef.</label>
        <input type="tel" v-model="registro.CBENEF" class="form-control"
               style="text-transform: uppercase;">
    </div>

</div>

<!--
<div class="row">            
    <div class="col-md-4">
        <label class="caption">CSOSN</label>
        <div class="input-group mb-3">                                            
            <input type="text" id="imp_csosn" class="form-control"
                   onkeypress="return somenteNumeros(event);"
                   maxlength="3"                           
                   >

            <button class="btn btn-outline-secondary" type="button"
                    v-on:click="getImpostos('csosn')"
                    >
                <span class="fa fa-search"></span>
            </button>
        </div>
    </div>

    <div class="col-md-4">
        <label class="caption">Origem</label>
        <div class="input-group mb-3">                                            
            <input type="text" id="imp_origem" class="form-control"
                   onkeypress="return somenteNumeros(event);"
                   maxlength="1"                           
                   >

            <button class="btn btn-outline-secondary" type="button"
                    v-on:click="getImpostos('origem')"
                    >
                <span class="fa fa-search"></span>
            </button>
        </div>
    </div>

    <div class="col-md-4">
        <label class="caption">Tipo</label>
        <div class="input-group mb-3">                                            
            <input type="text" id="imp_tipo" class="form-control"
                   onkeypress="return somenteNumeros(event);"
                   maxlength="3"                           
                   >
            <button class="btn btn-outline-secondary" type="button"
                    v-on:click="getImpostos('tipo','TIPO DE PRODUTO')"
                    >
                <span class="fa fa-search"></span>
            </button>                                                
        </div>
    </div>           
</div>
-->

<div class="row">            
    <div class="col-md-12" style="margin-top: 15px;">                
        <input type="checkbox" id="imp_trib_monofasica" 
               v-model="registro.IMP_TRIBUTACAO_MONOFASICA"
               true-value="S"
               false-value="N">
        <label for="imp_trib_monofasica">Tributação monofásica</label>                
    </div>
</div>

<div class="row" v-if="registro.DATA_AUDITORIA" style="margin-top: 15px;">            
    <div class="col-md-12">                                        
        <label for="auditado" v-bind:style="registro.AUDITADO === 'S' ? 'color: green;' : ''">
            NCM Auditado <span v-if="registro.DATA_AUDITORIA">em {{registro.DATA_AUDITORIA}}</span>
        </label>                
    </div>
</div>