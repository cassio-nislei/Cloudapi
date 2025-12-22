<div class="row">
    <div class="col-md-4">
        <label class="caption">Aliq. ICMS %</label>
        <input id="imp_aliq_icms" type="tel" class="form-control monetario"
               v-model="ncm.IMP_ALIQ_ICM"
               disabled="true"
               >
    </div>
    
    <div class="col-md-4">
        <label class="caption">CFOP</label>
        <div class="input-group mb-3">                                            
            <input type="text" id="imp_cfop" class="form-control"
                   v-model="ncm.CFOP"
                   disabled="true"                   
               >  
        </div>
    </div>
    
    <div class="col-md-4">
        <label class="caption">CEST</label>
        <div class="input-group mb-3">                                            
            <input type="text" id="imp_cest" class="form-control"
                   v-model="ncm.CEST"
                   disabled="true"                          
                   >
        </div>
    </div>
</div>

<div class="row" style="margin-top: -15px;">
    <div class="col-md-12">
        <label class="caption">Origem</label>
        <select id="imp_origem" v-model="ncm.ORIGEM" class="form-control" disabled="true" >
            <option v-for="i in lista_impostos.origem" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <label class="caption">CST</label>
        <select id="imp_cst" v-model="ncm.CST" class="form-control" disabled="true">
            <option v-for="i in lista_impostos.cst" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <label class="caption">CSOSN</label>
        <select id="imp_csosn" v-model="ncm.CSOSN" class="form-control" disabled="true">                    
            <option v-for="i in lista_impostos.csosn" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>
</div>

<div class="row">            
    <div class="col-md-12">
        <label class="caption">Tipo</label>
        <select id="imp_tipo" v-model="ncm.TIPO_ITEM" class="form-control" disabled="true">
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
        <input id="imp_red_bc" type="tel" class="form-control monetario" disabled="true"
               v-model="ncm.RED_BC"
               >
    </div>
    <div class="col-md-3">
        <label class="caption">Crédito SN %</label>
        <input id="imp_cred_sn" type="tel" class="form-control monetario" disabled="true"
               v-model="ncm.CRED_SN"
               >
    </div>
    <div class="col-md-3">
        <label class="caption">Código Benef.</label>
        <input type="tel" v-model="ncm.CBENEF" class="form-control" disabled="true"
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
               v-model="ncm.IMP_TRIBUTACAO_MONOFASICA"
               true-value="S"
               false-value="N" disabled="true">
        <label for="imp_trib_monofasica">Tributação monofásica</label>                
    </div>
</div>