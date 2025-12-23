<?php 
    if (!pode_ler('relatorios', FALSE)) {
        echo '<br><center>Sem permissão para acessar este recurso!</center>';
        return false;
    }
?>
<style>
    .dataTables_filter, .dataTables_info { display: none; }
    .paginate_button { font-size: 10px; }
</style>
<?php 
//Ocultar input de pesquisa:
//https://stackoverflow.com/questions/1920600/how-can-i-remove-the-search-bar-and-footer-added-by-the-jquery-datatables-plugin
?>
<br>
<h3><i class="bi bi-file-earmark-text"></i> Relatórios</h3>
<div id="appModelo">
    <?php 
        //include_once APPPATH . 'views/modulos/regions/modulo_modal.php';
    ?>
    
    <div class="row">
        <div class="col-md-2">
            <label for="data_inicial" class="caption">Data Inicial</label>
            <input type="text" id="data_inicial" class="form-control datepicker"
                    <?php 
                        if (isTesoureiro()) {
                            ?>
                            value="<?= getDateCurrentBr() ?>" 
                            disabled="disabled"
                            <?php
                        }
                   ?>
                   >
        </div>
        
        <div class="col-md-2">
            <label for="data_final" class="caption">Data Final</label>
            <input type="text" id="data_final" class="form-control datepicker"
                   <?php 
                        if (isTesoureiro()) {
                            ?>
                            value="<?= getDateCurrentBr() ?>" 
                            disabled="disabled"
                            <?php
                        }
                   ?>                   
                   >
        </div>
        
        <div class="col-md-2">
            <button class="btn btn-hti" style="width: 100%; margin-top: 40px;"
                    v-on:click="getAll()"
                    >
                <span class="fa fa-search"></span>
                Pesquisar
            </button>
        </div>
        
        <div class="col-md-3">
            <button class="btn btn-hti-out" style="width: 160px; margin-top: 40px;"
                    v-on:click="novo()"
                    >
                <span class="fa fa-plus"></span>
                Novo Relatório
            </button>
        </div>
        
        
    </div>
        
    <div style="width: 100%; text-align: left; font-size: 14px; margin-top: 20px;">  
        <div class="card">
            <div class="card-body" style="padding: 10px;">
                <table style="width: 100%;">
                    <tr>                        
                        <td>Dízimos</td>
                        <td>Ofertas</td>
                        <td>Ofertas Esp.</td>
                        <td>Ofertas Miss.</td>
                        <td style="text-align: right;">Total</td>
                    <tr>
                    <tr>
                        <td style="font-weight: bold; width: 20%;">{{total_dizimos}}</td>
                        <td style="font-weight: bold; width: 20%;">{{total_ofertas}}</td>
                        <td style="font-weight: bold; width: 20%;">{{total_especiais}}</td>
                        <td style="font-weight: bold; width: 20%;">{{total_missoes}}</td>
                        <td style="font-weight: bold; width: 20%; text-align: right;">{{total}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row" style="margin-top: 25px;">        
        <div class="col-md-12">            
            <!-- Card -->            
            <div class="card" style="padding-bottom: 10px;">
                <div class="card-header">
                    <div class="col-sm-6 col-md-4 mb-3 mb-sm-0">
                        <!-- Search -->
                        <div class="input-group input-group-merge input-group-flush">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="tio-search"></i>
                                </div>
                            </div>
                            <input id="datatableSearch" type="search" class="form-control" placeholder="Pesquisar" aria-label="Pesquisar">
                        </div>
                        <!-- End Search -->
                    </div>
                    
                    <!-- Exportacao -->
                    <div class="hs-unfold mr-2">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle" href="javascript:;"
                           data-hs-unfold-options='{
                              "target": "#usersExportDropdown",
                              "type": "css-animation"
                            }'>
                            <i class="tio-download-to mr-1"></i> Exportar
                        </a>

                        <div id="usersExportDropdown" 
                             class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                            <span class="dropdown-header">Opções</span>
                            <a id="export-copy" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2" src="<?= base_url('assets/dashboard/svg/illustrations/copy.svg') ?>" alt="Image Description">
                                Copiar Texto
                            </a>
                            <a id="export-print" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2" src="<?= base_url('assets/dashboard/svg/illustrations/print.svg') ?>" alt="Image Description">
                                Imprimir
                            </a>
                            <div class="dropdown-divider"></div>
                            <span class="dropdown-header">Download</span>
                            <a id="export-excel" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2" src="<?= base_url('assets/dashboard/svg/brands/excel.svg') ?>" alt="Image Description">
                                Excel
                            </a>
                            <a id="export-csv" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2" src="<?= base_url('assets/dashboard/svg/components/placeholder-csv-format.svg') ?>" alt="Image Description">
                                .CSV
                            </a>
                            <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2" src="<?= base_url('assets/dashboard/svg/brands/pdf.svg') ?>" alt="Image Description">
                                PDF
                            </a>
                        </div>
                    </div>
                    <!-- End Exportacao -->  
                    
                </div>                                
                <!-- Tab Content -->
                <div class="tab-content"> 
                    <div class="tab-pane fade show active" id="all-time" role="tabpanel" aria-labelledby="all-time-tab">                        
                        <div class="table-responsive">                            
                            <table id="tbRegistros" style="width: 100%; font-size: 14px;" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                   data-hs-datatables-options='{
                                        "order": [],                                        
                                      }'
                                   >
                                <thead class="thead-light">
                                    <tr>
                                        <th>Cód.</th>  
                                        <th>Data</th>  
                                        <th>Hora</th>
                                        <th>Pregador</th> 
                                        <th>Dízimos</th>
                                        <th>Ofertas</th>
                                        <th>Of. Esp.</th>
                                        <th>Of. Missões</th>
                                        <th>Total</th>
                                        <th style="width: 100px;">Status</th>
                                        <th style="width: 10px;"></th>
                                        <th style="width: 10px;"></th>
                                        <th style="width: 10px;"></th>
                                    </tr>
                                </thead>  
                                <tbody>                                    
                                </tbody>                                                                
                            </table>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div> 
    
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>

<!-- btn de exportacao -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.1.1/css/dataTables.dateTime.min.css">
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

<script src="https://cdn.datatables.net/plug-ins/1.13.4/api/sum().js"></script>  

<script>
    $(document).on('ready', function () {
        $.noConflict();        
    });
    
    var appModelo = new Vue({
        el:"#appModelo",
        mixins: [mxFunctions],
        data: {
            registros: [],
            registro: {},
            grupos: [],
            filtro: '', 
            myTable: null,            
            total: 0.00,
            total_dizimos: 0.00,
            total_ofertas: 0.00,
            total_especiais: 0.00,
            total_missoes: 0.00,
        },
        methods: {
            calcTotal() {
                let dizimos = 0.00;
                let ofertas = 0.00;
                let especiais = 0.00;
                let missoes = 0.00;
                let total = 0.00;
                
                let registros = this.myTable.rows({search:'applied'}).data().toArray();  
                registros.forEach(e => {
                    dizimos   += parseFloat(e.total_dizimos || 0);
                    ofertas   += parseFloat(e.total_gerais || 0);
                    especiais += parseFloat(e.total_especiais || 0);
                    missoes   += parseFloat(e.total_missoes || 0);
                    total     += parseFloat(e.total_final || 0);
                });     
                
                this.total_dizimos = this.toCurrency(dizimos);
                this.total_ofertas = this.toCurrency(ofertas);
                this.total_especiais = this.toCurrency(especiais);
                this.total_missoes = this.toCurrency(missoes);
                this.total = this.toCurrency(total);
            },
            
            getAll() {
                let inicio_mes = $("#data_inicial").val();
                let hoje = $("#data_final").val(); 

                let url = 'http://localhost/fgportal/Diarios/getAll'
                        + '?data_inicial='+inicio_mes
                        + '&data_final='+hoje;
                
                this.myTable.ajax.url(url).load();
            },
            
            novo() {                
                window.location.href = 'http://localhost/fgportal/diarios/novo'                
            },
                    
            editar(id) {   
                window.location.href = 'http://localhost/fgportal/diarios/'+id; 
            },
            
            imprimir(id) {   
                let url = 'http://localhost/fgportal/diarios/pdf/'+id; 
                window.open(url, 'blank_');

            },
                    
            excluir(id) {
                if (id === '' || id === undefined || id === 0) {
                    alert('O registro não foi salvo.');
                    return false;
                }
                if (!confirm('Deseja realmente excluir este registro?')) {
                    return false;
                }
                
                $.post('http://localhost/fgportal/diarios/excluir/'+id)
                .done(result => { 
                    let obj = JSON.parse(result);
                    if (obj) {
                        if (obj.status) {
                            this.myTable.ajax.reload();
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
            
        },    
        watch: {
            filtro() {                
                this.registros.forEach(c => {                                           
                    if (this.filtro === '') {
                       c.visivel = 'S';                           
                    } else {
                        let a = c.nome.toString().toLowerCase();
                        let b = this.filtro.toLowerCase();
                        c.visivel = (a.indexOf(b) > -1) ? 'S' : 'N';
                    }
                });
            }
        },
        mounted() {
            //retorna data atual
            <?php if (!isTesoureiro()): ?>
            var today = new Date();
            var dd    = String(today.getDate()).padStart(2, '0');
            var mm    = String(today.getMonth() + 1).padStart(2, '0'); 
            var yyyy  = today.getFullYear();

            var inicio_mes = '01/' + mm + '/' + yyyy;
            var hoje = dd + '/' + mm + '/' + yyyy;
            
            $("#data_inicial").val(inicio_mes);
            $("#data_final").val(hoje);
            <?php endif; ?>
            
            let url = 'http://localhost/fgportal/Diarios/getAll'
                    + '?data_inicial='+$("#data_inicial").val()
                    + '&data_final='+$("#data_final").val();
            
            this.myTable = $("#tbRegistros").DataTable({  
                drawCallback: function() {
                    var api = this.api();      
                    //alert( api.table().column(5).data().sum() );
                    $( api.table().footer() ).html(
                      api.column(5, { page:'current' }).data().sum()
                    );                    
                }, 
                footerCallback: function ( row, data, start, end, display ) {            
                    var api = this.api(), data;

                    // Total over this page
                    var pageTotal1 = api
                        .column( 5, { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                            //alert(a+' + '+b);
                            return a + b;
                        }, 0 );   
                    $(api.column(5).footer()).html(pageTotal1);      
                    
                },
                searching: true,
                paging: true,
                info: true, 
                pageLength: 10,
                processing: true,                
                ajax: {
                    url: url,
                    dataSrc: 'data',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                },
                columns: [
                    {   data: 'id',
                        render: function(data, type, row) {                            
                            return ('00000' + data).slice(-5);
                        }   
                    },
                    {   data: 'data' },
                    {   data: 'hora',
                        render: function(data, type, row) {                            
                            return data+':00';
                        }   
                    },
                    {   data: 'pregador' },
                    {   
                        data: 'total_dizimos',
                        class: 'text-right',
                        render: function(data, type, row) {                            
                            return appModelo.toCurrency(data);
                        }   
                    },
                    {   
                        data: 'total_gerais',
                        class: 'text-right',
                        render: function(data, type, row) {                            
                            return appModelo.toCurrency(data);
                        }   
                    },
                    {   
                        data: 'total_especiais',
                        class: 'text-right',
                        render: function(data, type, row) {                            
                            return appModelo.toCurrency(data);
                        }   
                    },
                    {   
                        data: 'total_missoes',
                        class: 'text-right',
                        render: function(data, type, row) {                            
                            return appModelo.toCurrency(data);
                        }   
                    },
                    {   
                        data: 'total_final',
                        class: 'text-right',
                        render: function(data, type, row) {                            
                            return appModelo.toCurrency(data);
                        }   
                    },
                    {   data: 'status',
                        class: 'text-center',
                        render: function(data, type, row) {                            
                            if (data === 'C') {
                                return '<span class="badge badge-success" style="width: 80px">Confirmado</span>';
                            } else {
                                return '<span class="badge badge-danger" style="width: 80px">Rascunho</span>';
                            }
                        }
                    
                    },
                    /*{
                        data: null,
                        className: "dt-center editor-edit",
                        defaultContent: '<i class="fa fa-edit" style="font-size: 20px; cursor: pointer; color: green;" />',
                        orderable: false
                    },
                    {
                        data: null,
                        className: "dt-center editor-edit",
                        defaultContent: '<i class="fa fa-trash" style="font-size: 20px; cursor: pointer; color: red;" />',
                        orderable: false
                    },*/
                    {
                        data: 'id',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<i class="fa fa-edit" title="Editar" style="font-size: 20px; cursor: pointer; color: green;" onclick="appModelo.editar('+data+')" />';
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        render: function(data, type, row) {
                            if (row.status === 'C') {
                                return '<i class="fa fa-print" title="Imprimir" style="font-size: 20px; cursor: pointer; color: black;" onclick="appModelo.imprimir('+data+')" />';
                            } else {
                                return '<i class="fa fa-print" style="font-size: 20px; color: gray;" />';
                            }    
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        render: function(data, type, row) {
                            <?php 
                                if (!isAdmin()) {
                                    //se nao for Admin, nao pode excluir apos confirmado
                                    ?>
                                    if (row.status !== 'C') {                                        
                                        return '<i class="fa fa-trash" title="Excluir" style="font-size: 20px; cursor: pointer; color: red;" onclick="appModelo.excluir('+data+')" />';
                                    } else {
                                        return '<i class="fa fa-trash" style="font-size: 20px; color: gray;" />';
                                    }     
                                    <?php
                                } else {
                                    ?>
                                    return '<i class="fa fa-trash" title="Excluir" style="font-size: 20px; cursor: pointer; color: red;" onclick="appModelo.excluir('+data+')" />';                                    
                                    <?php
                                }
                            ?>                            
                        }
                    },
                ],                
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.4/i18n/pt_br.json',                    
                },
                dom: 'Bfrtip',
                //dom: 'lfrtip',
                //buttons: [                    
                //    'copy', 'print', 'pdf', 'excel', 'csv'
                //],
                buttons: [
                    { extend: 'copy',  className: 'd-none' },
                    { extend: 'excel', className: 'd-none' },
                    { extend: 'csv',   className: 'd-none' },
                    { extend: 'pdf',   className: 'd-none' },
                    { extend: 'print', className: 'd-none' },
                ],                
            });
              
            _this = this;
            
            $('#export-copy').click(function() {
                _this.myTable.button('.buttons-copy').trigger();
            });

            $('#export-excel').click(function() {
                _this.myTable.button('.buttons-excel').trigger();
            });

            $('#export-csv').click(function() {
                _this.myTable.button('.buttons-csv').trigger();
            });

            $('#export-pdf').click(function() {
                _this.myTable.button('.buttons-pdf').trigger();
            });

            $('#export-print').click(function() {
                _this.myTable.button('.buttons-print').trigger();
            });
            
            $('#datatableSearch').on('keyup', function () {                 
                _this.myTable.search($('#datatableSearch').val()).draw();
            });
            
            $('#datatableSearch').on('search', function () {                 
                _this.myTable.search('').draw();
            });
            
            this.myTable.on('search.dt', function () {
                _this.calcTotal();
            });
            
        }
    });
</script>



