var mxMissoes = {
    data: {
        missao: {},
        missoes: [],
        loading_missoes: false,
        total_missoes: 0.00,
        missoes_fp: {},
        base_url: 'https://fgestor.is5.com.br/',
    },
    methods: {
        addMissao() {
            this.missao = {};
            $("#missao_total").val('');
            $("#missaoModal").modal('show');
        },

        editarMissao(missao) {
            this.missao =  this.clonar(missao);
            $("#missao_total").val( this.toCurrency(this.missao.total, false) );
            $("#missaoModal").modal('show');
        },

        getMissoes() {            
            if (parseInt(this.id) === 0) {
                return false;
            }

            this.loading_missoes = true;  
            this.total_missoes = 0.00;
            this.missoes_fp = {};

            $.get(this.base_url+'Missoes/getAll', {
                diario_id: this.id,
            })
            .done(result => { 
                let obj = JSON.parse(result);
                if (obj) {
                    this.missoes = [];
                    if (obj.status) {
                        this.missoes = obj.data;
                        this.total_missoes = obj.total;
                        this.missoes_fp = obj.total_fp;                        
                    } 
                } else {                    
                    alert(result);
                }
            })
            .fail(data => {                
                alert(JSON.stringify(data));
            })
            .always(() => {
                this.loading_missoes = false;
                this.getTotais();
            });
        },

        salvarMissao() {                    
            this.missao.diario_id = this.id;
            this.missao.total = $("#missao_total").val();
            
            this.loading_missoes = true;

            $.post(this.base_url+'Missoes/salvar', {                     
                registro: this.missao 
            })
            .done(result => {  
                this.loading_missoes = false;
                let obj = JSON.parse(result);
                if (obj) {                          
                    if (obj.status) {
                        $("#missaoModal").modal('hide');
                        this.getMissoes();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_missoes = false;
                alert(JSON.stringify(err));
            });                
        }, 

        excluirMissao(missao) { 
            if (!confirm('Deseja realmente excluir este registro agora?')) {
                return false;
            }
            this.loading_missoes = true;
            $.post(this.base_url+'Missoes/excluir', {                     
                id: missao.id
            })
            .done(result => {  
                this.loading_missoes = false;
                let obj = JSON.parse(result);
                if (obj) {
                    if (obj.status) {                            
                        this.getMissoes();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_missoes = false;
                alert(JSON.stringify(err));
            });                
        },       
    },
}