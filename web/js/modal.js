$(function(){
    $('.modalList').click(function(){
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
    })
});

$(function(){
    $('.modalUser').click(function(){
        $('#mU').modal('show')
            .find('#modalUserContent')
            .load($(this).attr('value'));
    })
});