<center>
    <h3>
        Bem-vindo <?= $this->session->userdata('user.nome') ?>! 
    </h3>
</center>
<br>
<?php    
    if (!$this->session->userdata('logado')) {
        die("Acesso negado!");
    }
?>
<small>Emitente</small><br>
<?= $this->session->userdata('emit.nome') ?>
<br><br>

<small>Usuário</small><br>
<?= $this->session->userdata('user.nome') ?>
<br><br>

<small>Grupo</small><br>
<?= $this->session->userdata('user.grupo') ?>
<br><br>

<small>E-mail</small><br>
<?= $this->session->userdata('user.email') ?>
<br><br>

<?php /*
<small>Token Auth</small><br>
<?= $this->session->userdata('user.token_auth') ?>
<br><br>

<small>Basic Auth - b64(email:token)</small><br>
<?= $this->session->userdata('user.auth') ?>
<br><br>

<hr>
Dados na Session
<textarea class="form-control" style="height: 150px; margin-top: 5px; font-size: 12px;" readonly="true"><?php 
    echo json_encode($this->session->userdata());
?></textarea>
*/ ?>

