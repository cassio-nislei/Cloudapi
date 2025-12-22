<div class="row">
    <div class="col-md-12">
        <label class="caption">CST</label>
        <select id="imp_cst_ipi" v-model="ncm.IPI_CST" class="form-control" disabled="true">
            <option v-for="i in lista_impostos.cst_ipi" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>            
</div>
<div class="row">
    <div class="col-md-3">
        <label class="caption">Aliquota %</label>
        <input type="tel" id="imp_aliq_ipi" 
               v-model="ncm.IPI_PERCENTUAL"
               class="form-control monetario"
               disabled="true"
               > 
    </div>
</div>