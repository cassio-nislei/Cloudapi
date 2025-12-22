var mxVisitantes = {
    data: {
        visitante: {},
        visitantes: [],
        loading_visitantes: false,
        base_url: 'https://fgestor.is5.com.br/',
    },
    methods: {
        addVisitante() {
            this.visitante = {};
            $("#visitanteModal").modal('show');
        },

        editarVisitante(visitante) {
            this.visitante =  this.clonar(visitante);
            $("#visitanteModal").modal('show');
        },

        getVisitantes() {
            if (parseInt(this.id) === 0) {
                return false;
            }

            this.loading_visitantes = true;                  

            $.get(this.base_url+'Visitantes/getAll', {
                diario_id: this.id,
            })
            .done(result => { 
                console.log(result);
                this.loading_visitantes = false;  
                let obj = JSON.parse(result);
                if (obj) {
                    this.visitantes = [];
                    if (obj.status) {
                        this.visitantes = obj.data;
                    } 
                } else {
                    this.loading_visitantes = false;  
                    alert(result);
                }
            })
            .fail(data => {
                this.loading_visitantes = false;
                alert(JSON.stringify(data));
            });
        },

        salvarVisitante() {                    
            this.visitante.diario_id = this.id;
            
            this.loading_visitantes = true;

            $.post(this.base_url+'Visitantes/salvar', {                     
                registro: this.visitante 
            })
            .done(result => {  
                this.loading_visitantes = false;
                let obj = JSON.parse(result);
                if (obj) {                          
                    if (obj.status) {
                        $("#visitanteModal").modal('hide');
                        this.getVisitantes();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_visitantes = false;
                alert(JSON.stringify(err));
            });                
        }, 

        excluirVisitante(visitante) { 
            if (!confirm('Deseja realmente excluir este registro agora?')) {
                return false;
            }
            this.loading_visitantes = true;
            $.post(this.base_url+'Visitantes/excluir', {                     
                id: visitante.id
            })
            .done(result => {  
                this.loading_visitantes = false;
                let obj = JSON.parse(result);
                if (obj) {
                    if (obj.status) {                            
                        this.getVisitantes();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_visitantes = false;
                alert(JSON.stringify(err));
            });                
        },       
    },
}