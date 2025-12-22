var mxApresentacoes = {
    data: {
        apresentacao: {},
        apresentacoes: [],
        loading_apresentacoes: false,
        base_url: 'https://admcloud.papion.com.br/',
    },
    methods: {
        addApresentacao() {
            this.apresentacao = {};
            $("#apresentacaoModal").modal('show');
        },

        editarApresentacao(r) {
            this.apresentacao =  this.clonar(r);
            $("#apresentacaoModal").modal('show');
        },

        getApresentacoes() {
            if (parseInt(this.id) === 0) {
                return false;
            }

            this.loading_apresentacoes = true;                  

            $.get(this.base_url+'Apresentacoes/getAll', {
                diario_id: this.id,
            })
            .done(result => {                       
                this.loading_apresentacoes = false;  
                let obj = JSON.parse(result);
                if (obj) {
                    this.apresentacoes = [];
                    if (obj.status) {
                        this.apresentacoes = obj.data;
                    } 
                } else {
                    this.loading_apresentacoes = false;  
                    alert(result);
                }
            })
            .fail(data => {
                this.loading_apresentacoes = false;  
                alert(JSON.stringify(data));
            });
        },

        salvarApresentacao() {                    
            this.apresentacao.diario_id = this.id;
            this.loading_apresentacoes = true;

            $.post(this.base_url+'Apresentacoes/salvar', {                     
                registro: this.apresentacao 
            })
            .done(result => {  
                this.loading_apresentacoes = false;
                let obj = JSON.parse(result);
                if (obj) {                          
                    if (obj.status) {
                        $("#apresentacaoModal").modal('hide');
                        this.getApresentacoes();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_apresentacoes = false;
                alert(JSON.stringify(err));
            });                
        }, 

        excluirApresentacao(r) { 
            if (!confirm('Deseja realmente excluir este registro agora?')) {
                return false;
            }
            this.loading_apresentacoes = true;
            $.post(this.base_url+'Apresentacoes/excluir', {                     
                id: r.id
            })
            .done(result => {  
                this.loading_apresentacoes = false;
                let obj = JSON.parse(result);
                if (obj) {
                    if (obj.status) {                            
                        this.getApresentacoes();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_apresentacoes = false;
                alert(JSON.stringify(err));
            });                
        },
    }
}