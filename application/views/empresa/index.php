<?php 
    if (!pode_ler('empresa', FALSE)) {
        echo '<br><center>Sem permissão para acessar este recurso!</center>';
        return false;
    }
?>

<?php 
    //mask, datepiker, etc
    include_once APPPATH . 'views/templates/regions/js/inputs.php';
?>
<div id="appPerfil" style="height: 100%;">
    <div id="divLoading" v-show="loading" style="width: 100%;">
        <img id="imgLoading" src="<?= base_url('images/loading1.gif') ?>" 
             style="display: none; position: absolute; left: 50%; top: 50%; margin-top:-130px; margin-left: -45px; width: 100px;"
             >        
    </div>
    <div v-show="!loading" style="display: none;">
        <div class="row">
            <div class="col-md-12">
                <h3> <span class="fa fa-cogs"></span> EMPRESA</h3>
            </div>            
        </div>

        <br>
        <div class="col-md-12">
            <div class="custom-tab">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="perfil-loja-tab" data-toggle="tab" href="#perfil-loja" role="tab" aria-controls="custom-nav-home" aria-selected="true">Loja</a>
                        <a class="nav-item nav-link" id="perfil-endereco-tab" data-toggle="tab" href="#perfil-endereco" role="tab" aria-controls="custom-nav-profile" aria-selected="false">Endereço</a>                                                
                        <a class="nav-item nav-link" id="perfil-config-tab" data-toggle="tab" href="#perfil-config" role="tab" aria-controls="custom-nav-profile" aria-selected="false">Configurações</a>                                                
                        <!-- <a class="nav-item nav-link" id="perfil-integracao-tab" data-toggle="tab" href="#perfil-integracao" role="tab" aria-controls="custom-nav-profile" aria-selected="false">Integração</a> -->
                    </div>
                </nav>
                <div class="tab-content pl-3 pt-2" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="perfil-loja" role="tabpanel" aria-labelledby="perfil-loja-tab">
                        <?php /*
                        <div class="row">
                            <div class="col-md-12">
                                <label for="basic-url" class="caption">
                                    Personalise sua URL<br><small>Somente letras e números, sem espaços ou acentos. Demais caracteres serão ignorados.</small>
                                </label>
                                <div class="input-group mb-3">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text" 
                                          id="basic-addon3">
                                        <?= isMobile() ? '...' : 'https://nextingresso.com.br/p/' ?>                                        
                                    </span>
                                  </div>
                                  <input type="text" class="form-control" 
                                         id="perfil_alias" 
                                         aria-describedby="basic-addon3"
                                         v-model="loja.alias"
                                         >
                                </div>
                            </div>
                        </div>
                        */ ?> 
                        <div class="row">
                            <div class="col-md-3">                            
                                <!-- LOGOMARCA -->
                                <label class="caption">Logo</label>
                                <div class="prod-img" style="border: 3px dotted silver; border-radius: 5px;">
                                    <center>                                
                                        <img id="logo"
                                             v-bind:src="loja.logo" 
                                             style="width: 200px;">                                
                                    </center>
                                    <div class="prod-img-up">
                                        <label for="file_logo">
                                            <span class="fa fa-upload" style="margin-right: 120px;"></span>
                                        </label>
                                        <span class="fa fa-trash" onclick="removerLogo();"></span>
                                    </div> 
                                </div>
                                <div style="text-align: center; font-size: 12px; margin-bottom: 10px;">(Tamanho ideal: 500 x 500px)</div>
                                <input type="file" id="file_logo" style="display: none;" />                               
                                <!-- LOGO FIM -->
                            </div>                        
                            <div class="col-md-9">
                                <!-- BANNER -->
                                <label class="caption">Banner</label>
                                <div class="prod-img" style="border: 3px dotted silver; border-radius: 5px;">
                                    <center>                                
                                        <img id="banner"
                                             v-bind:src="loja.banner" 
                                             style="min-width: 200px; height: 200px;">

                                    </center>
                                    <div class="prod-img-up">
                                        <label for="file_banner">
                                            <span class="fa fa-upload" style="margin-right: 120px;"></span>
                                        </label>
                                        <span class="fa fa-trash" onclick="removerBanner();"></span>
                                    </div> 
                                </div>
                                <input type="file" id="file_banner" style="display: none;" />                               
                                <!-- BANNER FIM -->
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label for="perfil_nome" class="caption">Nome/Razão Social</label>
                                <input type="text" id="nome" name="perfil_nome" class="form-control"
                                       v-model="loja.nome">
                            </div>
                            <div class="col-md-6">
                                <label for="perfil_cgc" class="caption">CPF/CNPJ</label>
                                <input type="text" id="perfil_cgc" name="perfil_cgc" class="form-control"
                                       onkeypress="return somenteNumeros(event);"
                                       maxlength="14"
                                       v-model="loja.cgc">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="perfil_fantasia" class="caption">Fantasia</label>
                                <input type="text" id="perfil_fantasia" name="perfil_fantasia" class="form-control"
                                       v-model="loja.fantasia"
                                       >
                            </div>                                                        
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="perfil_telefone" class="caption">Telefone</label>
                                <input type="text" id="perfil_telefone" name="perfil_telefone" 
                                       class="form-control telefone"                                       
                                       >
                            </div>
                            <div class="col-md-3">
                                <label for="perfil_celular" class="caption">Celular</label>
                                <input type="text" id="perfil_celular" name="perfil_celular" 
                                       class="form-control celular"
                                       >
                            </div>  
                            <div class="col-md-6">
                                <label for="perfil_email" class="caption">E-mail</label>
                                <input type="text" id="perfil_email" name="perfil_email" 
                                       class="form-control" style="text-transform: lowercase;"
                                       v-model="loja.email">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label for="perfil_facebook" class="caption">Facebook</label>
                                <div class="input-group mb-3">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text" 
                                          id="basic-addon3">
                                          <?= isMobile() ? '...' : 'https://facebook.com/' ?>                                        
                                    </span>
                                  </div>
                                  <input type="text" id="perfil_email" name="perfil_facebok" 
                                       class="form-control" style="text-transform: lowercase;"
                                       maxlength="50"
                                       v-model="loja.facebook">
                                </div>
                            </div>                        
                            <div class="col-md-6">
                                <label for="perfil_instagram" class="caption">Instagram</label>
                                <div class="input-group mb-3">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text" 
                                          id="basic-addon3">
                                          <?= isMobile() ? '...' : 'https://instagram.com/' ?>                                        
                                    </span>
                                  </div>
                                  <input type="text" id="perfil_email" name="perfil_instagram" 
                                       class="form-control" style="text-transform: lowercase;"
                                       maxlength="50"
                                       v-model="loja.instagram">
                                </div>
                            </div>                           
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <label for="perfil_descricao" class="caption">Descrição</label>
                                <textarea id="perfil_descricao" name="perfil_descricao" rows="5" class="form-control"
                                          v-model="loja.descricao"></textarea>
                            </div>
                        </div>                        
                        
                    </div>
                    <div class="tab-pane fade show" id="perfil-endereco" role="tabpanel" aria-labelledby="perfil-endereco-tab">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="caption">CEP <small>{{status}}</small></label>
                                <div class="input-group mb-3">                                            
                                    <input type="tel" id="perfil_cep" class="form-control cep"                                                                                      
                                           >
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                                v-on:click="consultarCep()"
                                                >
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="perfil_endereco" class="caption">Endereço</small></label>
                                <input type="text" id="perfil_endereco" class="form-control"
                                       v-model="loja.endereco">
                            </div> 
                            <div class="col-md-2">
                                <label for="perfil_numero" class="caption">Número</label>
                                <input type="text" id="perfil_numero" class="form-control"
                                       v-model="loja.numero">
                            </div>
                            <div class="col-md-3">
                                <label for="perfil_complemento" class="caption">Complemento</label>
                                <input type="text" id="perfil_complemento" class="form-control"
                                       v-model="loja.complemento">
                            </div>                            
                        </div>
                        <div class="row">                          
                            <div class="col-md-5">
                                <label for="perfil_bairro" class="caption">Bairro</label>
                                <input type="text" id="perfil_bairro" class="form-control"
                                       v-model="loja.bairro">
                            </div>
                            <div class="col-md-4">
                                <label for="perfil_cidade" class="caption">Cidade</label>
                                <input type="text" id="perfil_cidade" class="form-control"
                                       v-model="loja.cidade">
                            </div> 
                            <div class="col-md-1">
                                <label for="perfil_estado" class="caption">Estado</label>
                                <input type="text" id="perfil_estado" class="form-control"
                                       v-model="loja.estado">
                            </div>                            
                        </div>                        
                    </div>       
                    <div class="tab-pane fade show" id="perfil-config" role="tabpanel" aria-labelledby="perfil-config-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="caption">O relatório diário poderá ser editado em até quantos dias?</label>
                                <select v-model="loja.dias_fechar_diario" class="form-control" style="width: 200px;">
                                    <option value="1">1 dia</option>
                                    <option value="2">2 dias</option>
                                    <option value="3">3 dias</option>
                                    <option value="4">4 dias</option>
                                    <option value="5">5 dias</option>
                                    <option value="6">6 dias</option>
                                    <option value="7">7 dias</option>
                                    <option value="8">8 dias</option>
                                    <option value="9">9 dias</option>
                                    <option value="10">10 dias</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php /*
                    <div class="tab-pane fade show" id="perfil-integracao" role="tabpanel" aria-labelledby="perfil-integracao-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="user_htipay" class="caption" style="font-size: 14px;">E-mail</label>
                                <input type="text" id="user_htipay" name="user_htipay"
                                       class="form-control"
                                       >
                            </div>
                            <div class="col-md-6">
                                <label for="token_htipay" class="caption" style="font-size: 14px;">Token</label>
                                <input type="text" id="token_htipay" name="token_htipay"
                                       class="form-control"
                                       >                    
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-hti" style="margin-top: 25px;"
                                        v-on:click="configurarHtiPay()"
                                        >
                                    <span class="fa fa-check"></span> Confirmar
                                </button>
                            </div>
                        </div>
                    </div>
                    */ ?>
                </div>
            </div>            
        </div>
        <div class="row">
            <div class="col-md-12" style="text-align: center;">
                <button class="btn btn-hti" style="margin-top: 25px; width: 120px;"
                        v-on:click="updateEmitente()">
                    Salvar
                </button>
            </div>
        </div>
    </div>
    
    
    
</div>

<script>
    function setAltura() {        
        $("#divLoading").css('height',window.innerHeight+'px');
        $("#imgLoading").css('display','block');
    }
    
    window.addEventListener('resize', function(){
        setAltura();
    });
    
    setAltura();
    
    var app = new Vue({
        el: "#appPerfil",  
        data() {
            return {
                loja: {},                  
                filtro: '',                                
                loading: true,
                alias: '',
                status: '',                                
            }            
        },
        mixins: [mxFunctions],
        methods: {
            getDadosEmitente() {                
                $.get(this.base_url+'Empresa')
                .done(obj => { 
                    console.log(obj);
                    this.loading = false;                        
                    if (obj.status) {
                        this.loja = obj.data;  

                        //salva para verificar se mudou depois
                        this.alias = this.loja.alias;

                        $("#perfil_cep").val(this.loja.cep);

                        $("#user_htipay").val(this.loja.user_htipay);
                        $("#token_htipay").val(this.loja.token_htipay);

                        $("#perfil_telefone").val(this.loja.telefone);
                        $("#perfil_celular").val(this.loja.celular);
                    } else {
                        alert(obj.msg);
                    }
                })
                .fail(data => {
                    alert(JSON.stringify(data));
                });
                
            },
                    
            updateEmitente() {                    
                this.loja.telefone = $("#perfil_telefone").val();
                this.loja.celular = $("#perfil_celular").val();
                this.loja.cep = $("#perfil_cep").val();
                this.loja.logo = $("#logo").attr('src');
                this.loja.banner = $("#banner").attr('src');                 
                
                $.post(this.base_url+'Empresa', { 
                    action: 'update',
                    registro: this.loja 
                })
                .done(obj => {
                    alert(obj.msg);
                    if (obj.status === 'OK') {
                        //showToast(obj.msg, false);                                
                        window.location.reload();
                    } 
                })
                .fail(err => {
                    alert(JSON.stringify(err));
                });
                
            },     
                    
            consultarCep() {
                this.status = 'Consultando...';
                let cep = $("#perfil_cep").val();
                let url = 'https://viacep.com.br/ws/'+cep+'/json'; 
                console.log('URL',url);
                $.get(url)
                  .done(obj => {                     
                    this.status = '';  
                    if (!obj.erro) {
                        this.loja.endereco = obj.logradouro;
                        this.loja.complemento = obj.complemento;
                        this.loja.bairro = obj.bairro;
                        this.loja.cidade = obj.localidade;
                        this.loja.estado = obj.uf;           
                    } else {
                        alert('Erro ao consultar dados. Verifique o CEP.');
                    }
                  })
                  .fail(error => {
                      this.status = '';
                      alert('Erro: '+JSON.stringify(error));
                  })
                  .always(function() {
                      this.status = '';
                      //alert( "finished" );
                  });
            },
                    
            
        },        
        mounted() {  
            //$("#user_htipay").val('<?= $this->session->userdata('emit.user_htipay') ?>');
            //$("#token_htipay").val('<?= $this->session->userdata('emit.token_htipay') ?>');
            
            this.getDadosEmitente();            
        }
    });
</script>

<script>
    window.onload = function () {        
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            //LOGO
            var fileSelected1 = document.getElementById('file_logo'); 
            fileSelected1.addEventListener('change', function(e) {                
                var fileExtension = /image.*/;                
                var fileTobeRead = fileSelected1.files[0];                
                if (fileTobeRead.type.match(fileExtension)) {
                    var fileReader = new FileReader();
                    fileReader.onload = function(e) {                                  
                        document.getElementById('logo').src = fileReader.result;    
                        document.getElementById('hidden_logo').value = fileReader.result;
                    };
                    fileReader.readAsDataURL(fileTobeRead);
                }
                else {
                    alert("Por favor, selecione uma imagem para a logomarca.");
                }
            }, false);
                        
            //BANNER
            var fileSelected_item = document.getElementById('file_banner'); 
            fileSelected_item.addEventListener('change', function(e) {                
                var fileExtension = /image.*/;                
                var fileTobeRead = fileSelected_item.files[0];                
                if (fileTobeRead.type.match(fileExtension)) {
                    var fileReader = new FileReader();
                    fileReader.onload = function(e) {                        
                        document.getElementById('banner').src = fileReader.result;    
                        document.getElementById('hidden_banner').value = fileReader.result;
                    };
                    fileReader.readAsDataURL(fileTobeRead);
                }
                else {
                    alert("Por favor, selecione uma imagem para o banner.");
                }
            }, false);
        }
        else {
            alert("Arquivo(s) não suportado(s)");
        }
    }
    
    function removerLogo() {
        var src = document.getElementById('logo').src;
        if (src.indexOf('images/sem_imagem.jpg') > 0) {
            alert('Sem imagem para excluir.');
            return false;
        }
        
        if (confirm('Remover esta imagem?')) {
            document.getElementById('logo').src = '<?= base_url('images/sem_imagem.jpg') ?>';                
            alert('Para confirmar a exclusão da logomarca, clique em Salvar.');
        }
    }
    
    function removerBanner() {
        var src = document.getElementById('banner').src;
        if (src.indexOf('images/sem_imagem.jpg') > 0) {
            alert('Sem imagem para excluir.');
            return false;
        }
        
        if (confirm('Remover esta imagem?')) {
            document.getElementById('banner').src = '<?= base_url('images/sem_imagem.jpg') ?>';                
            alert('Para confirmar a exclusão do banner, clique em Salvar');
        }
    }
</script>
