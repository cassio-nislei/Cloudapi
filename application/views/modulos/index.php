<style>
    .dataTables_filter, .dataTables_info { display: none; }
    .paginate_button { font-size: 10px; }
</style>
<?php 
//Ocultar input de pesquisa:
//https://stackoverflow.com/questions/1920600/how-can-i-remove-the-search-bar-and-footer-added-by-the-jquery-datatables-plugin
?>

<h3><span class="fa fa-users"></span> MÓDULOS</h3>
<div id="appModelo">
    <?php 
        include_once APPPATH . 'views/modulos/regions/modulo_modal.php';
    ?>
    
    <!-- NOVO -->    
    <button class="btn btn-hti" style="margin-top: 20px; width: 150px;"
            v-on:click="novo()"
            >
        <span class="fa fa-plus"></span>
        Novo módulo</button>
    <!-- End NOVO -->
    
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
                            <input id="datatableSearch" type="search" class="form-control" placeholder="Pesquisar grupos" aria-label="Pesquisar grupos">
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
                                        <th>Controle</th>
                                        <th>Ação</th>                                           
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
            eventos: [],
        },
        methods: {
            novo() {                
                this.registro = {};                
                $("#moduloModal").modal('show');                
            },
                    
            editar(id) {   
                $.get(this.base_url+'Modulos/get/'+id)
                .done(data => {                    
                    let obj = JSON.parse(data);
                    if (obj) {
                        if (obj.status) {
                            this.registro = obj.data;                              
                            $("#moduloModal").modal('show'); 
                        } else {
                            alert(obj.msg);
                        }
                    } else {
                        alert(data);
                    }
                })
                .fail(e => {
                    alert(JSON.stringify(e));
                });
            },
            
            salvar() {
                $.post(this.base_url+'Modulos/salvar', { registro: this.registro })
                .done(result => {                                                  
                    let obj = JSON.parse(result);
                    if (obj) {
                        if (obj.status) {                            
                            $("#moduloModal").modal('hide');                            
                            this.myTable.ajax.reload();
                        } else {
                            alert(obj.msg);
                        }
                    } else {
                        alert(result);
                    }
                })
                .fail(data => {
                    alert(JSON.stringify(data));
                });    
            },
                    
            excluir(id) {
                if (id === '' || id === undefined || id === 0) {
                    alert('O registro não foi salvo.');
                    return false;
                }
                if (!confirm('Deseja realmente excluir este registro?')) {
                    return false;
                }
                
                $.post(this.base_url+'Modulos/excluir/'+id)
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
            this.myTable = $("#tbRegistros").DataTable({     
                searching: true,
                paging: true,
                info: true, 
                pageLength: 10,
                processing: true,
                ajax: {
                    url: this.base_url+'Modulos/getAll',
                    dataSrc: 'data',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                },
                columns: [
                    {   data: 'controle' },
                    {   data: 'acao' },
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
                            return '<i class="fa fa-edit" style="font-size: 20px; cursor: pointer; color: green;" onclick="appModelo.editar('+data+')" />';
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<i class="fa fa-trash" style="font-size: 20px; cursor: pointer; color: red;" onclick="appModelo.excluir('+data+')" />';
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
            
        }
    });
</script>

