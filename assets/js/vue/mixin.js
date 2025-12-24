var mxFunctions = {
  data: {
    base_url: "http://104.234.173.105:7010/",
  },
  methods: {
    toCurrency(v, cifrao = true) {
      v = v || 0.0;
      if (cifrao) {
        return parseFloat(v).toLocaleString("pt-BR", {
          style: "currency",
          currency: "BRL",
        });
      } else {
        return parseFloat(v).toLocaleString("pt-BR", {
          minimumFractionDigits: 2,
        });
      }
      //Intl.NumberFormat('pt-br', {style: 'currency', currency: 'BRL'}).format(t);
    },

    clonar(object) {
      return Object.assign({}, object);
    },

    getFormData(object) {
      let formData = new FormData();
      Object.keys(object).forEach((key) => formData.append(key, object[key]));
      return formData;
    },

    emBranco(obj) {
      return obj === "" || obj === undefined || obj === null;
    },

    getData(datetime) {
      //2021-01-15 10:06:40
      if (!this.emBranco(datetime)) {
        let arr = datetime.split(" ");
        let data = arr[0].split("-");
        return data[2] + "/" + data[1] + "/" + data[0];
      } else {
        return datetime;
      }
    },

    getHora(datetime) {
      //2021-01-15 10:06:40
      if (!this.emBranco(datetime)) {
        let arr = datetime.split(" ");
        return arr[1];
      } else {
        return datetime;
      }
    },

    getPrimeiroDiaMes() {
      let date = new Date();
      let firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
      return this.dateToStr(firstDay);
    },

    getDataAtual() {
      return this.dateToStr(new Date());
    },

    dateToStr(data) {
      let dia = data.getDate();
      let mes = data.getMonth();
      let ano = data.getFullYear();

      mes++;

      return ("00" + dia).slice(-2) + "/" + ("00" + mes).slice(-2) + "/" + ano;
    },

    formataData(data) {
      //2021-01-15
      if (!this.emBranco(data)) {
        if (data.length > 10) {
          arr = data.split(" ");
          return arr[0];
        }
        let arr = data.split("-");
        return arr[2] + "/" + arr[1] + "/" + arr[0];
      } else {
        return data;
      }
    },

    formataDataHora(datetime) {
      //2021-01-15 10:06:40
      if (!this.emBranco(datetime)) {
        let arr = datetime.split(" ");
        let data = arr[0].split("-");
        return data[2] + "/" + data[1] + "/" + data[0] + " " + arr[1];
      } else {
        return datetime;
      }
    },

    gerarcor() {
      var hexadecimais = "0123456789ABCDEF";
      var cor = "#FF"; //FF -> tons laranja, amarelo...

      // Pega um número aleatório no array acima
      for (var i = 0; i < 4; i++) {
        //E concatena à variável cor
        cor += hexadecimais[Math.floor(Math.random() * 16)];
      }
      return cor;
    },

    getRandomIntInclusive(min, max) {
      min = Math.ceil(min);
      max = Math.floor(max);
      return Math.floor(Math.random() * (max - min + 1)) + min;
    },

    getCorPadrao() {
      //retorna alguma das cores padrao do getmenu
      let cores = [
        "#FF4500",
        "#FFD700",
        "#F2E8CF",
        "#B26F2B",
        "#F59A53",
        "#E36437",
        "#FF2E6E",
        "#E9ECEF",
        "#E5F2DF",
        "#096e97",
        "#184cdb",
        "#e30e3c",
        "#0bba43",
      ];
      return cores[this.getRandomIntInclusive(0, cores.length)];
    },

    disableButton(id, text = "") {
      document.getElementById(id).innerHTML = text !== "" ? text : "Aguarde...";
      document.getElementById(id).disabled = true;
    },

    enableButton(id, text = "") {
      document.getElementById(id).innerHTML = text !== "" ? text : "Salvar";
      document.getElementById(id).disabled = false;
    },

    cpf_mask(v) {
      v = v.replace(/\D/g, ""); //Remove tudo o que não é dígito
      v = v.replace(/(\d{3})(\d)/, "$1.$2"); //Coloca um ponto entre o terceiro e o quarto dígitos
      v = v.replace(/(\d{3})(\d)/, "$1.$2"); //Coloca um ponto entre o terceiro e o quarto dígitos
      //de novo (para o segundo bloco de números)
      v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2"); //Coloca um hífen entre o terceiro e o quarto dígitos
      return v;
    },

    cnpj_mask(v) {
      v = v.replace(/\D/g, ""); //Remove tudo o que não é dígito
      v = v.replace(/^(\d{2})(\d)/, "$1.$2"); //Coloca ponto entre o segundo e o terceiro dígitos
      v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3"); //Coloca ponto entre o quinto e o sexto dígitos
      v = v.replace(/\.(\d{3})(\d)/, ".$1/$2"); //Coloca uma barra entre o oitavo e o nono dígitos
      v = v.replace(/(\d{4})(\d)/, "$1-$2"); //Coloca um hífen depois do bloco de quatro dígitos
      return v;
    },

    formataCgc(v) {
      v = v.replace(/\D/g, ""); //Remove tudo o que não é dígito
      if (v) {
        if (v.length == 11) {
          //CPF
          return this.cpf_mask(v);
        } else if (v.length == 14) {
          //CNPJ
          return this.cnpj_mask(v);
        } else {
          return v;
        }
      }
    },

    formataFone(fone) {
      if (!fone) {
        return "";
      }
      let f = fone.toString();
      f = f.replace(/\D/g, ""); //Remove tudo o que não é dígito
      f = f.replace(/^(\d{2})(\d)/g, "($1)$2"); //Coloca parênteses em volta dos dois primeiros dígitos
      f = f.replace(/(\d)(\d{4})$/, "$1-$2"); //Coloca hífen entre o quarto e o quinto dígitos
      return f;
    },
  },
};
