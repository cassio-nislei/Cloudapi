<div style="font-size: 16px; font-weight: bold; color: #235274; margin-top: 10px;">
            Saída
        </div>
<div class="row">
    <div class="col-md-12">
        <label class="caption">CST</label>
        <select id="imp_pc_cst_saida" v-model="registro.PISCOFINS_CST_SAIDA" class="form-control">
            <option v-for="i in lista_impostos.cst_pis" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>            
</div>
<div class="row">
    <div class="col-md-3">
        <label class="caption">PIS %</label>
        <input type="tel" id="imp_pc_perc_pis_saida" 
               class="form-control monetario"                         
               > 
    </div>
    <div class="col-md-3">
        <label class="caption">COFINS %</label>
        <input type="tel" id="imp_pc_perc_cofins_saida" 
               class="form-control monetario"                         
               > 
    </div>
</div>        
<div style="font-size: 16px; font-weight: bold; color: #235274; margin-top: 20px;">
    Entrada
</div>
<div class="row">
    <div class="col-md-12">
        <label class="caption">CST</label>
        <select id="imp_pc_cst_entrada" v-model="registro.PISCOFINS_CST_ENTRADA" class="form-control">
            <option v-for="i in lista_impostos.cst_pis" v-bind:value="i.CODIGO">{{i.CODIGO}} - {{i.DESCRICAO}}</option>
        </select>
    </div>            
</div>
<div class="row">
    <div class="col-md-3">
        <label class="caption">PIS %</label>
        <input type="tel" id="imp_pc_perc_pis_entrada" 
               class="form-control monetario"                         
               > 
    </div>
    <div class="col-md-3">
        <label class="caption">COFINS %</label>
        <input type="tel" id="imp_pc_perc_cofins_entrada" 
               class="form-control monetario"                         
               > 
    </div>
</div>