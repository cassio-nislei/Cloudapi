function somenteNumeros(e) {
    var charCode = e.charCode ? e.charCode : e.keyCode;
    // charCode 8 = backspace   
    // charCode 9 = tab
    if (charCode != 8 && charCode != 9) {
        // charCode 48 equivale a 0   
        // charCode 57 equivale a 9
        if (charCode < 48 || charCode > 57) {
            return false;
        }
    }
}

function somenteNumerosPonto(e) {
    var charCode = e.charCode ? e.charCode : e.keyCode;
    // charCode 8 = backspace   
    // charCode 9 = tab
    // . = 46
    // , = 44
    if (charCode != 8 && charCode != 9 && charCode != 46) {
        // charCode 48 equivale a 0   
        // charCode 57 equivale a 9
        if (charCode < 48 || charCode > 57) {
            return false;
        }
    }
}

function somenteLetraMaiuscula(e) {
    var tecla = (window.event) ? event.keyCode : e.which;
    if ((tecla >= 65) && (tecla <= 90)) return true;
    else {
        if ((tecla == 8) || (tecla == 0) || (tecla == 32)) return true;
        else return false;
    }
}

function somenteLetraMinuscula(e) {
    var tecla = (window.event) ? event.keyCode : e.which;
    if ((tecla >= 97) && (tecla <= 122)) return true;
    else {
        if ((tecla == 8) || (tecla == 0) || (tecla == 32)) return true;
        else return false;
    }
}

function somenteLetras(e) {
    var tecla = (window.event) ? event.keyCode : e.which;
    if ( somenteLetraMaiuscula(e) || somenteLetraMinuscula(e) ) return true;
    else {
        if ((tecla == 8) || (tecla == 0) || (tecla == 32)) return true;
        else return false;
    }
}

function somenteLetrasENumeros(e) {
    var tecla = (window.event) ? event.keyCode : e.which;
    if (somenteLetraMaiuscula(e) || sSomenteLetraMinuscula(e) || somenteNumero(e)) return true;
    else {
        if ((tecla == 8) || (tecla == 0) || (tecla == 32)) return true;
        else return false;
    }
}

function somenteNumerosVirgula(e) {    
    var charCode = e.charCode ? e.charCode : e.keyCode;
    // charCode 8 = backspace   
    // charCode 9 = tab
    // . = 46
    // , = 44    
    if (charCode != 8 && charCode != 9 && charCode != 44) {
        // charCode 48 equivale a 0   
        // charCode 57 equivale a 9
        if (charCode < 48 || charCode > 57) {
            return false;
        }
    }
}

function somenteHoras(e) {    
    var charCode = e.charCode ? e.charCode : e.keyCode;
    // charCode 8 = backspace   
    // charCode 9 = tab
    // : = 58 
    if (charCode != 8 && charCode != 9 && charCode != 58) {
        // charCode 48 equivale a 0   
        // charCode 57 equivale a 9
        if (charCode < 48 || charCode > 57) {
            return false;
        }
    }
}

function disableButton(id, text = '') {
    document.getElementById(id).innerHTML = text !== '' ? text : 'Aguarde...';
    document.getElementById(id).disabled = true;     
}
        
function enableButton(id, text = '') {
    document.getElementById(id).innerHTML = text !== '' ? text : 'Salvar';
    document.getElementById(id).disabled = false;     
}


