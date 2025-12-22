<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <!-- Required Meta Tags Always Come First -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Title -->
  <title>AdmCloud</title>
  <meta name="description" content="AdmCloud">

  <!-- Favicon -->
  <link rel="apple-touch-icon" href="<?= base_url('assets/images/icon.png')?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/icon.png') ?>">

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">

  <!-- CSS Implementing Plugins -->
  <link rel="stylesheet" href="<?= base_url('/assets/dashboard/vendor/icon-set/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/dashboard/vendor/hs-mega-menu/dist/hs-mega-menu.min.css') ?>">

  
  <script src="<?= base_url('assets/vue-2.7.15/vue.js') ?>"></script>
  <script src="<?= base_url('assets/vue-2.7.15/axios.min.js') ?>"></script>
  <script src="<?= base_url('assets/vue-2.7.15/cleave.min.js') ?>"></script>
  <script src="<?= base_url('assets/vue-2.7.15/vue-cleave-directive.min.js') ?>"></script>
  
  <script src="<?= base_url('assets/js/vue/mixin.js?v='.uniqid()) ?>"></script>
  
  <!-- CSS Front Template -->  
  <link rel="stylesheet" href="<?= base_url('/assets/dashboard/css/theme.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/template.css?v='.uniqid()) ?>">
  
  <link rel="stylesheet" href="<?= base_url('assets/dashboard/vendor/icon-set/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/dashboard/vendor/select2/dist/css/select2.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/dashboard/vendor/flatpickr/dist/flatpickr.min.css') ?>">
  <?php /*
  <link rel="stylesheet" href="<?= base_url('assets/dashboard/vendor/chart.js/dist/Chart.min.css') ?>">
   */?>
  <link rel="stylesheet" href="<?= base_url('assets/dashboard/vendor/daterangepicker/daterangepicker.css') ?>">
    
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
       
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <script>
    function alertError(str) {
        Swal.fire({                                
            text: str,
            icon: "error"
        });
    }

    function alertSuccess(str) {
        Swal.fire({                                
            text: str,
            icon: "success"
        });
    }  
    
    function alertQuestion(str, callback) {
        Swal.fire({                   
            showDenyButton: true,
            confirmButtonText: "OK",
            denyButtonText: "Cancelar",
            text: str,
            icon: "question"
        }).then((result) => {                    
            if (result.isConfirmed) {
                callback();
            } 
        });
    }
  </script>
  
    <style>
        .navbar .nav-link {
            color: #fff; font-weight: bold; font-size: 14px;
            text-transform: uppercase;
        }
        
        .navbar .nav-link:hover {
            color: #45A6D7;
        }
        
        .dropdown-menu {
            background-color: #45A6D7; border-radius: 0px;
        }
        
        .dropdown-item {
            color: #fff;
        }
        
        .dropdown-item:hover {
            color: #fff;
            background-color: silver;
        }
        
        .navbar-brand-logo {
            width: 100%;
            min-width: 9.5rem;
            max-width: 8.5rem;
        }
    </style>
</head>
<body style="background-color: #F3F5FA;">    
  <!-- ========== HEADER ========== -->
  <header id="header" class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered"
          style="background-color: #37517E; color: #fff; border-style: none; height: 74px;">      
    <div class="container">
      <div class="js-mega-menu navbar-nav-wrap">
        <div class="navbar-brand-wrapper">
          <!-- Logo -->
          
          
            <a class="navbar-brand" href="<?= base_url() ?>" aria-label="Front">
              <img class="navbar-brand-logo"  
                   src="<?= base_url('images/logo_fg2.png') ?>" alt="Logo">
            </a>
          
          <!-- End Logo -->
        </div>

        <!-- Secondary Content -->
        <div class="navbar-nav-wrap-content-right">
          <!-- Navbar -->
          <ul class="navbar-nav align-items-center flex-row">
            <?php /*
            <li class="nav-item">
              <!-- Account -->
              <div class="hs-unfold">
                <a class="js-hs-unfold-invoker navbar-dropdown-account-wrapper" href="javascript:;"
                   data-hs-unfold-options='{
                     "target": "#accountNavbarDropdown",
                     "type": "css-animation"
                   }'>
                  <div class="avatar avatar-sm avatar-circle">
                    <img class="avatar-img" src="<?= base_url('/assets/dashboard/img/160x160/img1.jpg') ?>" alt="Image Description">
                    <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                  </div>
                </a>

                
                <div id="accountNavbarDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account" style="width: 16rem;">
                  <div class="dropdown-item-text">
                      <span class="card-title h5" style="text-align: center; margin-bottom: 10px;"><?= $this->session->userdata('emit.nome') ?></span>
                    <div class="media align-items-center">
                      <div class="avatar avatar-sm avatar-circle mr-2">
                        <img class="avatar-img" src="<?= base_url('/assets/dashboard/img/160x160/img1.jpg') ?>" alt="Image Description">
                      </div>
                      <div class="media-body">
                        
                        <span class="card-text"><?= $this->session->userdata('user.nome') ?></span>
                        <span class="card-text" style="font-size: 12px; color: silver;"><?= $this->session->userdata('user.email') ?></span>
                      </div>
                    </div>
                  </div>

                  <div class="dropdown-divider"></div>

                  <a class="dropdown-item" href="<?= base_url('perfil') ?>">
                    <span class="text-truncate pr-2" title="Editar perfil">Perfil</span>
                  </a>

                  <div class="dropdown-divider"></div>

                  <a class="dropdown-item" href="#">
                    <span class="text-truncate pr-2" title="Sair do sistema"
                          onclick="logout();"
                          >Sair</span>
                  </a>
                </div>
              </div>
              <!-- End Account -->
            </li>*/?>

            <li class="nav-item">
              <!-- Toggle -->
              <button type="button" class="navbar-toggler btn btn-ghost-secondary rounded-circle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navbarNavMenu" data-toggle="collapse" data-target="#navbarNavMenu">
                <i class="tio-menu-hamburger"></i>
              </button>
              <!-- End Toggle -->
            </li>
          </ul>
          <!-- End Navbar -->
        </div>
        <!-- End Secondary Content -->

        <!-- Navbar -->
        <div class="navbar-nav-wrap-content-left collapse navbar-collapse" id="navbarNavMenu">
          <div class="navbar-body">
            <ul class="navbar-nav flex-grow-1">
              
              <li>
                <a class="navbar-nav-link nav-link" href="<?= base_url('Portal/pessoas') ?>">
                  <i class="bi bi-people nav-icon" style="font-size: 15px;"></i> Pessoas
                </a>
              </li>
              
              <li>
                <a class="navbar-nav-link nav-link" href="<?= base_url('Ncm') ?>">
                  <i class="bi bi-file-earmark nav-icon" style="font-size: 15px;"></i> NCM's
                </a>
              </li>
              
              <li>
                <a class="navbar-nav-link nav-link" href="<?= base_url('Produtos') ?>">
                  <i class="bi bi-box nav-icon" style="font-size: 15px;"></i> Produtos
                </a>
              </li>
              
              <li>
                <a class="navbar-nav-link nav-link" href="<?= base_url('NotasEnviadas') ?>">
                  <i class="bi bi-filetype-xml nav-icon" style="font-size: 15px;"></i> Notas Enviadas
                </a>
              </li>
              
              <!--
              <li class="hs-has-sub-menu">
                <a id="dashboardsDropdown" class="hs-mega-menu-invoker navbar-nav-link nav-link nav-link-toggle" href="javascript:;" aria-haspopup="true" aria-expanded="false" aria-labelledby="navLinkDashboardsDropdown">
                  <i class="bi bi-file-earmark-text nav-icon" style="font-size: 15px; margin-right: -10px;"></i> Tabelas
                </a>
                  
                <ul id="navLinkDashboardsDropdown" class="hs-sub-menu dropdown-menu dropdown-menu-lg" 
                    aria-labelledby="dashboardsDropdown" style="min-width: 16rem;">
                  <li>
                    <a class="dropdown-item" href="<?= base_url('Tabelas/ncm') ?>">
                      <span class="tio-circle nav-indicator-icon"></span> NCM
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="<?= base_url('Tabelas/cfop') ?>">
                      <span class="tio-circle nav-indicator-icon"></span> CFOP
                    </a>
                  </li>   
                  <li>
                    <a class="dropdown-item" href="<?= base_url('Tabelas/cest') ?>">
                      <span class="tio-circle nav-indicator-icon"></span> CEST
                    </a>
                  </li>   
                </ul>
              </li>              
              <!-- End Sysop -->

              <!-- Aprovacoes              
              <li>
                <a class="navbar-nav-link nav-link" href="<?= base_url('Portal/aprovacoes') ?>">
                  <i class="bi bi-bookmarks" style="font-size: 15px;"></i> Aprovações
                </a>
              </li>              
              <!-- End Aprovacoes  -->
                            
              <!-- Filiais             
              <?php if (pode_ler('Filiais', FALSE)): ?>
              <li>
                <a class="navbar-nav-link nav-link" href="<?= base_url('Portal/filiais') ?>">
                  <i class="bi bi-shop-window" style="font-size: 15px;"></i> Filiais
                </a>
              </li>             
              <?php endif; ?>
              <!-- End Vendedores  -->              
              
              <!-- Dashboards          
              <li class="hs-has-sub-menu">
                <a id="dashboardsDropdown" class="hs-mega-menu-invoker navbar-nav-link nav-link nav-link-toggle" href="javascript:;" aria-haspopup="true" aria-expanded="false" aria-labelledby="navLinkDashboardsDropdown">
                  <i class="bi bi-gear nav-icon" style="font-size: 15px; margin-right: -10px;"></i> Configurações
                </a>

                <!-- Dropdown -->
                <ul id="navLinkDashboardsDropdown" class="hs-sub-menu dropdown-menu dropdown-menu-lg" aria-labelledby="dashboardsDropdown" style="min-width: 16rem;">
                  <?php if (pode_ler('Empresa', FALSE)): ?>
                  <li>
                    <a class="dropdown-item" href="<?= base_url('empresa') ?>">
                      <span class="tio-circle nav-indicator-icon"></span> Empresa
                    </a>
                  </li>
                  <?php endif; ?>
                   <li>
                    <a class="dropdown-item" href="<?= base_url('perfil') ?>">
                      <span class="tio-circle nav-indicator-icon"></span> Perfil
                    </a>
                  </li> 
                  <?php if (pode_ler('Usuarios', FALSE)): ?>
                  <li>
                    <a class="dropdown-item" href="<?= base_url('usuarios') ?>">
                      <span class="tio-circle nav-indicator-icon"></span> Usuários
                    </a>
                  </li>
                  <?php endif; ?>
                 
                  <!-- <li>
                    <a class="dropdown-item" href="<?= base_url('Promoters/index') ?>">
                      <span class="tio-circle nav-indicator-icon"></span> Promoters
                    </a>
                  </li>  -->
                </ul>
                <!-- End Dropdown -->
              </li>              
              <!-- End Dashboards -->
              
              <?php /* if ($this->session->userdata('user.sysop')): ?>
              <!---------------- SYSOP ---------------------------------------->
              <!-- Sysop -->              
              <li class="hs-has-sub-menu">
                <a id="dashboardsDropdown" class="hs-mega-menu-invoker navbar-nav-link nav-link nav-link-toggle" href="javascript:;" aria-haspopup="true" aria-expanded="false" aria-labelledby="navLinkDashboardsDropdown">
                  <i class="bi bi-incognito nav-icon" style="font-size: 15px; margin-right: -10px;"></i> Sysop
                </a>

                <!-- Dropdown -->
                <ul id="navLinkDashboardsDropdown" class="hs-sub-menu dropdown-menu dropdown-menu-lg" aria-labelledby="dashboardsDropdown" style="min-width: 16rem;">
                  <li>
                    <a class="dropdown-item" href="<?= base_url('modulos') ?>">
                      <span class="tio-circle nav-indicator-icon"></span> Módulos
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="<?= base_url('grupos') ?>">
                      <span class="tio-circle nav-indicator-icon"></span> Grupos
                    </a>
                  </li>  
                  <li>
                    <a class="dropdown-item" href="<?= base_url('usuarios') ?>">
                      <span class="tio-circle nav-indicator-icon"></span> Usuários
                    </a>
                  </li>
                </ul>
                <!-- End Dropdown -->
              </li>              
              <!-- End Sysop -->
              <!--------------------------------------------------------------->
              <?php endif; */ ?>     
              
              <!-- Eventos -->
              <li>
                <a class="navbar-nav-link nav-link" href="#" onclick="logout()">
                  <i class="bi bi-box-arrow-right nav-icon" style="font-size: 15px;"></i> Sair
                </a>
              </li>
              <!-- End Eventos  -->
              

              <?php /*
              <!-- Layouts -->
              <li>
                <a class="navbar-nav-link nav-link" href="../layouts/layouts.html">
                  <i class="tio-dashboard-vs-outlined nav-icon"></i> Layouts
                </a>
              </li>
              <!-- End Layouts -->

              <!-- Documentation -->
              <li class="hs-has-sub-menu">
                <a id="documentationDropdown" class="hs-mega-menu-invoker navbar-nav-link nav-link nav-link-toggle" href="javascript:;" aria-haspopup="true" aria-expanded="false" aria-labelledby="navLinkDocumentationDropdown">
                  <i class="tio-book-opened nav-icon"></i> Docs
                </a>

                <!-- Dropdown -->
                <ul id="navLinkDocumentationDropdown" class="hs-sub-menu dropdown-menu dropdown-menu-lg" aria-labelledby="documentationDropdown" style="min-width: 16rem;">
                  <li>
                    <a class="dropdown-item" href="../documentation/index.html">
                      <span class="tio-circle nav-indicator-icon"></span> Documentation <span class="badge badge-primary badge-pill ml-1">v1.0</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="../documentation/index.html">
                      <span class="tio-circle nav-indicator-icon"></span> Components
                    </a>
                  </li>
                </ul>
                <!-- End Dropdown -->
              </li>
              <!-- End Documentation -->
               */ ?>               
            </ul>

          </div>
        </div>
        <!-- End Navbar -->
      </div>
    </div>
  </header>
  <!-- ========== END HEADER ========== -->

  <!-- ========== MAIN CONTENT ========== -->
  <main id="content" role="main" class="main">
    <!-- Content -->
    <div class="content container" <?= (!isMobile()) ? 'style="width: 80%; margin-left: auto; margin-right: auto;"' : '' ?> >
        <div style="padding: 15px; background-color: #fff; border-radius: 3px; 
                    margin-top: <?= isMobile() ? '5px' : '20px' ?>; padding-bottom: 30px;">
            <?= $content ?>                  
        </div>    
    </div>
    <!-- End Content -->
  </main>
  <!-- ========== END MAIN CONTENT ========== -->

  <!-- ========== SECONDARY CONTENTS ========== -->
  <!-- Welcome Message Modal -->
  <div class="modal fade" id="welcomeMessageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <!-- Header -->
        <div class="modal-close">
          <button type="button" class="btn btn-icon btn-sm btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
            <i class="tio-clear tio-lg"></i>
          </button>
        </div>
        <!-- End Header -->

        <!-- Body -->
        <div class="modal-body p-sm-5">
          <div class="text-center">
            <div class="w-75 w-sm-50 mx-auto mb-4">
              <img class="img-fluid" src="<?= base_url('/assets/dashboard/assets/svg/illustrations/graphs.svg') ?>" alt="Image Description">
            </div>

            <h4 class="h1">Welcome to Front</h4>

            <p>We're happy to see you in our community.</p>
          </div>
        </div>
        <!-- End Body -->

        <!-- Footer -->
        <div class="modal-footer d-block text-center py-sm-5">
          <small class="text-cap mb-4">Trusted by the world's best teams</small>

          <div class="w-85 mx-auto">
            <div class="row justify-content-between">
              <div class="col">
                <img class="img-fluid ie-welcome-brands" src="<?= base_url('/assets/dashboard/svg/brands/gitlab-gray.svg') ?>" alt="Image Description">
              </div>
              <div class="col">
                <img class="img-fluid ie-welcome-brands" src="<?= base_url('/assets/dashboard/svg/brands/fitbit-gray.svg') ?>" alt="Image Description">
              </div>
              <div class="col">
                <img class="img-fluid ie-welcome-brands" src="<?= base_url('/assets/dashboard/svg/brands/flow-xo-gray.svg') ?>" alt="Image Description">
              </div>
              <div class="col">
                <img class="img-fluid ie-welcome-brands" src="<?= base_url('/assets/dashboard/svg/brands/layar-gray.svg') ?>" alt="Image Description">
              </div>
            </div>
          </div>
        </div>
        <!-- End Footer -->
      </div>
    </div>
  </div>
  <!-- End Welcome Message Modal -->
  <!-- ========== END SECONDARY CONTENTS ========== -->
  
  <!-- JS Global Compulsory  -->
  <script src="<?= base_url('/assets/dashboard/vendor/jquery/dist/jquery.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/jquery-migrate/dist/jquery-migrate.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>

  <!-- JS Implementing Plugins -->
  <script src="<?= base_url('/assets/dashboard/vendor/hs-unfold/dist/hs-unfold.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/hs-form-search/dist/hs-form-search.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/hs-mega-menu/dist/hs-mega-menu.min.js') ?>"></script>

  <!-- JS Front -->
  <script src="<?= base_url('/assets/dashboard/js/theme.min.js') ?>"></script>
  
  <script src="<?= base_url('assets/js/utils.js?v=1.3') ?>"></script>
  
  <!-- JS Implementing Plugins -->
  <script src="<?= base_url('/assets/dashboard/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/hs-unfold/dist/hs-unfold.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/hs-form-search/dist/hs-form-search.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/select2/dist/js/select2.full.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/flatpickr/dist/flatpickr.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/hs-nav-scroller/dist/hs-nav-scroller.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/daterangepicker/moment.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/daterangepicker/daterangepicker.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/chart.js/dist/Chart.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/clipboard/dist/clipboard.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/appear/dist/appear.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/vendor/circles.js/circles.min.js') ?>"></script>

  <!-- JS Front -->
  <script src="<?= base_url('/assets/dashboard/js/theme.min.js') ?>"></script>
  <script src="<?= base_url('/assets/dashboard/js/hs.chartjs-matrix.js') ?>"></script>       

  <!-- mascaras -->
  <script src="<?= base_url('assets/js/jquery.maskedinput.js') ?>" type="text/javascript"></script>
  <script src="<?= base_url('assets/js/jquery.maskMoney.min.js') ?>" type="text/javascript"></script>

  <!-- date picker -->
  <link href="<?= base_url('assets/jquery-ui-1.11.4/jquery-ui.min.css') ?>" rel="stylesheet" type="text/css"/>
  <script src="<?= base_url('assets/jquery-ui-1.11.4/jquery-ui.min.js') ?>" type="text/javascript"></script>
  <script src="<?= base_url('assets/jquery-ui-1.11.4/jquery.ui.datepicker-pt-BR.js') ?>" type="text/javascript"></script>

  <!-- traducao -->
  <script src="<?= base_url('assets/js/globalize.culture.pt-BR.js') ?>" type="text/javascript"></script>
  <script src="<?= base_url('assets/js/validator.methods.number.pt-BR.js') ?>" type="text/javascript"></script>

  <!-- JS Plugins Init. -->
  <script>
    $(document).on('ready', function () {
        $(".monetario").maskMoney({ showSymbol: false, decimal: ",", thousands: "." });        
        $(".peso").maskMoney({ showSymbol: false, decimal: ".", thousands: "", precision: 3 });
        
        $(".datepicker").mask("99/99/9999");
        $(".hora").mask("99:99");
        
        $(".telefone").mask("(99)9999-9999");
        $(".celular").mask("(99)99999-9999");
        $(".cep").mask("99999-999");
        
        $(".cartao").mask("9999-9999-9999-9999");
        $(".mesano").mask("99/99");
        $(".code").mask("999");  
        
        //show calendario
        $(".datepicker").datepicker({ dateFormat: "dd/mm/yy" });
        
        $('.somenteLetrasNumeros').keypress(function (e) {
            var regex = new RegExp("^[a-zA-Z0-9\b]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }

            e.preventDefault();
            return false;
        });
        
        $('.somenteLetras').keypress(function (e) {
            var regex = new RegExp("^[a-zA-Z\b]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }

            e.preventDefault();
            return false;
        });
        
        $('.somenteNumeros').keypress(function (e) {
            var regex = new RegExp("^[0-9\b]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }

            e.preventDefault();
            return false;
        });
        
        // INITIALIZATION OF UNFOLD
        // =======================================================
        $('.js-hs-unfold-invoker').each(function () {
          var unfold = new HSUnfold($(this)).init();
        });


        // INITIALIZATION OF FORM SEARCH
        // =======================================================
        $('.js-form-search').each(function () {
          new HSFormSearch($(this)).init()
        });


        // INITIALIZATION OF MEGA MENU
        // =======================================================
        var megaMenu = new HSMegaMenu($('.js-mega-menu'), {
          desktop: {
            position: 'left'
          }
        }).init();

        // INITIALIZATION OF CIRCLES
        // =======================================================
        $('.js-circle').each(function () {
          var circle = $.HSCore.components.HSCircles.init($(this));
        });
        
        // INITIALIZATION OF CHARTJS
        // =======================================================
        $('.js-chart').each(function () {
          $.HSCore.components.HSChartJS.init($(this));
        });

        var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

        // Datasets for chart, can be loaded from AJAX request
        var updatingChartDatasets = [
          [
            [45, 25, 30]
          ],
          [
            [35, 50, 15]
          ]
        ]

        // Set datasets for chart when page is loaded
        updatingChart.data.datasets.forEach(function (dataset, key) {
          dataset.data = updatingChartDatasets[0][key];
        });
        updatingChart.update();

        // Call when tab is clicked
        $('[data-toggle="chart"]').click(function(e) {
          let keyDataset = $(e.currentTarget).attr('data-datasets')

          // Update datasets for chart
          updatingChart.data.datasets.forEach(function (dataset, key) {
             dataset.data = updatingChartDatasets[keyDataset][key];
          });
          updatingChart.update();
        })

        
        // INITIALIZATION OF MATRIX CHARTJS WITH CHARTJS MATRIX PLUGIN
        // =======================================================
        function generateHoursData() {
          var data = [];
          var dt = moment().subtract(365, 'days').startOf('day');
          var end = moment().startOf('day');
          while(dt <= end) {
            data.push({
              x: dt.format('YYYY-MM-DD'),
              y: dt.format('e'),
              d: dt.format('YYYY-MM-DD'),
              v: Math.random() * 24
            });
            dt = dt.add(1, 'day');
          }
          return data;
        }

        $.HSCore.components.HSChartMatrixJS.init($('.js-chart-matrix'), {
          data: {
            datasets: [{
              label: 'Commits',
              data: generateHoursData(),
              width: function(ctx) {
                var a = ctx.chart.chartArea;
                return (a.right - a.left) / 70;
              },
              height: function(ctx) {
                var a = ctx.chart.chartArea;
                return (a.bottom - a.top) / 10;
              }
            }]
          },
          options: {
            tooltips: {
              callbacks: {
                title: function() { return '';},
                label: function(item, data) {
                  var v = data.datasets[item.datasetIndex].data[item.index];

                  if (v.v.toFixed() > 0) {
                    return '<span class="font-weight-bold">' + v.v.toFixed() + ' hours</span> on ' + v.d;
                  }  else {
                    return '<span class="font-weight-bold">No time</span> on ' + v.d;
                  }
                }
              }
            },
            scales: {
              xAxes: [{
                position: 'bottom',
                type: 'time',
                offset: true,
                time: {
                  unit: 'week',
                  round: 'week',
                  displayFormats: {
                    week: 'MMM'
                  }
                },
                ticks: {
                  "labelOffset": 20,
                  "maxRotation": 0,
                  "minRotation": 0,
                  "fontSize": 12,
                  "fontColor": "rgba(22, 52, 90, 0.5)",
                  "maxTicksLimit": 12,
                },
                gridLines: {
                  display: false
                }
              }],
              yAxes: [{
                type: 'time',
                offset: true,
                time: {
                  unit: 'day',
                  parser: 'e',
                  displayFormats: {
                    day: 'ddd'
                  }
                },
                ticks: {
                  "fontSize": 12,
                  "fontColor": "rgba(22, 52, 90, 0.5)",
                  "maxTicksLimit": 2,
                },
                gridLines: {
                  display: false
                }
              }]
            }
          }
        });
        
    });
    
    function logout() {
        if (confirm('Deseja realmente sair do sistema agora?')) {
            window.location.href = '<?= base_url('Account/logout') ?>';
        }
    }
  </script>
  
  <!-- IE Support -->
  <script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="<?= base_url('/assets/dashboard/vendor/babel-polyfill/polyfill.min.js') ?>"><\/script>');
  </script>
</body>
</html>