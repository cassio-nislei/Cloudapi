<style>
    .dataTables_filter { display: none; }
    .paginate_button { font-size: 10px; }
    
    .dropdown-menu-menu {
        border-radius: 0px;
        color: #000;
        background-color: #FFF;
    }
    
    .dropdown-item-menu {
        color: #000;
    }
    
    .dropdown-header {
        display: block;
        padding: 1rem 1.5rem;
        margin-bottom: 0;
        font-size: .8125rem;
        color: #000;
        white-space: nowrap;
    }
</style>
<?php 
//Ocultar input de pesquisa:
//https://stackoverflow.com/questions/1920600/how-can-i-remove-the-search-bar-and-footer-added-by-the-jquery-datatables-plugin
?>

<h3><span class="fa fa-users"></span> PESSOAS</h3>
<div id="appModelo">
    <?php 
        include_once APPPATH . 'views/pessoas/regions/pessoa_modal.php';        
        include_once APPPATH . 'views/pessoas/regions/atendimento_modal.php';    
    ?>
    
    <!-- NOVO -->    
    <button class="btn btn-hti" style="margin-top: 20px; width: 150px;"
            v-on:click="novo()"
            >
        <span class="fa fa-plus"></span>
        Novo</button>
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
                             class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right"
                             style="background-color: #FFF;"
                             >
                            <span class="dropdown-header">Opções</span>
                            <a id="export-copy" class="dropdown-item" href="javascript:;" style="color: #000;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2" src="<?= base_url('assets/dashboard/svg/illustrations/copy.svg') ?>" alt="Image Description">
                                Copiar Texto
                            </a>
                            <a id="export-print" class="dropdown-item" href="javascript:;" style="color: #000;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2" src="<?= base_url('assets/dashboard/svg/illustrations/print.svg') ?>" alt="Image Description">
                                Imprimir
                            </a>
                            <div class="dropdown-divider"></div>
                            <span class="dropdown-header">Download</span>
                            <a id="export-excel" class="dropdown-item" href="javascript:;" style="color: #000;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2" src="<?= base_url('assets/dashboard/svg/brands/excel.svg') ?>" alt="Image Description">
                                Excel
                            </a>
                            <a id="export-csv" class="dropdown-item" href="javascript:;" style="color: #000;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2" src="<?= base_url('assets/dashboard/svg/components/placeholder-csv-format.svg') ?>" alt="Image Description">
                                .CSV
                            </a>
                            <a id="export-pdf" class="dropdown-item" href="javascript:;" style="color: #000;">
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
                                        <th>Nome</th>
                                        <th>CNPJ/CPF</th> 
                                        <th>Telefone</th> 
                                        <th>Celular</th> 
                                        <th>Cidade</th> 
                                        <th>Status</th> 
                                        
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
            registro: {},   
            atendimento: {},
            atendimentos: [],
            myTable: null,                                   
        },
        methods: {
            formataRegistro() {
                this.registro.TELEFONE = $("#telefone").val() || '';
                this.registro.CELULAR  = $("#celular").val() || '';
                this.registro.FAX      = $("#fax").val() || '';
                this.registro.CEP      = $("#cep").val() || '';
                
                this.registro.EXPIRA_EM = $("#expira_em").val() || '';
                this.registro.MENSALIDADE = $("#mensalidade").val() || '';
                this.registro.DATA_CADASTRO = $("#data_cadastro").val() || '';
                this.registro.ULTIMA_ATUALIZACAO = $("#ultima_atualizacao").val() || '';
                
                this.registro.DATA_INSTALL = $("#data_install").val() || '';
            },
            
            configuraModalPessoa() {                
                $("#emit-basico-tab").removeClass('active');
                $("#emit-financeiro-tab").removeClass('active');
                $("#emit-atualizacoes-tab").removeClass('active');
                $("#emit-outros-tab").removeClass('active');
                $("#emit-licencas-tab").removeClass('active');
                $("#emit-atendimentos-tab").removeClass('active');

                $("#emit-basico").removeClass('active');
                $("#emit-financeiro").removeClass('active');
                $("#emit-atualizacoes").removeClass('active');
                $("#emit-outros").removeClass('active');   
                $("#emit-licencas").removeClass('active');  
                $("#emit-atendimentos").removeClass('active');

                $("#emit-basico-tab").addClass('active');
                $("#emit-basico").addClass('active');
                $("#emit-basico").addClass('show');
            },
            
            novo() {                
                this.registro = {};   
                this.atendimentos = [];
                this.configuraModalPessoa();
                $("#pessoaModal").modal('show');                
            },
            
            novoAtendimento() {
                this.atendimento = {};
                $("#atendimentoModal").modal('show');
            },
            
            editarAtendimento(a) {                
                this.atendimento = JSON.parse(JSON.stringify(a));
                $("#atendimentoModal").modal('show');
            },
                    
            editar(id) { 
                this.atendimentos = [];
                $.get(this.base_url+'Pessoas/get/'+id)
                .done(data => {                    
                    let obj = JSON.parse(data);
                    if (obj) {
                        if (obj.status) {
                            this.registro = obj.data; 
                            
                            console.log(this.registro);
                            
                            $("#telefone").val(this.registro.TELEFONE || '');
                            $("#celular").val(this.registro.CELULAR || '');
                            $("#fax").val(this.registro.FAX || '');
                            $("#cep").val(this.registro.CEP || '');
                            $("#expira_em").val(this.registro.EXPIRA_EM || '');
                            $("#mensalidade").val(this.registro.MENSALIDADE || '');
                            $("#data_cadastro").val(this.registro.DATA_CADASTRO || '');
                            $("#ultima_atualizacao").val(this.registro.ULTIMA_ATUALIZACAO || '');
                            $("#data_install").val(this.registro.DATA_INSTALL || '');
                            
                            this.configuraModalPessoa();
                            
                            this.getAtendimentos();
                            
                            $("#pessoaModal").modal('show'); 
                        } else {
                            alertError(obj.msg);
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
                this.formataRegistro();
                
                $.post(this.base_url+'Pessoas/salvar', { registro: this.registro })
                .done(result => {                                                  
                    let obj = JSON.parse(result);
                    if (obj) {
                        if (obj.status) {       
                            alertSuccess(obj.msg);
                            //$("#pessoaModal").modal('hide');                            
                            this.myTable.ajax.reload();
                        } else {
                            alertError(obj.msg);
                        }
                    } else {
                        alert(result);
                    }
                })
                .fail(data => {
                    alert(JSON.stringify(data));
                });    
            },
            
            salvarAtendimento() {                
                if ((this.atendimento.TEXTO ?? '') === '') {
                    alertError('Digite alguma informação do atendimento.');
                    return false;
                }
                
                $.post(this.base_url+'Atendimentos/salvar', {
                    id: this.atendimento.ID,
                    texto: this.atendimento.TEXTO,
                    pessoa_id: this.registro.ID_PESSOA
                })
                .done(result => {                                                  
                    let obj = JSON.parse(result);
                    if (obj) {
                        if (obj.status) {       
                            //alertSuccess(obj.msg);
                            this.getAtendimentos();
                            $("#atendimentoModal").modal('hide');                            
                        } else {
                            alertError(obj.msg);
                        }
                    } else {
                        alert(result);
                    }
                })
                .fail(data => {
                    alert(JSON.stringify(data));
                }); 
            },
            
            getAtendimentos() {                
                this.atendimentos = [];
                $.get(this.base_url+'Atendimentos/getAll?id_pessoa='+this.registro.ID_PESSOA)
                .done(data => {                    
                    let obj = JSON.parse(data);
                    if (obj) {
                        if (obj.status) {
                            this.atendimentos = obj.data;                             
                        }                         
                    } else {
                        alert(data);
                    }
                })
                .fail(e => {
                    alert(JSON.stringify(e));
                });
            },
            
            excluirAtendimento(id) {                
                if (id === '' || id === undefined || id === 0) {
                    alertError('Registro não especificado.');
                    return false;
                }
                                
                alertQuestion('Deseja realmente excluir o registro agora?', () => {                    
                    $.post(this.base_url+'Atendimentos/excluir/'+id)
                    .done(result => {                         
                        let obj = JSON.parse(result);
                        if (obj) {
                            if (!obj.status) {
                                alertError(obj.msg);
                            } 
                            this.getAtendimentos();
                        } else {
                            alert(result);
                        }                         
                    })
                    .fail(err => {
                        alert(JSON.stringify(err));
                    });    
                });
            },
                    
            excluir(id) {
                if (id === '' || id === undefined || id === 0) {
                    alertError('O registro não foi salvo.');
                    return false;
                }
                                
                alertQuestion('Deseja realmente excluir o registro agora?', () => {                    
                    $.post(this.base_url+'Pessoas/excluir/'+id)
                    .done(result => { 
                        let obj = JSON.parse(result);
                        if (obj) {
                            if (obj.status) {
                                this.myTable.ajax.reload();
                            } else {
                                alertError(obj.msg);
                            } 
                        } else {
                            alert(result);
                        }                         
                    })
                    .fail(err => {
                        alert(JSON.stringify(err));
                    });    
                });
            },
            
            getLicencas() {
                //this.registro.LICENCAS = [];
                
                $.get(this.base_url+'Pessoas/getLicencas?id_pessoa='+this.registro.ID_PESSOA)
                .done(data => {                    
                    let obj = JSON.parse(data);
                    if (obj) {
                        if (obj.status) {
                            this.registro.LISTA_LICENCAS = obj.data;                             
                        } else {
                            if (obj.msg === 'Nenhum registro encontrado.') {
                                this.registro.LISTA_LICENCAS = [];
                            } else {
                                alert(obj.msg);
                            }
                        } 
                    } else {
                        alert(data);
                    }
                })
                .fail(e => {
                    alert(JSON.stringify(e));
                });
            },
            
            excluirLicenca(id) {
                alertQuestion('Deseja realmente excluir esta Licença agora?', () => {                    
                    $.post(this.base_url+'Pessoas/excluirLicenca?', { id: id })
                    .done(result => { 
                        let obj = JSON.parse(result);
                        if (obj) {                        
                            if (obj.status) {
                                this.getLicencas();
                            } 
                        }                        
                    })
                    .fail(err => {
                        alert(JSON.stringify(err));
                    }); 
                });                
                   
            },
            
            changeStatus(id_licenca) {
                //if (!confirm('Deseja realmente alterar este status agora?')) {
                //    return false;
                //}
                
                $.post(this.base_url+'Pessoas/changeStatus', { id: id_licenca })
                .done(result => {                                                  
                    let obj = JSON.parse(result);
                    if (obj) {
                        if (obj.status) {                            
                            this.getLicencas();
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
            
        },    
        watch: {
            //
        },
        mounted() {  
            this.myTable = $("#tbRegistros").DataTable({     
                searching: true,
                paging: true,
                info: true, 
                pageLength: 100,
                processing: true,
                ajax: {
                    url: this.base_url+'Pessoas/getAll',
                    dataSrc: 'data',
                },
                columns: [
                    {   
                        data: 'NOME',
                        render: function(data, type, row) {
                            return '<div class="btn btn-link" style="font-weight: normal;" onclick="appModelo.editar('+row.ID_PESSOA+')">'+data+'</div>';
                        }
                        
                    },
                    {   
                        data: 'CGC',
                        className: 'text-right',
                        render: function(data, type, row) {
                            if (data) {
                                return appModelo.formataCgc(data);
                            } 
                            return data;
                        }
                    },
                    {   
                        data: 'TELEFONE',                        
                        render: function(data, type, row) {
                            if (data) {
                                return appModelo.formataFone(data);
                            } 
                            return data;
                        }
                    },
                    {   
                        data: 'CELULAR',                        
                        render: function(data, type, row) {
                            if (data) {
                                return appModelo.formataFone(data);
                            } 
                            return data;
                        }
                    },
                    {   
                        data: 'CIDADE',
                        render: function(data, type, row) {
                            if (data && row.ESTADO) {
                                return data +'/'+ row.ESTADO;
                            } 
                            return data;
                        }
                    },
                    {
                        data: 'status',
                        render: function(data, type, row) {
                            if (data === 'Ativo') {
                                return '<span class="badge badge-success" style="width: 100px;">Ativo</span>';
                            } 
                            return '<span class="badge badge-danger" style="width: 100px;">'+data+'</span>';
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
                        data: 'ID_PESSOA',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<i class="fa fa-edit" title="Editar" style="font-size: 20px; cursor: pointer; color: green;" onclick="appModelo.editar('+data+')" />';
                        }
                    },                   
                    {
                        data: 'ID_PESSOA',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<i class="fa fa-trash" title="Excluir" style="font-size: 20px; cursor: pointer; color: red;" onclick="appModelo.excluir('+data+')" />';
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

