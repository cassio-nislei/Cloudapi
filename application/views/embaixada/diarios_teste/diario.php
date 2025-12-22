<style>
    input {
        text-transform: uppercase;
    }
</style>

<?php     
    if (!pode_ler('relatorios', FALSE)) {
        echo '<br><center>Sem permissão para acessar este recurso!</center>';
        return false;
    }
    
    $lista_fp = edf_lista_formas_pag(TRUE);
    
    $editavel = TRUE;
    
    if ($diario->data) {
        //tesoureiro somente pode editar relatorios gerados no mesmo dia
        if (isTesoureiro()) {
            if ($diario->data !== getDateCurrent()) {
                $editavel = FALSE;
                ?>
                <div style="width: 100%; text-align: center; font-weight: bold; color: red; margin-top: 20px;">
                    Tesoureiros somente podem editar relatórios gerados no mesmo dia.
                </div>    
            <?php  
            }
        }
        
        //pastores somente podem editar relatorios dentro do prazo limite
        if (isPastor()) {
            $data   = new DateTime($diario->data);
            $hoje   = new DateTime(getDateCurrent());
            $limite = edf_diario_limite_dias_edicao();
            $diff   = $data->diff($hoje)->format("%a"); //ret valor absluto (sem sinal)

            if ($diff > $limite) {
                $editavel = FALSE;
                ?>
                <div style="width: 100%; text-align: center; font-weight: bold; color: red; margin-top: 20px;">
                    Este relatório não pode ser mais editado. Prazo de <?= $limite ?> dias ultrapassado.
                </div>    
                <?php            
            }
        }
    }    
    
?>

<?php 
    //mask, datepiker, etc
    include_once APPPATH . 'views/templates/regions/js/inputs.php';
?>

<style>
    .div-alteracoes {
        margin: 5px; 
        padding: 5px; 
        color: red; 
        background-color: #fff;
        border-radius: 5px;
        border: 1px solid #f8fafd;        
    }    
</style>

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

        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header ccheader">
                        Dados Básicos
                        <span class="pull-right">
                            <?= Zeros($diario_id, 5) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="data" class="caption">Data</label>
                                <input type="text" id="data" class="form-control datepicker"
                                       <?= $editavel ?  '' : 'disabled="disabled"' ?>
                                       >                                               
                            </div>

                            <div class="col-md-2">
                                <label for="hora" class="caption">Hora</label>
                                <select id="hora" v-model="registro.hora" class="form-control"
                                        <?= $editavel ?  '' : 'disabled="disabled"' ?>
                                        >
                                    <option v-for="hora in horas" 
                                            v-bind:value="hora">
                                        {{hora}}
                                    </option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="adultos" class="caption">Adultos</label>
                                <input type="tel" v-model="registro.adultos" class="form-control"                                   
                                       onkeypress="return somenteNumeros(event);"
                                       maxlength="5"
                                       v-on:change="calcTotalPessoas()"
                                       <?= $editavel ?  '' : 'disabled="disabled"' ?>
                                       >
                            </div>
                            
                            <div class="col-md-2">
                                <label for="criancas_ate12" class="caption">Crianças <small>(até 12 anos)</small></label>
                                <input type="tel" v-model="registro.criancas_ate12" class="form-control"                                   
                                       onkeypress="return somenteNumeros(event);"
                                       maxlength="5"
                                       v-on:change="calcTotalPessoas()"
                                       <?= $editavel ?  '' : 'disabled="disabled"' ?>
                                       >
                            </div>
                            
                            <div class="col-md-3">
                                <label for="total_pessoas" class="caption">Total Pessoas</label>
                                <input id="total_pessoas" type="tel" v-model="registro.total_pessoas" class="form-control"                                   
                                       onkeypress="return somenteNumeros(event);"
                                       maxlength="5"
                                       disabled="disabled"
                                       >
                            </div>
                            
                        </div>

                        <div class="row">
                            <div class="col-md-7">
                                <label for="pregador" class="caption">Pregador</label>
                                <input type="text" v-model="registro.pregador" class="form-control"
                                       <?= $editavel ?  '' : 'disabled="disabled"' ?>
                                       >
                            </div>
                            
                            <div class="col-md-2">
                                <label for="visitantes" class="caption">Visitantes</label>
                                <input type="tel" v-model="registro.visitantes" class="form-control"                                   
                                       onkeypress="return somenteNumeros(event);"
                                       maxlength="5"
                                       <?= $editavel ?  '' : 'disabled="disabled"' ?>
                                       >
                            </div>

                            <div class="col-md-3">
                                <label for="conversoes" class="caption">Conversões</label>
                                <input type="tel" v-model="registro.conversoes" class="form-control"                                   
                                       onkeypress="return somenteNumeros(event);"
                                       maxlength="5"
                                       <?= $editavel ?  '' : 'disabled="disabled"' ?>
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
                    <div class="card-header ccheader" style="background-color: #096e97; color: #fff;">
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
                    <div class="card-header ccheader" style="background-color: #096e97; color: #fff;">
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
                        <button class="btn <?= $editavel ? 'btn-hti' : 'btn-hti-disabled' ?>" style="margin-top: 25px; width: 12em;"
                                v-on:click="salvar()"
                                <?= $editavel ? '' : 'disabled="disabled"' ?>
                                >
                            <i class="bi bi-check-circle"></i>
                            Salvar
                        </button>
                        
                        
                        <button v-if="registro.status === 'C'" class="btn btn-hti-out" style="margin-top: 25px; width: 12em; margin-left: 1em;"
                                v-on:click="imprimir()"><i class="bi bi-printer"></i>
                            Imprimir
                        </button>
                        <button v-else class="btn btn-hti-disabled" style="margin-top: 25px; width: 12em; margin-left: 1em;"
                                disabled="disabled"><i class="bi bi-printer"></i>
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
                $.get('http://localhost/fgportal/Diarios/get/'+this.id)
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
                    
            salvar() { 
                <?php if (isTesoureiro()): ?>
                //if (this.registro.status !== 'C') {
                //    if (!confirm('Deseja realmente confirmar este relatório? Você não poderá editá-lo novamente.')) {
                //        return false;
                //    }
                //}        
                <?php endif; ?>
                
                this.registro.data = $("#data").val();
                this.registro.hora = $("#hora").val();                              
                
                $.post('http://localhost/fgportal/Diarios/salvar', {                     
                    registro: this.registro 
                })
                .done(result => {  
                    console.log(result);
                    let obj = JSON.parse(result);
                    if (obj) {
                        if (obj.status) {
                            //showToast(obj.msg, false);                                
                            window.location.href = 'http://localhost/fgportal/diarios/'+obj.data.id;
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
                $.get('http://localhost/fgportal/Diarios/getTotais/'+this.id)
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
                if (this.registro.status === 'C') {
                    let url = 'http://localhost/fgportal/diarios/pdf/'+this.id;
                    window.open(url, 'blank_');
                } else {
                    alert('O relatório ainda não foi Confirmado.');
                }
            },
                    
            temPendencias() {
                return this.dizimos_alt.length > 0 || 
                       this.ofertas_alt.length > 0 ||
                       this.missoes_alt.length > 0 ||
                       this.especiais_alt.length > 0;
            },
                    
            calcTotalPessoas() {
                let a = parseInt(this.registro.adultos || 0);
                let c = parseInt(this.registro.criancas_ate12 || 0);                
                this.registro.total_pessoas = (a + c);                
            },
                
            <?php if (isAdmin()): ?>
            excluirAlteracao(id, tabela) { 
                if (!confirm('Deseja realmente excluir este registro agora?')) {
                    return false;
                }            
                this.loading_dizimos = true;
                $.post('http://localhost/fgportal/Diarios/excluirAlteracao', {                     
                    id: id,
                    diario_id: this.id,
                })
                .done(result => {  
                    this.loading_dizimos = false;
                    let obj = JSON.parse(result);
                    if (obj) {
                        if (obj.status) {                            
                            if (tabela === 'dizimos') {
                                this.getDizimos();
                            } else
                            if (tabela === 'ofertas') {
                                this.getOfertas();
                            } else
                            if (tabela === 'especiais') {
                                this.getEspeciais();
                            } else
                            if (tabela === 'missoes') {
                                this.getMissoes();
                            }
                        } else {
                            alert(obj.msg);
                        } 
                    } else {
                        alert(result);
                    }
                })
                .fail(err => {
                    this.loading_dizimos = false;
                    alert(JSON.stringify(err));
                });                
            },   
            <?php endif; ?>
                
            
            
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

