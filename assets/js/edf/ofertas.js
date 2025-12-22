var mxOfertas = {
    data: {
        oferta: {},
        ofertas: [],
        loading_ofertas: false,
        total_ofertas: 0.00,
        ofertas_fp: {},
        base_url: 'https://fgestor.is5.com.br/'
    },
    methods: {
        addOferta() {
            this.oferta = {};
            $("#oferta_total").val('');
            $("#ofertaModal").modal('show');
        },

        editarOferta(oferta) {
            this.oferta =  this.clonar(oferta);
            $("#oferta_total").val( this.toCurrency(this.oferta.total, false) );
            $("#ofertaModal").modal('show');
        },

        getOfertas() {
            if (parseInt(this.id) === 0) {
                return false;
            }

            this.loading_ofertas = true;   
            this.total_ofertas = 0.00;
            this.ofertas_fp = {};

            $.get(this.base_url+'Ofertas/getAll', {
                diario_id: this.id,
            })
            .done(result => { 
                let obj = JSON.parse(result);
                if (obj) {
                    this.ofertas = [];
                    if (obj.status) {
                        this.ofertas = obj.data;
                        this.total_ofertas = obj.total;
                        this.ofertas_fp = obj.total_fp;                        
                    } 
                } else {                    
                    alert(result);
                }
            })
            .fail(data => {                
                alert(JSON.stringify(data));
            })
            .always(() => {
                this.loading_ofertas = false;  
                this.getTotais();
            });
        },

        salvarOferta() {                    
            this.oferta.diario_id = this.id;
            this.oferta.total = $("#oferta_total").val();
            
            this.loading_ofertas = true;
                    
            $.post(this.base_url+'Ofertas/salvar', {                     
                registro: this.oferta 
            })
            .done(result => {  
                this.loading_ofertas = false;
                let obj = JSON.parse(result);
                if (obj) {                          
                    if (obj.status) {
                        $("#ofertaModal").modal('hide');
                        this.getOfertas();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_ofertas = false;
                alert(JSON.stringify(err));
            });                
        }, 

        excluirOferta(oferta) { 
            if (!confirm('Deseja realmente excluir este registro agora?')) {
                return false;
            }
            this.loading_ofertas = true;
            $.post(this.base_url+'Ofertas/excluir', {                     
                id: oferta.id
            })
            .done(result => {  
                this.loading_ofertas = false;
                let obj = JSON.parse(result);
                if (obj) {
                    if (obj.status) {                            
                        this.getOfertas();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_ofertas = false;
                alert(JSON.stringify(err));
            });                
        },       
    },
}