var mxDizimos = {
    data: {
        dizimo: {},
        dizimos: [],
        loading_dizimos: false,
        total_dizimos: 0.00,
        dizimos_fp: [],
        base_url: 'https://fgestor.is5.com.br/'
    },
    methods: {
        addDizimo() {
            this.dizimo = {};
            $("#dizimo_valor").val('');
            $("#dizimoModal").modal('show');
        },

        editarDizimo(dizimo) {
            this.dizimo =  this.clonar(dizimo);
            $("#dizimo_valor").val( this.toCurrency(this.dizimo.valor, false) );
            $("#dizimoModal").modal('show');
        },

        getDizimos() {            
            if (parseInt(this.id) === 0) {
                return false;
            }

            this.loading_dizimos = true;                  
            this.total_dizimos = 0.00;
            
            $.get(this.base_url+'Dizimos/getAll', {
                diario_id: this.id,
            })
            .done(result => {                                 
                let obj = JSON.parse(result);
                if (obj) {
                    this.dizimos = [];
                    if (obj.status) {
                        this.dizimos = obj.data;
                        this.total_dizimos = obj.total;
                        this.dizimos_fp = obj.total_fp;                        
                    }                     
                } else {                    
                    alert(result);
                }
            })
            .fail(data => {                
                alert(JSON.stringify(data));
            })
            .always(() => {
                this.loading_dizimos = false;  
                this.getTotais();
            });
        },

        salvarDizimo() {                    
            this.dizimo.diario_id = this.id;
            this.dizimo.valor = $("#dizimo_valor").val();
            this.loading_dizimos = true;            

            $.post(this.base_url+'Dizimos/salvar', {                     
                registro: this.dizimo 
            })
            .done(result => {  
                this.loading_dizimos = false;
                let obj = JSON.parse(result);
                if (obj) {                          
                    if (obj.status) {
                        $("#dizimoModal").modal('hide');
                        this.getDizimos();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_dizimos = false;
                alert(JSON.stringify(err));
            });                
        }, 

        excluirDizimo(dizimo) { 
            if (!confirm('Deseja realmente excluir este registro agora?')) {
                return false;
            }            
            this.loading_dizimos = true;
            $.post(this.base_url+'Dizimos/excluir', {                     
                id: dizimo.id
            })
            .done(result => {  
                this.loading_dizimos = false;
                let obj = JSON.parse(result);
                if (obj) {
                    if (obj.status) {                            
                        this.getDizimos();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_dizimos = false;
                alert(JSON.stringify(err));
            });                
        },       
    },
}