var mxDiaconos = {
    data: {
        diacono: {},
        diaconos: [],
        loading_diaconos: false,
        base_url: 'https://fgestor.is5.com.br/'
    },
    methods: {
        addDiacono() {
            this.diacono = {};
            $("#diaconoModal").modal('show');
        },

        editarDiacono(diacono) {
            this.diacono =  this.clonar(diacono);
            $("#diaconoModal").modal('show');
        },

        getDiaconos() {
            if (parseInt(this.id) === 0) {
                return false;
            }

            this.loading_diaconos = true;                  

            $.get(this.base_url+'Diaconos/getAll', {
                diario_id: this.id,
            })
            .done(result => {                       
                this.loading_diaconos = false;  
                let obj = JSON.parse(result);
                if (obj) {
                    this.diaconos = [];
                    if (obj.status) {
                        this.diaconos = obj.data;
                    } 
                } else {
                    this.loading_diaconos = false;  
                    alert(result);
                }
            })
            .fail(data => {
                this.loading_diaconos = false;
                alert(JSON.stringify(data));
            });
        },

        salvarDiacono() {                    
            this.diacono.diario_id = this.id;
            this.loading_diaconos = true;

            $.post(this.base_url+'Diaconos/salvar', {                     
                registro: this.diacono 
            })
            .done(result => {  
                this.loading_diaconos = false;
                let obj = JSON.parse(result);
                if (obj) {                          
                    if (obj.status) {
                        $("#diaconoModal").modal('hide');
                        this.getDiaconos();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_diaconos = false;
                alert(JSON.stringify(err));
            });                
        }, 

        excluirDiacono(diacono) { 
            if (!confirm('Deseja realmente excluir este registro agora?')) {
                return false;
            }
            this.loading_diaconos = true;
            $.post(this.base_url+'Diaconos/excluir', {                     
                id: diacono.id
            })
            .done(result => {  
                this.loading_diaconos = false;
                let obj = JSON.parse(result);
                if (obj) {
                    if (obj.status) {                            
                        this.getDiaconos();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_diaconos = false;
                alert(JSON.stringify(err));
            });                
        },       
    },
}