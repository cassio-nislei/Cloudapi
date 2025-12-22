var mxEspeciais = {
    data: {
        especial: {},
        especiais: [],
        loading_especiais: false,
        total_especiais: 0.00,
        especiais_fp: {},
        base_url: 'https://fgestor.is5.com.br/',
    },
    methods: {
        addEspecial() {
            this.especial = {};
            $("#especial_total").val('');
            $("#especialModal").modal('show');
        },

        editarEspecial(especial) {
            this.especial =  this.clonar(especial);
            $("#especial_total").val( this.toCurrency(this.especial.total, false) );
            $("#especialModal").modal('show');
        },

        getEspeciais() {            
            if (parseInt(this.id) === 0) {
                return false;
            }

            this.loading_especiais = true;   
            this.total_especiais = 0.00;
            this.especiais_fp = {};

            $.get(this.base_url+'Especiais/getAll', {
                diario_id: this.id,
            })
            .done(result => { 
                let obj = JSON.parse(result);
                if (obj) {
                    this.especiais = [];
                    if (obj.status) {
                        this.especiais = obj.data;
                        this.total_especiais = obj.total;
                        this.especiais_fp = obj.total_fp;                        
                    } 
                } else {                    
                    alert(result);
                }
            })
            .fail(data => {                
                alert(JSON.stringify(data));
            })
            .always(() => {
                this.loading_especiais = false;              
                this.getTotais();
            });
        },

        salvarEspecial() {                    
            this.especial.diario_id = this.id;
            this.especial.total = $("#especial_total").val();
            
            this.loading_especiais = true;

            $.post(this.base_url+'Especiais/salvar', {                     
                registro: this.especial 
            })
            .done(result => {  
                this.loading_especiais = false;
                let obj = JSON.parse(result);
                if (obj) {                          
                    if (obj.status) {
                        $("#especialModal").modal('hide');
                        this.getEspeciais();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_especiais = false;
                alert(JSON.stringify(err));
            });                
        }, 

        excluirEspecial(especial) { 
            if (!confirm('Deseja realmente excluir este registro agora?')) {
                return false;
            }
            this.loading_especiais = true;
            $.post(this.base_url+'Especiais/excluir', {                     
                id: especial.id
            })
            .done(result => {  
                this.loading_especiais = false;
                let obj = JSON.parse(result);
                if (obj) {
                    if (obj.status) {                            
                        this.getEspeciais();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_especiais = false;
                alert(JSON.stringify(err));
            });                
        },       
    },
}