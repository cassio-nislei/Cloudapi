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

<h3><span class="bi bi-box"></span> PRODUTOS</h3>
<div id="appModelo">
    <?php 
        include_once APPPATH . 'views/produtos/regions/produto_modal.php';  
        include_once APPPATH . 'views/produtos/regions/impostos_modal.php'; 
    ?>
    
    <div class="row">
        <div class="col-md-2">
            <label class="caption">NCM</label>
            <input type="tel" id="pesquisa_ncm" class="form-control"
                   onkeypress="return somenteNumeros(event)"
                   maxlength="10"                   
                   >
        </div>
        <div class="col-md-2">
            <label class="caption">Referência</label>
            <input type="tel" id="pesquisa_referencia" class="form-control"
                   onkeypress="return somenteNumeros(event)"
                   maxlength="14"
                   >
        </div>
        
        <div class="col-md-3">
            <label class="caption">Nome</label>
            <input type="tel" id="pesquisa_descricao" class="form-control"
                   style="text-transform: uppercase;"
                   >
        </div>
        
        <div class="col-md-3">
            <label class="caption">Filtro</label>
            <select id="pesquisa_filtro" class="form-control">
                <option value=""></option>
                <option value="S">AUDITADO 10 DIAS</option>                
                <option value="N">NÃO AUDITADOS</option>
            </select>
        </div>
        
        <div class="col-md-2 d-flex align-items-end">
            <div class="btn btn-success" style="width: 100%;"
                 v-on:click="pesquisar()"
                >
                <span class="fa fa-search"></span> Pesquisar
            </div>    
        </div>
    </div>
    
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
                                        <th></th>
                                        <th>Referência</th>
                                        <th width="100">Nome</th> 
                                        <th>NCM</th> 
                                        <th>CFOP</th> 
                                        <th>CEST</th> 
                                        <th>CSOSN</th> 
                                        <th>AUDITADO</th>
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
            myTable: null,    
            lista_impostos: {
                csosn: <?= json_encode(fiscal_get_impostos('CSOSN','ICMS')) ?>,
                origem: <?= json_encode(fiscal_get_impostos('ORIGEM','ICMS')) ?>,
                tipo: <?= json_encode(fiscal_get_impostos('TIPO','ICMS')) ?>,
                medida: ["BD", "BIS", "CM", "CT", "CX", "DZ", "FD", "FR", "GL", "GR", "KG", 
                         "KIT", "KU", "LT", "M2", "M3", "MG", "MIL", "MM", "PAR", "PC", 
                         "PCT", "PT", "RL", "SC", "TB", "TN", "UN", "UND"
                ],
                cst: <?= json_encode(fiscal_get_impostos('CST-ICMS','ICMS')) ?>,
                cst_ipi: <?= json_encode(fiscal_get_impostos('CST','IPI')) ?>,
                cst_pis: <?= json_encode(fiscal_get_impostos('CST-PIS','IPI')) ?>,
                mod_bc: <?= json_encode(fiscal_get_impostos('MOD-BC-ST','OUTROS')) ?>,
                motivo_deson: <?= json_encode(fiscal_get_impostos('DESO','ICMS')) ?>,
            },
            caption: '',
            impostos: [],
            imposto: {},            
            filtro_imposto: '',
            tabela_impostos: '',
            ncm: {},
        },
        methods: {
            novo() { 
                this.registro = {};  
                this.ncm = {};
                
                $('a[href="#produto-icms"]').tab('show');
                $("#produtoModal").modal('show');                
            },
                    
            editar(id) {       
                $.get('<?= base_url('Produtos/get') ?>?id='+id)
                .done(data => {                    
                    console.log(data);
                    let obj = JSON.parse(data);
                    if (obj) {
                        if (obj.status) {
                            this.registro = obj.data;                             
                            this.getNCM(this.registro.NCM);                            
                            
                            $('a[href="#produto-icms"]').tab('show');
                            $("#produtoModal").modal('show');
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
                $.post('<?= base_url('Produtos/salvar') ?>', this.registro)
                  .done(data => {                      
                    let obj = JSON.parse(data);
                    if (obj) {
                        if (obj.status) { 
                            alertSuccess(obj.msg);                            
                            this.myTable.ajax.reload();
                        } else {
                            alertError(obj.msg);
                        }
                    } else {
                        alert('Impossível retornar dados. Tente novamente.');
                    }                    
                  })
                  .fail(error => {
                    alert('Erro: '+JSON.stringify(error));
                  });
            },
                    
            excluir(id) {
                if (id === '' || id === undefined || id === 0) {
                    alertError('O registro não foi salvo.');
                    return false;
                }
                                
                alertQuestion('Deseja realmente excluir o registro agora?', () => {                    
                    $.post(this.base_url+'Produtos/excluir/'+id)
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
                    
            getImpostos(tabela, caption = '') {
                this.impostos = [];   
                this.tabela_impostos = tabela;
                this.caption = (caption !== '') ? caption : tabela; 
                
                $.get('<?= base_url('Impostos') ?>'+'/'+tabela)
                .done(data => {                                             
                    let obj = JSON.parse(data);
                    if (obj) {                          
                        if (obj.status) {
                            this.impostos = obj.data;                               
                            this.filtro_imposto = '';
                            $("#modalImpostos").modal('show');
                        } else {
                            alert('Erro: '+obj.msg);                                                      
                        }                      
                    } else {
                        alert('Impossível retornar dados. Tente novamente.');
                    }                                        
                })
                .fail(error => {
                    this.loading = false;
                    alert('Erro: '+JSON.stringify(error));
                }); 
            },
            
            selecionar_imposto(codigo) {  
                alert(this.tabela_impostos);
                $("#imp_"+this.tabela_impostos.toLowerCase()).val(codigo);                
                $("#modalImpostos").modal('hide');
            },                 
                 
            getNCM(ncm) {
                $.get('<?= base_url('Produtos') ?>/getNcm?ncm='+ncm)
                .done(data => {                                             
                    let obj = JSON.parse(data);
                    console.log(obj);
                    if (obj) {
                        if (obj.status) {
                            this.ncm = obj.data;
                        } else {
                            this.ncm = '';
                            $("#imp_ncm").val('');
                            this.$forceUpdate();
                            
                            alertError(obj.msg);                            
                        }                        
                    }                                       
                })
                .fail(error => {
                    this.loading = false;
                    alert('Erro: '+JSON.stringify(error));
                }); 
            },            
                    
            pesquisar() {
                let ncm        = ($("#pesquisa_ncm").val() || '').trim();
                let descricao  = ($("#pesquisa_descricao").val() || '').trim();
                let referencia = ($("#pesquisa_referencia").val() || '').trim();
                let filtro     = $("#pesquisa_filtro").val() || '';
                
                if (ncm === '' && descricao === '' && referencia === '' && filtro === '') {
                    alertError('Especifique ao menos um campo para a pesquisa.');                    
                    return false;
                }
                
                //if (ncm === '' && descricao === '' && referencia === '' && filtro === 'S') {
                //    alertError('Existem muitos produtos auditados. Especifique mais campos para a Pesquisa.');                    
                //    return false;
                //}
                
                if (ncm.length > 0 && ncm.length < 2) {
                    alertError('O NCM deve ter no mínimo 2 digitos para pesquisar.');
                    return false;
                }
                
                if (referencia.length > 0 && referencia.length < 3) {
                    alertError('A Referência deve ter no mínimo 3 digitos para pesquisar.');
                    return false;
                }
                
                if (descricao.length > 0 && descricao.length < 3) {
                    alertError('A Descrição deve ter no mínimo 3 caracteres para pesquisar.');
                    return false;
                }
                
                let url = this.base_url+'Produtos/getAll?'+
                      'ncm='+ ncm +
                      '&descricao='+ descricao +
                      '&referencia='+ referencia +
                      '&filtro='+ filtro;
                      
                this.myTable.ajax.url(url).load();
            }
            
        },    
        watch: {
            //
        },
        mounted() {  
            let url = this.base_url+'Produtos/getAll?'+
                      'ncm=00000000'+
                      '&descricao=00000000'+
                      '&referencia=00000000';
                      
            this.myTable = $("#tbRegistros").DataTable({     
                searching: true,
                paging: true,
                info: true, 
                pageLength: 100,
                processing: true,
                ajax: {
                    url: url,
                    dataSrc: 'data',
                },                
                order: [[0, 'asc']], // Ordena pela primeira coluna (id) em ordem crescente               
                columns: [
                    {
                        data: 'ID',
                        visible: false // Oculta a coluna de id
                    },
                    {   
                        data: 'REFERENCIA',
                        render: function(data, type, row) {
                            return '<div class="btn btn-link" style="font-weight: normal;" onclick="appModelo.editar('+row.ID+')">'+data+'</div>';
                        }                        
                    },
                    {   
                        data: 'DESCRICAO',                        
                    },
                    {  data: 'NCM' },
                    {  data: 'CFOP' },
                    {  data: 'CEST' },
                    {  data: 'CSOSN' }, 
                    {  data: 'DATA_HORA_AUDITADO' },
                    {
                        data: 'ID',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<i class="fa fa-edit" title="Editar" style="font-size: 20px; cursor: pointer; color: green;" onclick="appModelo.editar('+data+')" />';
                        }
                    },                   
                    {
                        data: 'ID',
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

