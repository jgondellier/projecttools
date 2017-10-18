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
                $('.js-datepicker').datepicker({format: 'dd/mm/yyyy',language:'fr'});
                $(".form_datetime").datetimepicker({format: 'dd/mm/yyyy hh:ii',language:'fr'});
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

        $("#submitProject").find(".ajaxScrollLoader").show();
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
            .done(function (data) {
                if (typeof data.message !== 'undefined') {
                    var dataTable = $('#dataTable').DataTable();
                    dataTable.ajax.reload();
                    if(data.type === 'edit'){
                        $('#addModal').modal('hide');
                        $("#submitProject").find(".ajaxScrollLoader").hide();
                    }else if(data.type === 'delete'){
                        $('#addModal').modal('hide');
                    }else{
                        $('#addModal').modal('hide');
                        $('#projectForm')[0].reset();
                        $("#submitProject").find(".ajaxScrollLoader").hide();
                    }
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                $("#submitProject").find(".ajaxScrollLoader").hide();
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