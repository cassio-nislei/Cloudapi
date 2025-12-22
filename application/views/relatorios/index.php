<?php 
    //mask, datepiker, etc
    include_once APPPATH . 'views/templates/regions/js/inputs.php';
    
    $filiais = edf_lista_filiais();
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- 4.3.0 -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>



<style>
    .hheader {
        font-weight: bold; background-color: #f8fafd; color: #000; border: none; text-transform: uppercase;
    }
</style>

<div id="appDesempenho" >
    <div class="row">
        <div class="col-md-12">
            <h3> <span class="fa fa-area-chart"></span> RELATÓRIO ESTATÍSTICO</h3>
        </div>            
    </div>
    <br>
    
    <div class="row" style="margin-bottom: 25px;">
        <div class="col-md-2">
            <label style="font-size: 14px; font-weight: bold;">Início</label>
            <input id="data_inicial" 
                   type="tel" class="form-control datepicker" 
                   >
        </div>
        <div class="col-md-2">
            <label style="font-size: 14px; font-weight: bold;">Fim</label>
            <input id="data_final" 
                   type="tel" class="form-control datepicker" 
                   >
        </div>
        <?php if (isset($filiais) && is5_count($filiais) > 0): ?>
        <div class="col-md-6">
            <label style="font-size: 14px; font-weight: bold;">Filial</label>
            <select id="filial_id" class="form-control">
                <option value="0">-- TODAS --</option>
                <?php 
                    foreach($filiais as $f) {
                        ?>
                        <option value="<?= $f->id ?>"><?= $f->nome ?></option>    
                        <?php
                    }
                ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="col-md-2">
            <button class="btn btn-hti" style="margin-top: 28px; width: 100%;"                    
                    v-on:click="filtrar()">
                <span class="fa fa-search"></span>
                Filtrar
            </button>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header hheader">
                    POR TIPO DE ARRECADAÇÃO
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td style="width: 200px;">
                                <div>Total do período</div>
                                <div style="color: #000; font-weight: bold; font-size: 20px;">R$ {{total_arrecadacao}}</div>
                            </td>                            
                        </tr>
                    </table>
                    <canvas id="myChartArrecadacoes" ></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header hheader">
                    POR TIPO DE ENTRADA
                </div>
                <div class="card-body">
                    <table>
                        <tr>                            
                            <td>
                                <div>Total do período</div>
                                <div style="color: #000; font-weight: bold; font-size: 20px;">R$ {{total_fp}}</div>
                            </td>
                        </tr>
                    </table>
                    <canvas id="myChartFormasPag"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <br><br>
    <!-- POR PESSOAS -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header hheader">
                    PESSOAS
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td style="width: 200px;">
                                <div>Total de pessoas</div>
                                <div style="color: #000; font-weight: bold; font-size: 20px;">{{total_pessoas}}</div>
                            </td>                            
                        </tr>
                    </table>
                    <canvas id="myChartPessoas" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header hheader">
                    PESSOAS POR FILIAIS
                </div>
                <div class="card-body">
                    <table>
                        <tr>                            
                            <td>
                                <div>Total de pessoas por filiais</div>
                                <div style="color: green; font-weight: bold; font-size: 20px;">{{total_pessoas_filiais}}</div>
                            </td>
                        </tr>
                    </table>
                    <canvas id="myChartPessoasFiliais" height="100"></canvas>
                </div>
            </div>
        </div>
        
    </div>
    <!-- FIM POR PESSOAS -->
    <br><br><br>
    
</div>

<script>
    var appDesempenho = new Vue({
        el:"#appDesempenho",
        mixins: [mxFunctions],
        data: {
            pedido_total_periodo: '0.00',
            pedido_qtd_periodo: '0',
            pedido_total_periodo_mes: '0.00',
            pedido_qtd_periodo_mes: '0',
            total_arrecadacao: '0.00',
            total_fp: '0.00',
            total_pessoas: '0',
            total_pessoas_filiais: '0',
            myChartArrecadacoes: {},
            myChartFormasPag: {},
            myChartPessoas: {},
            myChartPessoasFiliais: {},
        },
        methods: {
            zerarValores() {
                this.pedido_total_periodo = '0,00';
                this.pedido_qtd_periodo = '0';
                this.pedido_total_periodo_mes = '0,00';
                this.pedido_qtd_periodo_mes = '0';
                this.total_arrecadacao = '0,00';
                this.total_fp = '0,00';
                this.total_pessoas = '0';
                this.total_pessoas_filiais = '0';
            },
            
            setDataIncDays(dias) {                
                var hoje = new Date();
                var primeiroDia = new Date();
                primeiroDia.setDate(primeiroDia.getDate() - dias);

                $("#data_inicial").val(this.dateToStr(primeiroDia));                
                $("#data_final").val(this.dateToStr(hoje));                
            },
            
            dumpArrecadacao() {
                let data_inicial = $("#data_inicial").val() || '';
                let data_final   = $("#data_final").val() || '';
                let filial_id    = $("#filial_id").val() || 0;
                  
                $.get('<?= base_url('Relatorios/porArrecadacao') ?>', {
                    data_inicial: data_inicial,
                    data_final: data_final,
                    filial_id: filial_id,
                })
                 .done(data => {                      
                    this.loading = false;
                    let obj = JSON.parse(data);                    
                    if (obj) {
                        if (obj.status) {                            
                            this.renderChartArrecadacao(obj.data.tipos, obj.data.totais);
                            this.total_arrecadacao = obj.data.total_geral;
                        } else {
                            //alert(obj.msg);
                            this.renderChartArrecadacao([], []);
                        }
                    } else {
                        alert('Impossível retornar dados. Tente novamente.');
                    }                    
                 })
                 .fail(error => {
                   this.loading = false;  
                   alert('Erro: '+JSON.stringify(error));
                 })
                 .always(function() {
                    this.loading = false;
                    //alert( "finished" );
                 });
            },
            
            renderChartArrecadacao(tipos, totais) {
                let cores = [];                
                tipos.forEach(m => {
                    let cor = this.getCorPadrao(); //this.gerarcor();
                    while (cores.includes(cor)) {
                        cor = this.getCorPadrao();
                    }
                    cores.push(cor);
                });
                
                if ('destroy' in this.myChartArrecadacoes) {                    
                    this.myChartArrecadacoes.destroy();
                }
                
                var ctx = document.getElementById('myChartArrecadacoes').getContext('2d');
                
                this.myChartArrecadacoes = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: tipos,
                        datasets: [{
                            label: 'ARRECADAÇÕES',
                            data: totais,
                            backgroundColor: cores,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: true,
                            position: 'right',
                            align: 'start'
                        }
                    }
                });
            },
                    
            dumpFormasPag() {
                let data_inicial = $("#data_inicial").val() || '';
                let data_final   = $("#data_final").val() || '';
                let filial_id    = $("#filial_id").val() || 0;
                  
                $.get('<?= base_url('Relatorios/porFormasPagamento') ?>', {
                    data_inicial: data_inicial,
                    data_final: data_final,
                    filial_id: filial_id,
                })
                 .done(data => {                       
                    this.loading = false;
                    let obj = JSON.parse(data);                    
                    if (obj) {
                        if (obj.status) {                            
                            this.renderChartFormasPag(obj.data.tipos, obj.data.totais);
                            this.total_fp = obj.data.total;
                        } else {
                            //alert(obj.msg);
                            this.renderChartFormasPag([], []);
                        }
                    } else {
                        alert('Impossível retornar dados. Tente novamente.');
                    }                    
                 })
                 .fail(error => {
                   this.loading = false;  
                   alert('Erro: '+JSON.stringify(error));
                 })
                 .always(function() {
                    this.loading = false;
                    //alert( "finished" );
                 });
            },
                    
            renderChartFormasPag(tipos, totais) {
                let cores = [];
                tipos.forEach(m => {
                    let cor = this.getCorPadrao(); //this.gerarcor();
                    while (cores.includes(cor)) {
                        cor = this.getCorPadrao();
                    }
                    cores.push(cor);
                });
                
                if ('destroy' in this.myChartFormasPag) {                    
                    this.myChartFormasPag.destroy();
                }
                
                var ctx = document.getElementById('myChartFormasPag').getContext('2d');
                
                this.myChartFormasPag = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: tipos,
                        datasets: [{
                            label: 'FORMAS DE PAGAMENTO',
                            data: totais,
                            backgroundColor: cores,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: true,
                            position: 'right',
                            align: 'start'
                        }                                              
                    }
                });
            },
                    
            /// PESSOAS 
            dumpPessoas() {
                let data_inicial = $("#data_inicial").val() || '';
                let data_final   = $("#data_final").val() || '';
                let filial_id    = $("#filial_id").val() || 0;
                  
                $.get('<?= base_url('Relatorios/pessoas') ?>', {
                    data_inicial: data_inicial,
                    data_final: data_final,
                    filial_id: filial_id,
                })
                 .done(data => {                      
                    this.loading = false;
                    let obj = JSON.parse(data);                    
                    if (obj) {
                        if (obj.status) {                            
                            this.renderChartPessoas(obj.data.tipos, obj.data.totais);
                            this.total_pessoas = obj.data.total_geral;
                        } else {
                            //alert(obj.msg);
                            this.renderChartPessoas([], []);
                        }
                    } else {
                        alert('Impossível retornar dados. Tente novamente.');
                    }                    
                 })
                 .fail(error => {
                   this.loading = false;  
                   alert('Erro: '+JSON.stringify(error));
                 })
                 .always(function() {
                    this.loading = false;
                    //alert( "finished" );
                 });
            },
            
            renderChartPessoas(tipos, totais) {
                let cores = [];
                tipos.forEach(m => {
                    let cor = this.getCorPadrao(); //this.gerarcor();
                    while (cores.includes(cor)) {
                        cor = this.getCorPadrao();
                    }
                    cores.push(cor);
                });
                
                if ('destroy' in this.myChartPessoas) {                    
                    this.myChartPessoas.destroy();
                }
                
                var ctx = document.getElementById('myChartPessoas').getContext('2d');
                
                this.myChartPessoas = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: tipos,
                        datasets: [{
                            label: 'PESSOAS',
                            data: totais,
                            backgroundColor: cores,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: true,
                            position: 'right',
                            align: 'start'
                        }
                    }
                });
            },    
            /// FIM PESSOAS
    
            /// PESSOAS POR FILIAIS
            dumpPessoasFiliais() {
                let data_inicial = $("#data_inicial").val() || '';
                let data_final   = $("#data_final").val() || '';
                let filial_id    = $("#filial_id").val() || 0;
                  
                $.get('<?= base_url('Relatorios/pessoasPorFiliais') ?>', {
                    data_inicial: data_inicial,
                    data_final: data_final,
                    filial_id: filial_id,
                })
                 .done(data => {                      
                    this.loading = false;
                    let obj = JSON.parse(data);                    
                    if (obj) {
                        if (obj.status) {                            
                            this.renderChartPessoasFiliais(obj.data.tipos, obj.data.totais);
                            this.total_pessoas_filiais = obj.data.total_geral;
                        } else {
                            //alert(obj.msg);
                            this.renderChartPessoasFiliais([], []);
                        }
                    } else {
                        alert('Impossível retornar dados. Tente novamente.');
                    }                    
                 })
                 .fail(error => {
                   this.loading = false;  
                   alert('Erro: '+JSON.stringify(error));
                 })
                 .always(function() {
                    this.loading = false;
                    //alert( "finished" );
                 });
            },
            
            renderChartPessoasFiliais(tipos, totais) {
                let cores = [];
                tipos.forEach(m => {
                    let cor = this.getCorPadrao(); //this.gerarcor();
                    while (cores.includes(cor)) {
                        cor = this.getCorPadrao();
                    }
                    cores.push(cor);
                });
                
                if ('destroy' in this.myChartPessoasFiliais) {                    
                    this.myChartPessoasFiliais.destroy();
                }
                
                var ctx = document.getElementById('myChartPessoasFiliais').getContext('2d');
                
                this.myChartPessoasFiliais = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: tipos,
                        datasets: [{
                            label: 'PESSOAS POR FILIAIS',
                            data: totais,
                            backgroundColor: cores,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: true,
                            position: 'right',
                            align: 'start'
                        }
                    }
                });
            },            
            /// FIM PESSOAS POR FILIAIS
             
            filtrar() {
                this.zerarValores();
                this.dumpArrecadacao();
                this.dumpFormasPag();
                this.dumpPessoas();
                this.dumpPessoasFiliais();
            },
            
        },
        mounted() {
            //this.setDataIncDays(30);
            $("#data_inicial").val(this.getPrimeiroDiaMes());
            $("#data_final").val(this.getDataAtual());
            this.filtrar();
        }
    });
</script>


