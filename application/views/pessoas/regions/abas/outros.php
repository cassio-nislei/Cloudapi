<div class="row">
    <div class="col-md-12" style="font-weight: bold; color: #0c5a82; margin-top: 15px;">
        SPC - SISTEMA PAPION DE CONTROLE DE CONTAS :)
    </div>
</div>

<div class="row" style="margin-top: 20px; margin-bottom: 25px;">
    <div class="col-md-12">
        <input type="checkbox" 
               v-model="registro.ACESSO_SPC"
               true-value="S"
               false-value="N"
               > O cliente pode acessar o serviço
    </div>    
</div>

<div class="row">
    <div class="col-md-3">
        <label for="dia_acerto" class="caption">Consultas</label>
        <input type="tel" id="dia_acerto" class="form-control"
               onkeypress="return somenteNumeros(event);"
               maxlength="3"
               v-model="registro.SPC_CONSULTA">
    </div>
    <div class="col-md-3">
        <label for="dia_acerto" class="caption">Negativações</label>
        <input type="tel" id="dia_acerto" class="form-control"
               onkeypress="return somenteNumeros(event);"
               maxlength="3"
               v-model="registro.SPC_NEGATIVACAO">
    </div>
    <div class="col-md-3">
        <label for="dia_acerto" class="caption">Reabilitações</label>
        <input type="tel" id="dia_acerto" class="form-control"
               onkeypress="return somenteNumeros(event);"
               maxlength="3"
               v-model="registro.SPC_REABILITACAO">
    </div>
</div>