<!--
<div class="row" style="margin-top: 15px;">
    <div class="col-md-12">
        <label class="caption">Motivo desativação</label> <small>(aparecerá para o cliente quando for desativado)</small>
        <textarea row="5" v-model="registro.OBS" class="form-control">{{registro.OBS}}</textarea>
    </div>
</div>
-->

<div class="row">
    <div class="col-md-12" style="font-weight: bold; color: #0c5a82; margin-top: 15px;">
        COBRANÇA        
    </div>
</div>
<div class="row">       
    <div class="col-md-2">
        <label for="dia_acerto" class="caption">Dia Acerto</label>
        <input type="tel" id="dia_acerto" class="form-control"
               onkeypress="return somenteNumeros(event);"
               maxlength="2"
               v-model="registro.DIA_ACERTO">
    </div>        
    <div class="col-md-2">
        <label for="cred_desconto" class="caption">Mensalidade</label>
        <input type="tel" id="mensalidade" class="form-control monetario">
    </div>
    <div class="col-md-4">
        <label for="cred_limite" class="caption">Data Cadastro</label>
        <input type="tel" id="data_cadastro" class="form-control datepicker" disabled="true">
    </div>
    <div class="col-md-4">
        <label for="cred_limite" class="caption">Últ. Atualização</label>
        <input type="tel" id="ultima_atualizacao" class="form-control datepicker" disabled="true">
    </div>
</div>
<div class="row">
    <div class="col-md-12" style="font-weight: bold; color: #0c5a82; margin-top: 20px;">
        LICENÇAS        
    </div>
</div>
<div class="row">    
    <div class="col-md-2">
        <label for="dia_acerto" class="caption">Necessárias</label>
        <input type="tel" id="dia_acerto" class="form-control"
               onkeypress="return somenteNumeros(event);"
               maxlength="2"
               v-model="registro.LICENCAS">
    </div>    
    <div class="col-md-3">
        <label for="cred_limite" class="caption">Expiram Em</label>
        <input type="tel" id="expira_em" class="form-control datepicker">
    </div>
    
    
    <div class="col-md-2">
        <label for="dia_acerto" class="caption">Versão FBX</label>
        <input type="tel" id="dia_acerto" class="form-control" disabled="true"
               v-model="registro.VERSAO_FBX">
    </div>
    <div class="col-md-2">
        <label for="dia_acerto" class="caption">Versão PDV</label>
        <input type="tel" id="dia_acerto" class="form-control" disabled="true"
               v-model="registro.VERSAO_PDV">
    </div>
    
</div>

<div class="row" style="margin-top: 25px;">
    <div class="col-md-12">
        <input type="checkbox" 
               v-model="registro.ACESSA_IMPOSTOS"
               true-value="S"
               false-value="N"
               > Permite acessar impostos
    </div>
</div>

<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <input type="checkbox" 
               v-model="registro.AUTO_INSTALL"
               true-value="S"
               false-value="N"
               > Cliente em fase de avaliação <small>(grátis por 30 dias)</small>
    </div>    
</div>

<div class="row" style="margin-top: 15px;">
    <div class="col-md-4">
        <label class="caption">Data Instalação</label> <small>(instalador)</small>
        <input type="tel" id="data_install" class="form-control datepicker" disabled="disabled">
    </div>
</div>