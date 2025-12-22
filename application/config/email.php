<?php
$config['smtp_host']   = 'smtp.kinghost.net';
$config['smtp_port']   = '587';
$config['smtp_crypto'] = 'tls'; //tls
$config['smtp_user']   = 'fbx@papion.com.br';
$config['smtp_pass']   = 'dev@P4p10n';
$config['protocol']    = 'smtp';
$config['validate']    = TRUE;
$config['charset']     = 'utf-8';
$config['newline']     = "\r\n";
$config['wordwrap']    = TRUE;
$config['mailtype']    = 'html';
$config['useragent']   = 'papion.com.br';
$config['X-Mailer']    = 'papion.com.br';

//liberar acesso externo no Gmail:
//log na conta, acesse o site:
//https://myaccount.google.com/lesssecureapps
//marcar como ON