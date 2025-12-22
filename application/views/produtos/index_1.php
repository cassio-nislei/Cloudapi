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

<h3><span class="fa fa-users"></span> NCM's</h3>
<div id="appModelo">
    <?php 
        include_once APPPATH . 'views/ncm/regions/ncm_modal.php';  
        include_once APPPATH . 'views/ncm/regions/impostos_modal.php'; 
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
                                        <th></th>
                                        <th>NCM</th>
                                        <th width="100">Descrição</th> 
                                        <th>CSOSN</th> 
                                        <th>CEST</th> 
                                        <th>CST</th> 
                                        <th>CFOP</th> 
                                        <th>Auditoria</th> 
                                        
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

                $("#emit-basico").removeClass('active');
                $("#emit-financeiro").removeClass('active');
                $("#emit-atualizacoes").removeClass('active');
                $("#emit-outros").removeClass('active');   
                $("#emit-licencas").removeClass('active');   

                $("#emit-basico-tab").addClass('active');
                $("#emit-basico").addClass('active');
                $("#emit-basico").addClass('show');
            },
            
            novo() { 
                this.registro = {};   
        
                $("#imp_ncm").val('');
                $("#imp_cfop").val('');
                $("#imp_cest").val('');
                $("#imp_aliq_icms").val('');
                $("#imp_red_bc").val('');
                $("#imp_cred_sn").val('');
                $("#imp_aliq_ipi").val('');
                
                $("#imp_pc_perc_pis_saida").val('');
                $("#imp_pc_perc_cofins_saida").val('');
                $("#imp_pc_perc_pis_entrada").val('');
                $("#imp_pc_perc_cofins_entrada").val('');
                
                $("#imp_cfop_externo").val('');    
                $("#imp_aliq_icms_externo").val('');
                $("#imp_mva_normal").val('');
                $("#imp_mva_simples").val('');
                $("#imp_fcp").val('');
                $("#imp_glp").val('');
                $("#imp_gnn").val('');
                $("#imp_gni").val('');
                $("#imp_anp").val('');
                
                $("#imp_icms_diferido").val('');
                $("#imp_aliq_deson").val('');
                
                $('a[href="#produto-icms"]').tab('show');
                $("#ncmModal").modal('show');                
            },
                    
            editar(id) {                  
                $.get('<?= base_url('Ncm/get') ?>?id='+id)
                .done(data => {                    
                    console.log(data);
                    let obj = JSON.parse(data);
                    if (obj) {
                        if (obj.status) {
                            this.registro = obj.data; 
                            
                            $("#imp_ncm").val(this.registro.IMP_NCM);
                            $("#imp_cfop").val(this.registro.CFOP);
                            $("#imp_cest").val(this.registro.CEST);
                            $("#imp_aliq_icms").val(this.registro.IMP_ALIQ_ICM);
                            $("#imp_red_bc").val(this.registro.RED_BC);
                            $("#imp_cred_sn").val(this.registro.CRED_SN);
                            $("#imp_aliq_ipi").val(this.registro.IPI_PERCENTUAL);

                            $("#imp_pc_perc_pis_saida").val(this.registro.PISCOFINS_PERC_PIS_SAIDA);
                            $("#imp_pc_perc_cofins_saida").val(this.registro.PISCOFINS_PERC_COFINS_SAIDA);
                            $("#imp_pc_perc_pis_entrada").val(this.registro.PISCOFINS_PERC_PIS_ENTRADA);
                            $("#imp_pc_perc_cofins_entrada").val(this.registro.PISCOFINS_PERC_COFINS_ENTRADA);

                            $("#imp_cfop_externo").val(this.registro.IMP_CFOP_EXTERNO);
                            $("#imp_aliq_icms_externo").val(this.registro.IMP_ALIQ_ICMS_EXTERNO);
                            $("#imp_mva_normal").val(this.registro.IMP_MVA_NORMAL);
                            $("#imp_mva_simples").val(this.registro.IMP_MVA);
                            $("#imp_fcp").val(this.registro.IMP_FCP);
                            $("#imp_glp").val(this.registro.IMP_GLP);
                            $("#imp_gnn").val(this.registro.IMP_GNN);
                            $("#imp_gni").val(this.registro.IMP_GNI);
                            $("#imp_anp").val(this.registro.IMP_ANP);
                            $("#imp_icms_diferido").val(this.registro.IMP_ICMS_DIFERIDO);
                            $("#imp_aliq_deson").val(this.registro.IMP_ALIQ_DESON);
                            
                            $('a[href="#produto-icms"]').tab('show');
                            $("#ncmModal").modal('show');
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
                //devido a maskaras, tenho q repassar
                //this.registro.NCM    = $("#imp_ncm").val();
                //this.registro.CFOP   = $("#imp_cfop").val();
                //this.registro.CEST   = $("#imp_cest").val();

                this.registro.IMP_ALIQ_ICM   = $("#imp_aliq_icms").val();
                this.registro.RED_BC     = $("#imp_red_bc").val();
                this.registro.CRED_SN    = $("#imp_cred_sn").val();                
                this.registro.IPI_PERCENTUAL   = $("#imp_aliq_ipi").val();

                this.registro.PISCOFINS_PERC_PIS_SAIDA      = $("#imp_pc_perc_pis_saida").val();
                this.registro.PISCOFINS_PERC_COFINS_SAIDA   = $("#imp_pc_perc_cofins_saida").val();
                this.registro.PISCOFINS_PERC_PIS_ENTRADA    = $("#imp_pc_perc_pis_entrada").val();
                this.registro.PISCOFINS_PERC_COFINS_ENTRADA = $("#imp_pc_perc_cofins_entrada").val();

                //this.registro.IMP_CFOP_EXTERNO      = $("#imp_cfop_externo").val();
                this.registro.IMP_ALIQ_ICMS_EXTERNO = $("#imp_aliq_icms_externo").val();
                this.registro.IMP_MVA_NORMAL        = $("#imp_mva_normal").val();
                this.registro.IMP_MVA               = $("#imp_mva_simples").val();
                
                this.registro.IMP_FCP = $("#imp_fcp").val();
                this.registro.IMP_GLP = $("#imp_glp").val();
                this.registro.IMP_GNN = $("#imp_gnn").val();
                this.registro.IMP_GNI = $("#imp_gni").val();
                this.registro.IMP_ANP = $("#imp_anp").val();

                this.registro.IMP_ICMS_DIFERIDO = $("#imp_icms_diferido").val();
                this.registro.IMP_ALIQ_DESON = $("#imp_aliq_deson").val();
                
                /*
                if (!this.registro.NCM || this.registro.NCM === '') {
                    alert('O campo Descrição é obrigatório.');
                    return false;
                }
                
                if (!this.registro.DESCRICAO || this.registro.DESCRICAO === '') {
                    alert('O campo Descrição é obrigatório.');
                    return false;
                }
                */
                                
                $.post('<?= base_url('Ncm/salvar') ?>', this.registro)
                  .done(data => {  
                    console.log(data);
                    let obj = JSON.parse(data);
                    if (obj) {
                        if (obj.status) {                            
                            alert(obj.msg); 
                            this.myTable.ajax.reload();
                        } else {
                            alert(obj.msg);
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
                    alert('O registro não foi salvo.');
                    return false;
                }
                if (!confirm('Deseja realmente excluir este registro?')) {
                    return false;
                }
                
                $.post(this.base_url+'Ncm/excluir/'+id)
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
                    url: this.base_url+'Ncm/getAll',
                    dataSrc: 'data',
                },                
                order: [[0, 'asc']], // Ordena pela primeira coluna (id) em ordem crescente               
                columns: [
                    {
                        data: 'ID',
                        visible: false // Oculta a coluna de id
                    },
                    {   
                        data: 'NCM',
                        render: function(data, type, row) {
                            return '<div class="btn btn-link" style="font-weight: normal;" onclick="appModelo.editar('+row.ID+')">'+data+'</div>';
                        }                        
                    },
                    {   
                        data: 'DESCRICAO',                        
                    },
                    {  data: 'CSOSN' },
                    {  data: 'CEST' },
                    {  data: 'CST' },
                    {  data: 'CFOP' },
                    {  data: 'DATA_AUDITORIA' },                    
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

