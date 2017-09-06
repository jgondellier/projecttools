function createProjectForm(url,type){
    $(".slideInLeft").removeClass("slideInLeft");
    $.ajax({
        type: type,
        url: url
    })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                $('.modal-content').html(data.form);
                submitProjectForm();
                $('#addProjectModal').modal('show');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
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
                    var projectTable = $('#projectTable').DataTable();
                    if(data.type === 'edit'){
                        projectTable.ajax.reload();
                        $('#addProjectModal').modal('hide');
                        $(".ajaxScrollLoader").hide()
                    }else if(data.type === 'delete'){
                        projectTable.ajax.reload();
                        $('#addProjectModal').modal('hide');
                    }else{
                        projectTable.ajax.reload();
                        $('#addProjectModal').modal('hide');
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