<div class="row">
    <div class="col-md-3">
        <label class="caption">CFOP Externo</label>
        <div class="input-group mb-3">                                            
            <input type="text" id="imp_cfop_externo" class="form-control"
                   v-model="ncm.IMP_CFOP_EXTERNO"
                   disabled="true"> 
        </div>
    </div>
    
    <div class="col-md-9">
        <label class="caption">CSOSN Externo</label>
        <select id="imp_csosn_externo" v-model="ncm.IMP_CSOSN_EXTERNO" class="form-control" disabled="true">
            <option v-for="i in lista_impostos.csosn" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>
</div>   

<!--
<div class="row">
    <div class="col-md-12">
        <label class="caption">CST Externo</label>
        <select id="imp_cst_externo" v-model="registro.IMP_CST_EXTERNO" class="form-control">                    
            <option v-for="i in lista_impostos.cst" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>
</div>
-->

<!--
<div class="row">
    <div class="col-md-12">
        <label class="caption">Mod. BC para ICMS ST</label>
        <select id="imp_mod_bc_st" v-model="registro.IMP_MOD_BC_ST" class="form-control">
            <option v-for="i in lista_impostos.mod_bc" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <label class="caption">Motivo Desoneração</label>
        <select id="imp_motivo_deson" v-model="registro.IMP_MOTIVO_DESON" class="form-control">
            <option v-for="i in lista_impostos.motivo_deson" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>
</div>
-->
<div class="row">    
    <div class="col-md-3">
        <label class="caption">Cod. Enq. IPI</label>
        <input id="imp_cod_enq_ipi" type="tel" class="form-control monetario"
               v-model="ncm.IMP_COD_ENQ_IPI"
               disabled="true"
               >
    </div>
    <div class="col-md-3">
        <label class="caption">Fundo Pobreza %</label>
        <input id="imp_fcp" type="tel" class="form-control monetario" disabled="true"
               v-model="ncm.IMP_FCP"
               >
    </div>

    <div class="col-md-3">
        <label class="caption">MVA Normal %</label>
        <input id="imp_mva_normal" type="tel" class="form-control monetario" disabled="true"
               v-model="ncm.IMP_MVA_NORMAL"
               >
    </div>
    <div class="col-md-3">
        <label class="caption">MVA Simples %</label>
        <input id="imp_mva_simples" type="tel" class="form-control monetario" disabled="true"
               v-model="ncm.IMP_MVA"
               >
    </div>
</div>
<div class="row">        
    <div class="col-md-3">
        <label class="caption">GLP %</label>
        <input id="imp_glp" type="tel" class="form-control monetario" disabled="true"
               v-model="ncm.IMP_GLP">
    </div>          
    <div class="col-md-3">
        <label class="caption">GNn %</label>
        <input id="imp_gnn" type="tel" class="form-control monetario" disabled="true"
               v-model="ncm.IMP_GNN"
               >
    </div>
    <div class="col-md-3">
        <label class="caption">GNi %</label>
        <input id="imp_gni" type="tel" class="form-control monetario" disabled="true"
               v-model="ncm.IMP_GNI"
               >
    </div>
    
</div>
