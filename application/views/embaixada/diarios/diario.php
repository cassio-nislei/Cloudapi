<?php     
    if (!pode_ler('filiais', FALSE)) {
        echo '<br><center>Sem permissão para acessar este recurso!</center>';
        return false;
    }
    
    $lista_fp = edf_lista_formas_pag(TRUE);
?>

<?php 
    //mask, datepiker, etc
    include_once APPPATH . 'views/templates/regions/js/inputs.php';
?>
<br>
<div id="appModelo" style="height: 100%;">
    <div id="divLoading" v-show="loading" style="width: 100%;">
        <img id="imgLoading" src="<?= base_url('images/loading1.gif') ?>" 
             style="display: none; position: absolute; left: 50%; top: 50%; margin-top:-130px; margin-left: -45px; width: 100px;"
             >        
    </div>
    <div v-show="!loading" style="display: none;">
        <div class="row">
            <div class="col-md-12">
                <h3> <i class="bi bi-file-earmark-text"></i> Relatório de Culto
                <?php 
                    if ($diario_id) {
                        if (edf_diario_istemp($diario_id)) {
                            ?>
                            <span class="badge badge-danger">Rascunho</span>    
                            <?php
                        }
                    }
                ?>
                </h3>
            </div>            
        </div>
        
        <?php 
            $filial = edf_get_filial_by_diario($diario_id);
            if ($filial) {
                ?>
                <br>
                <div class="card">
                    <div class="card-header ccheader">
                        FILIAL
                    </div>
                    <div class="card-body" style="font-weight: bold;">   
                        <?= (!empty($filial->cgc)) ? formata_cgc($filial->cgc) . ' - ' : '' ?> <?= is5_strtoupper($filial->nome) ?>                        
                    </div>
                </div>    
                <?php
            }        
        ?>

        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header ccheader">
                        DADOS BÁSICOS
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="data" class="caption">Data</label>
                                <input type="text" id="data" class="form-control datepicker">                                               
                            </div>

                            <div class="col-md-2">
                                <label for="hora" class="caption">Hora</label>
                                <select id="hora" v-model="registro.hora" class="form-control">
                                    <option v-for="hora in horas" 
                                            v-bind:value="hora">
                                        {{hora}}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label for="pregador" class="caption">Pregador</label>
                                <input type="text" v-model="registro.pregador" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label for="visitantes" class="caption">Visitantes</label>
                                <input type="tel" v-model="registro.visitantes" class="form-control"                                   
                                       onkeypress="return somenteNumeros(event);"
                                       maxlength="5"
                                       >
                            </div>

                            <div class="col-md-2">
                                <label for="conversoes" class="caption">Conversões</label>
                                <input type="tel" v-model="registro.conversoes" class="form-control"                                   
                                       onkeypress="return somenteNumeros(event);"
                                       maxlength="5"
                                       >
                            </div>

                            <div class="col-md-2">
                                <label for="criancas_ate12" class="caption">Crianças <small>(até 12 anos)</small></label>
                                <input type="tel" v-model="registro.criancas_ate12" class="form-control"                                   
                                       onkeypress="return somenteNumeros(event);"
                                       maxlength="5"
                                       >
                            </div>

                            <div class="col-md-2">
                                <label for="total_pessoas" class="caption">Total Pessoas</label>
                                <input type="tel" v-model="registro.total_pessoas" class="form-control"                                   
                                       onkeypress="return somenteNumeros(event);"
                                       maxlength="5"
                                       >
                            </div>
                        </div>
                        <br>
                    </div>
                </div>

                <br>

                <?php 
                    include_once APPPATH . 'views/embaixada/diarios/regions/pastores/pastores_table.php';
                    include_once APPPATH . 'views/embaixada/diarios/regions/diaconos/diaconos_table.php';                    
                    include_once APPPATH . 'views/embaixada/diarios/regions/apresentacoes/apresentacoes_table.php';
                    include_once APPPATH . 'views/embaixada/diarios/regions/visitantes/visitantes_table.php';
                    include_once APPPATH . 'views/embaixada/diarios/regions/dizimos/dizimos_table.php';
                    include_once APPPATH . 'views/embaixada/diarios/regions/ofertas/ofertas_table.php';
                    include_once APPPATH . 'views/embaixada/diarios/regions/especiais/especiais_table.php';
                    include_once APPPATH . 'views/embaixada/diarios/regions/missoes/missoes_table.php';    
                ?>    
                
                <br>
                <div class="card">
                    <div class="card-header ccheader" style="background-color: #096e97; color: #ffffff;">
                        TOTAL POR FORMAS DE PAGAMENTO
                    </div>
                    <div class="card-body">
                        <table style="width: 100%;">
                            <tr>
                                <td v-for="p in totais.pagamentos">
                                    <div>{{p.nome}}</div>
                                    <div>{{toCurrency(p.valor)}}</div>
                                </td> 
                            </tr>
                        </table>
                    </div>
                </div>
                
                <br>
                <div class="card">
                    <div class="card-header ccheader" style="background-color: #096e97; color: #ffffff;">
                        TOTAL POR ARRECADAÇÃO
                    </div>
                    <div class="card-body">
                        <table style="width: 100%;">
                            <tr>
                                <td v-for="c in totais.categorias">
                                    <div>{{c.nome}}</div>
                                    <div>{{toCurrency(c.valor)}}</div>
                                </td> 
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-hti" style="margin-top: 25px; width: 12em;"
                                v-on:click="salvar()"><i class="bi bi-check-circle"></i>
                            Salvar
                        </button>

                        <button class="btn btn-hti-out" style="margin-top: 25px; width: 12em; margin-left: 1em;"
                                v-on:click="imprimir()"><i class="bi bi-printer"></i>
                            Imprimir
                        </button>
                    </div>
                </div>                                    

            </div>
        </div>
    </div>
    
    <?php 
        include_once APPPATH . 'views/embaixada/diarios/regions/diaconos/diacono_modal.php';
        include_once APPPATH . 'views/embaixada/diarios/regions/pastores/pastor_modal.php';
        include_once APPPATH . 'views/embaixada/diarios/regions/apresentacoes/apresentacao_modal.php';
        include_once APPPATH . 'views/embaixada/diarios/regions/visitantes/visitante_modal.php';
        include_once APPPATH . 'views/embaixada/diarios/regions/dizimos/dizimo_modal.php';
        include_once APPPATH . 'views/embaixada/diarios/regions/missoes/missao_modal.php';
        include_once APPPATH . 'views/embaixada/diarios/regions/ofertas/oferta_modal.php';
        include_once APPPATH . 'views/embaixada/diarios/regions/especiais/especial_modal.php';
    ?>
    
</div>

<script src="<?= base_url('assets/js/edf/diaconos.js?v='.uniqid()) ?>"></script>
<script src="<?= base_url('assets/js/edf/visitantes.js?v='.uniqid()) ?>"></script>
<script src="<?= base_url('assets/js/edf/pastores.js?v='.uniqid()) ?>"></script>
<script src="<?= base_url('assets/js/edf/apresentacoes.js?v='.uniqid()) ?>"></script>
<script src="<?= base_url('assets/js/edf/dizimos.js?v='.uniqid()) ?>"></script>
<script src="<?= base_url('assets/js/edf/missoes.js?v='.uniqid()) ?>"></script>
<script src="<?= base_url('assets/js/edf/ofertas.js?v='.uniqid()) ?>"></script>
<script src="<?= base_url('assets/js/edf/especiais.js?v='.uniqid()) ?>"></script>

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
        el: "#appModelo",  
        mixins: [mxFunctions, mxDiaconos, mxPastores, mxVisitantes, mxApresentacoes,
            mxDizimos, mxMissoes, mxOfertas, mxEspeciais,
        ],
        data: {            
            registro: {},                  
            filtro: '',                                
            loading: true,
            alias: '',
            status: '',  
            id: '<?= (int)$diario_id ?>',
            horas: <?= json_encode(get_horas_range()) ?>, 
            formas_pag: <?= json_encode(edf_lista_formas_pag()) ?>,
            totais: [],
    
        },        
        methods: {
            getDados() {                
                $.get(this.base_url+'Diarios/get/'+this.id)
                .done(result => {                         
                    this.loading = false;  
                    let obj = JSON.parse(result);
                    if (obj) {
                        if (obj.status) {
                            this.registro = obj.data;                             
                            $("#data").val(this.registro.data);
                            $("#hora").val(this.registro.hora);    
                            this.getTotais();
                        } 
                    } else {
                        alert(result);
                    }
                })
                .fail(data => {
                    alert(JSON.stringify(data));
                });                
            },
            
            refreshDadosTotal() {                
                $.get(this.base_url+'Diarios/get/'+this.id)
                .done(result => {                                             
                    let obj = JSON.parse(result);
                    if (obj) {
                        if (obj.status) {
                            this.registro.total_dizimos   = obj.data.total_dizimos;                             
                            this.registro.total_gerais    = obj.data.total_gerais;
                            this.registro.total_especiais = obj.data.total_especiais;
                            this.registro.total_missoes   = obj.data.total_missoes;
                            this.registro.total_final     = obj.data.total_final;
                        } 
                    }
                })
                .fail(data => {
                    alert(JSON.stringify(data));
                });                
            },
                    
            salvar() {                    
                this.registro.data = $("#data").val();
                this.registro.hora = $("#hora").val();                              
                
                $.post(this.base_url+'Diarios/salvar', {                     
                    registro: this.registro 
                })
                .done(result => {  
                    console.log(result);
                    let obj = JSON.parse(result);
                    if (obj) {
                        if (obj.status) {
                            //showToast(obj.msg, false);                                
                            window.location.href = this.base_url+'diarios/'+obj.data.id;
                        } else {
                            alert(obj.msg);
                        } 
                    } else {
                        alert(result);
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
                        this.registro.endereco = obj.logradouro;
                        this.registro.complemento = obj.complemento;
                        this.registro.bairro = obj.bairro;
                        this.registro.cidade = obj.localidade;
                        this.registro.estado = obj.uf;           
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
            
            getTotais() { 
                this.totais = [];
                $.get(this.base_url+'Diarios/getTotais/'+this.id)
                .done(result => {                                         
                    let obj = JSON.parse(result);
                    if (obj) {
                        if (obj.status) {
                            this.totais = obj.data;
                        } 
                    } else {
                        alert(result);
                    }
                })
                .fail(data => {
                    alert(JSON.stringify(data));
                });                
            },
            
            imprimir() {                
                window.location.href = this.base+'diarios/pdf/'+this.id;
            }
            
        },        
        mounted() {      
            this.getDados();              
            
            if (parseInt(this.id) > 0) {
                this.getDiaconos();
                this.getPastores();
                this.getApresentacoes();
                this.getVisitantes();
                this.getDizimos();
                this.getMissoes();
                this.getOfertas();
                this.getEspeciais();
            } else {                
                $("#data").val(this.dateToStr(new Date()));
            }           
        }
    });
</script>

