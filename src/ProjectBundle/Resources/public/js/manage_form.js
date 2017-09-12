function createForm(url,type){
    $.ajax({
        type: type,
        url: url
    })
        .done(function (data) {
            $(".slideInLeft").removeClass("slideInLeft");
            $(".flash").removeClass("flash");
            $(".rotate").removeClass("rotate");
            $(".ajaxScrollLoader").hide();
            if (typeof data.message !== 'undefined') {
                $('.modal-content').html(data.form);
                submitProjectForm();
                $('#addModal').modal('show');
                $('.js-datepicker').datepicker();
                $(".form_datetime").datetimepicker({format: 'dd/mm/yyyy hh:ii'});
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            $(".ajaxScrollLoader").hide();
            $(".slideInLeft").removeClass("slideInLeft");
            $(".flash").removeClass("flash");
            if (typeof jqXHR.responseJSON !== 'undefined') {
                if (jqXHR.responseJSON.hasOwnProperty('form')) {
                    $('#form_body').html(jqXHR.responseJSON.form);
                }
                $('.form_error').html(jqXHR.responseJSON.message);
            } else {
                alert(errorThrown);
            }
        });
}

function submitProjectForm(){
    $('#projectForm').submit(function (e) {
        e.preventDefault();

        $('.ajaxScrollLoader').show();
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
            .done(function (data) {
                if (typeof data.message !== 'undefined') {
                    var dataTable = $('#dataTable').DataTable();
                    if(data.type === 'edit'){
                        dataTable.ajax.reload();
                        $('#addModal').modal('hide');
                        $(".ajaxScrollLoader").hide()
                    }else if(data.type === 'delete'){
                        dataTable.ajax.reload();
                        $('#addModal').modal('hide');
                    }else{
                        dataTable.ajax.reload();
                        $('#addModal').modal('hide');
                        $('#projectForm')[0].reset();
                        $(".ajaxScrollLoader").hide()
                    }
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                $(".ajaxScrollLoader").hide()
                if (typeof jqXHR.responseJSON !== 'undefined') {
                    if (jqXHR.responseJSON.hasOwnProperty('form')) {
                        $('#form_body').html(jqXHR.responseJSON.form);
                    }

                    $('.form_error').html(jqXHR.responseJSON.message);

                } else {
                    alert(errorThrown);
                }

            });
    });
}