$(document).ready(function () {
    /*
    para todos os elementos da classe que vamos chamar de "datePicker",
    será aplicado o "componente" de calendário no formato brasileiro.
    Fonte: http://diretonocodigo.blogspot.com.br/2011/07/helper-para-calendario-com-jquery-ui_24.html?m=1
    */
    $(".datePicker").datepicker({ dateFormat: "dd/mm/yy" });
});

jQuery(function ($) {
    /*
    Fonte: http://digitalbush.com/projects/masked-input-plugin/
    */
    $(".datePicker").mask("99/99/9999");
    $(".telefone").mask("(99)9999-9999");
    $(".celular").mask("(99)99999-9999");
    $(".cep").mask("99999-999");
    $(".cnpj").mask("999.999.999/9999-99");
    $(".cpf").mask("999.999.999-99");
    $(".hora").mask("99:99");
    $(".cartao").mask("9999-9999-9999-9999");
    $(".mesano").mask("99/99");
});