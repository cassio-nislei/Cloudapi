//funcoes para validar campos double (monetario) corretamente
//baixar o pacote Globalize por NuGet
$.validator.methods.number = function (value, element) {
    return this.optional(element) ||
    !isNaN(Globalize.parseFloat(value));
}
$(document).ready(function () {
    Globalize.culture('pt-BR');
});

jQuery.extend(jQuery.validator.methods, {
    range: function (value, element, param) {
        //Use the Globalization plugin to parse the value
        var val = $.global.parseFloat(value);
        return this.optional(element) || (
        val >= param[0] && val <= param[1]);
    }
});