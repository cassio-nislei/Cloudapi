<script>    
    var myHashId = '<?= getHashId() ?>';    
    var novos_pedidos = [];
    //var last_codigo = '<?= getmenu_get_codigo_ultimo_pedido_pendente() ?>';   
    var last_codigo = '';
    
    //clear historico pedidos
    var adaRef = database.ref('getmenu/pedidos/<?= $this->session->userdata('emit.token') ?>');
    adaRef.remove()
    .then(function() {
      console.log("Remove succeeded.");
    })
    .catch(function(error) {
      console.log("Remove failed: " + error.message);
    });    
       
    function writeUserData(userId, email, token) {
        database.ref('users/' + userId).set({
          email: email,
          token: token,          
        });
    }
    
    //salva token atual
    writeUserData(myHashId, '<?= $this->session->userdata('_email') ?>', '<?= $this->session->userdata('_token_auth') ?>');
    
    //observa alteracoes no campo token da "tabela" users
    var tokenRef = database.ref('users/' + myHashId + '/token');    
    tokenRef.on('value', (snapshot) => {
      const data = snapshot.val();      
      if (data !== '<?= $this->session->userdata('_token_auth') ?>') {
          alert('Atenção!\n\rAlguém conectou em outro dispositivo com esta conta!');
          window.location.href = '<?= base_url('Account/logout') ?>';
      }
    });
    
    var ppAudio = new Audio('<?= base_url('assets/mid/notification.mp3') ?>');
   
    //observa alteracoes no campo token da "tabela" users
    var dcp = document.getElementById('divContPedidos');
    dcp.style.display = 'none';
    
    //var dbRef = database.ref('getmenu/pedidos/<?= $this->session->userdata('emit.token') ?>');
    //var pedidosRef = dbRef.limitToLast(1);    
    var pedidosRef = database.ref('getmenu/pedidos/<?= $this->session->userdata('emit.token') ?>').limitToLast(1);
    pedidosRef.on('value', (snapshot) => {
      const data = snapshot.val(); 
      snapshot.forEach((childSnapshot) => {
        var childKey = childSnapshot.key;
        var childData = childSnapshot.val();  
                
        //if (childData.codigo !== last_codigo) {
            //last_codigo = childData.codigo;            
            novos_pedidos.push(childData.codigo); //last_codigo

            dcp.innerHTML = novos_pedidos.length;
            dcp.style.display = 'block'; 

            ppAudio.play();
        //}
        
        /*
        console.log('childData.codigo = '+childData.codigo+', last_codigo='+last_codigo);
        
        if ((childData.codigo !== '') && (childData.codigo !== undefined) && (childData.codigo !== last_codigo)) {                                                
            last_codigo = childData.codigo;
            novos_pedidos.push(last_codigo);
            
            dcp.innerHTML = novos_pedidos.length;
            dcp.style.display = 'block'; 
            
            ppAudio.play();
        } else {
            dcp.style.display = 'none';            
        } 
        */
      });         
    });
    
    console.log('dados enviados!');
</script>
