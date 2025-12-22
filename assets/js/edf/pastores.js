var mxPastores = {
    data: {
        pastor: {},
        pastores: [],
        loading_pastores: false,
        base_url: 'https://fgestor.is5.com.br/',
    },
    methods: {
        addPastor() {
            this.pastor = {};
            $("#pastorModal").modal('show');
        },

        editarPastor(pastor) {
            this.pastor =  this.clonar(pastor);
            $("#pastorModal").modal('show');
        },

        getPastores() {
            if (parseInt(this.id) === 0) {
                return false;
            }

            this.loading_pastores = true;                  

            $.get(this.base_url+'Pastores/getAll', {
                diario_id: this.id,
            })
            .done(result => {                       
                this.loading_pastores = false;  
                let obj = JSON.parse(result);
                if (obj) {
                    this.pastores = [];
                    if (obj.status) {
                        this.pastores = obj.data;
                    } 
                } else {
                    this.loading_pastores = false;  
                    alert(result);
                }
            })
            .fail(data => {
                this.loading_pastores = false;
                alert(JSON.stringify(data));
            });
        },

        salvarPastor() {                    
            this.pastor.diario_id = this.id;
            
            this.loading_pastores = true;

            $.post(this.base_url+'Pastores/salvar', {                     
                registro: this.pastor 
            })
            .done(result => {  
                this.loading_pastores = false;
                let obj = JSON.parse(result);
                if (obj) {                          
                    if (obj.status) {
                        $("#pastorModal").modal('hide');
                        this.getPastores();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_pastores = false;
                alert(JSON.stringify(err));
            });                
        }, 

        excluirPastor(pastor) { 
            if (!confirm('Deseja realmente excluir este registro agora?')) {
                return false;
            }
            
            this.loading_pastores = true;
                    
            $.post(this.base_url+'Pastores/excluir', {                     
                id: pastor.id
            })
            .done(result => {  
                console.log(result);
                let obj = JSON.parse(result);
                if (obj) {
                    if (obj.status) {                            
                        this.getPastores();
                    } else {
                        alert(obj.msg);
                    } 
                } else {
                    alert(result);
                }
            })
            .fail(err => {
                this.loading_pastores = false;
                alert(JSON.stringify(err));
            });                
        },   
    },
}