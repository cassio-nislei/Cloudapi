<div class="row">
    <div class="col-md-6">
        <label for="perfil_nome" class="caption">Nome/Razão Social</label>
        <input type="text" id="registro_nome" class="form-control"
               v-model="registro.NOME">
    </div> 
    <div class="col-md-6">
        <label for="fantasia" class="caption">Fantasia/Apelido</label>
        <input type="text" id="fantasia" class="form-control"
               v-model="registro.FANTASIA">
    </div>         
</div>  
<div class="row">
    <div class="col-md-3">
        <label for="perfil_cgc" class="caption">CPF/CNPJ</label>
        <input type="tel" id="registro_cgc" class="form-control"
               onkeypress="return somenteNumeros(event);"
               maxlength="14"
               v-model="registro.CGC">
    </div>
    <div class="col-md-3">
        <label for="ie" class="caption">RG/IE</label>
        <input type="tel" id="registro_cgc" class="form-control"
               onkeypress="return somenteNumeros(event);"
               maxlength="10"
               v-model="registro.IE">
    </div>    
    <div class="col-md-3">
        <label for="contato" class="caption">Contato/Recados</label>
        <input type="text" id="nome_contato" class="form-control"
               v-model="registro.NOME_CONTATO">
    </div> 
</div>
<div class="row">
    <div class="col-md-3">
        <label for="perfil_email" class="caption">E-mail</label>
        <input type="text" id="registro_email"
               class="form-control" style="text-transform: lowercase;"
               v-model="registro.EMAIL">
    </div>
    <div class="col-md-3">
        <label for="perfil_telefone" class="caption">Telefone</label>
        <input type="text" id="telefone"
               class="form-control telefone"
               >
    </div>
    <div class="col-md-3">
        <label for="perfil_celular" class="caption">Celular</label>
        <input type="text" id="celular" 
               class="form-control celular"
               >
    </div>
    <div class="col-md-3">
        <label for="whatsapp" class="caption">WhatsApp</label>
        <input type="text" id="fax" 
               class="form-control celular"
               >
    </div>
</div>

<!-- ENDERECO -->
<div class="row">                               
    <div class="col-md-7">
        <label for="perfil_endereco" class="caption">Endereço</small></label>
        <input type="text" id="registro_endereco" class="form-control"
               v-model="registro.ENDERECO">
    </div> 
    <div class="col-md-2">
        <label for="perfil_numero" class="caption">Número</label>
        <input type="text" id="registro_numero" class="form-control"
               v-model="registro.NUMERO">
    </div>
    <div class="col-md-3">
        <label for="perfil_complemento" class="caption">Complemento</label>
        <input type="text" id="registro_complemento" class="form-control"
               v-model="registro.COMPLEMENTO">
    </div>                            
</div>
<div class="row">                          
    <div class="col-md-4">
        <label for="perfil_bairro" class="caption">Bairro</label>
        <input type="text" id="registro_bairro" class="form-control"
               v-model="registro.BAIRRO">
    </div>
    <div class="col-md-4">
        <label for="perfil_cidade" class="caption">Cidade</label>
        <input type="text" id="registro_cidade" class="form-control"
               v-model="registro.CIDADE">
    </div> 
    <div class="col-md-2">
        <label for="perfil_estado" class="caption">Estado</label>
        <input type="text" id="registro_estado" class="form-control"
               maxlength="2"
               style="text-transform: uppercase;"
               v-model="registro.ESTADO">
    </div>
    <div class="col-md-2">
        <label class="caption">CEP </label>
        <div class="input-group mb-3">                                            
            <input type="text" id="cep" class="form-control cep"
               v-model="registro.CEP">
        </div>
    </div>
</div>
<!-- FIM ENDERECO -->

<div class="row" style="margin-top: 10px;">
    <div class="col-md-12">
        <input type="checkbox" 
               v-model="registro.ATIVO"
               true-value="S"
               false-value="N"
               > Cadastro está ativo <small>(cliente pode logar no sistema)</small>
    </div>    
</div>
