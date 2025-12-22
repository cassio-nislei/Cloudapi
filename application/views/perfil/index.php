<?php 
    if (!pode_ler('perfil', FALSE)) {
        echo '<br><center>Sem permissão para acessar este recurso!</center>';
        return false;
    }
?>

<h3><span class="fa fa-user"></span> PERFIL</h3>
<br>
<?php 
    include APPPATH . 'views/templates/regions/alert.php';
?>
<script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>

<div id="appUsuario">
    <!-- Button trigger modal -->
    <div class="row">
        <div class="col-md-12">
            <button type="button" class="btn btn-hti-out pull-right" data-toggle="modal" 
                    style="width: 150px;"
                    data-target="#modalAlterarSenha"                    
                    >
                <span class="fa fa-key"></span>
                Alterar Senha
            </button>
        </div>   
    </div>
    
        <div class="row">
            <div class="col-md-6">
                <label for="nome" class="caption">Nome</label>
                <input type="text" id="nome" name="nome" class="form-control"
                       v-model="usuario.nome"                    
                       >

            </div>
            <div class="col-md-6">
                <label for="cgc" class="caption">CPF/CNPJ</label>
                <input type="text" id="cgc" name="cgc" class="form-control"
                       v-model="usuario.cgc"
                       onkeypress="return somenteNumeros(event);"
                       maxlength="14"
                       >

            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label for="email" class="caption">E-mail</label>
                <input type="text" id="email" name="email" class="form-control" readonly="true"
                       v-model="usuario.email"
                       >

            </div>
            <div class="col-md-6">
                <label for="telefone" class="caption">Celular</label>
                <input type="text" id="telefone" name="celular" class="form-control celular">
            </div>
        </div>
    
        <div class="row">
            <div class="col-md-6">
                <label for="nome" class="caption">Grupo</label>
                <input type="text" id="nome" name="nome" class="form-control" disabled="disabled"
                       value="<?= $this->session->userdata('user.grupo') ?>"
                       >
            </div>
        </div>

        <div class="row" style="margin-top: 25px;">    
            <div class="col-md-12">
                <button class="btn btn-hti"
                        style="width: 120px;"
                        v-on:click="atualizar()"
                        >
                    Gravar
                </button>
            </div>
        </div>
    

    <!-- Modal alterar senha -->
    <div class="modal fade" id="modalAlterarSenha" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title pull-left" id="exampleModalLabel"><b><span class="fa fa-key"></span> ALTERAR SENHA</b></h5>
            <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <label class="caption" style="font-size: 14px;">Senha Atual</label>
              <input type="password" id="senha_atual" class="form-control">
              <label class="caption" style="font-size: 14px;">Nova Senha</label>
              <input type="password" id="senha_nova" class="form-control">
              <label class="caption" style="font-size: 14px;">Repetir Nova Senha</label>
              <input type="password" id="senha_confirm" class="form-control">
              <br>
          </div>
          <div class="modal-footer">        
            <button type="button" class="btn btn-hti" style="width: 120px;" 
                    v-on:click="alterarSenha()">Confirmar</button>
          </div>
        </div>
      </div>
    </div>
</div>

<script>
    let appUsuario = new Vue({
        el:"#appUsuario",
        mixins: [mxFunctions],
        data: {
            usuario: {},
        },
        methods: {
            getData() {
                $.get(this.base_url+'Perfil')
                .done(obj => {                          
                    if (obj.status) {
                        this.usuario = obj.data; 
                        $("#telefone").val(this.usuario.telefone);
                    } else {
                        alert(obj.msg);
                    }
                })
                .fail(err => {
                    alert(JSON.stringify(err));
                });
                
            },
            
            atualizar() {
                this.usuario.telefone = $("#telefone").val();
                
                $.post(this.base_url+'Perfil',{ 
                    action: 'update', 
                    registro: this.usuario 
                })
                .done(obj => {                          
                    alert(obj.msg);
                    if (obj.status) {
                        window.location.reload();
                    } 
                })
                .fail(err => {
                    alert(JSON.stringify(err));
                });
            },
                    
            showMudarSenha() {
                $("#modalAlterarSenha").modal('show');
            },
            
            alterarSenha() {
                var atual   = $("#senha_atual").val();
                var nova    = $("#senha_nova").val();
                var confirm = $("#senha_confirm").val();
                
                if (atual === '' || nova === '' || confirm === '') {
                    alert('Especifique todos os dados.');
                    return false;
                }
                
                if (nova !== confirm) {
                    alert('A nova senha está diferente da confirmação.');
                    return false;
                }
                
                $.post(this.base_url+'Perfil',{ 
                    action: 'changepass', 
                    atual: atual,
                    nova: nova,
                    confirmacao: confirm 
                })
                .done(obj => {                          
                    alert(obj.msg);
                    if (obj.status) {
                        window.location.reload();
                    } 
                })
                .fail(err => {
                    alert(JSON.stringify(err));
                });                          
                
            },
        },
        mounted() {
            this.getData();
        }
    });

</script>

