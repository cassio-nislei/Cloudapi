<div style="text-align: center; margin-top: 30px;" class="ffont">
    <img src="<?= base_url('assets/images/next_email.png') ?>" style="width: 150px;" >
    
    <div style="font-size: 20px; margin-top: 25px; margin-bottom: 10px;">
        Olá <?= $nome ?>,
    </div>
    
    <div style="font-size: 14px; margin-bottom: 25px;">
        Você recebeu um ingresso para o evento:<br><br>
        <span style="font-weight: bold; text-transform: uppercase; font-size: 20px;"><?= $evento ?></span>
    </div>
    
    <center>
        <a href="<?= $link ?>">
            <img src="<?= base_url('assets/images/ingresso_nomeado1.png') ?>" style="width: 250px;">
        </a>
    </center>

    <div style="margin-left: auto; margin-right: auto; font-size: 12px; margin-top: 30px; color: #000; background: #e0e2e5; padding: 10px;">
        Ingresso intermediado por Next Ingresso - <a href="https://portal.nextingresso.com.br/register" target="_blank" style="font-weight: bold; color: #096e97;">Nunca foi tão fácil vender ingressos.</a>
        <br>
        Todos os direitos reservados ao Grupo HTI.
    </div>
    
</div>

