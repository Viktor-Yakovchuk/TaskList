jQuery(function($){
    var $modal = $('#editor-modal'),
        $editor = $('#editor'),
        $editorTitle = $('#editor-title'),
        ft = null,
        $authModal = $('#auth-modal'),
        $authEditor = $('#auth'),
        isAdmin = false;

    $.get('backend.php?controller=CheckLoginController').done(function(data) {
        isAdmin = data.authorized;
        console.log('admin? ' + isAdmin);

        if (isAdmin)
            $('#auth-link').hide();

        ft = FooTable.init('#editing-example', {
            columns: $.get('columns.json'),
            rows: $.get('backend.php'),
            paging: {
                size: 3
            },
            editing: {
                enabled: true,
                alwaysShow: true,
                allowDelete: false,
                addRow: function() {
                    $modal.removeData('row');
                    $editor[0].reset();
                    $editor.find('#status').parent().parent().hide();
                    //$editor.find('#status').parent().parent().remove(); //FIXME
                    $editorTitle.text('Add a new task');
                    $modal.modal('show');
                },
                addText: 'Add Task',
                allowEdit: isAdmin,
                editRow: function(row) {
                    var values = row.val();
                    $editor.find('#id').val(values.id);
                    //$editor.find('#id').hide();
                    $editor.find('#username').val(values.username);
                    $editor.find('#username').parent().parent().hide();
                    $editor.find('#email').val(values.email);
                    $editor.find('#email').parent().parent().hide();
                    $editor.find('#text').val(values.text);
                    $editor.find('#status').get(0).checked = (values.status == "1") ? true : false;

                    $editor.find('#picture').parent().parent().hide();
                    $editor.find('#picture').get(0).required = false;

                    $modal.data('row', row);
                    $editorTitle.text('Edit task #' + values.id);
                    $modal.modal('show');
                }/*,
                deleteRow: function(row) {
                    if (confirm('Are you sure you want to delete the row?')) {
                        row.delete();
                    }
                }*/
            }
        });
    });

    window.statusFormatter = function(status) {
        return (status == "1") ? '<font color="green">Completed</font>' : 'Not completed yet';
    }

    window.pictureFormatter = function(id) {
        return '<img src="uploads/' + id + '.jpg" />';
    }

    $('#auth-link').on('click', function(e) {
        $authModal.modal('show');
    });

    $authEditor.on('submit', function(e) {
        e.preventDefault();

        $.post('backend.php?controller=AuthController',
            {
                login: $authEditor.find('#login').val(),
                password: $authEditor.find('#password').val()
            },
            function(response) {
                if (response.status != 'ok')
                    alert(response.details);

                console.log(response);

                if (response.status == 'ok') {
                    $authModal.modal('hide');
                    document.location.reload();
                }
        });
    });

    $modal.on('hidden.bs.modal', function(e) {
        $editor.find('div.form-group').show();
        $editor.find('#picture').get(0).required = true;
    });

    $editor.on('submit', function(e) {
        if (this.checkValidity && !this.checkValidity()) return;
        e.preventDefault();
        var row = $modal.data('row'),
            values = {
                id: $editor.find('#id').val(),
                username: $editor.find('#username').val(),
                email: $editor.find('#email').val(),
                text: $editor.find('#text').val(),
                status: $editor.find('#status').get(0).checked
            },
            onSaved = function() {
                $modal.modal('hide');
            },
            editAjaxHandler = function(response) {
                if (response.status != 'ok')
                    alert(response.details);

                console.log(response);
                row.val(values);

                onSaved();
            },
            addAjaxHandler = function(response) {
                if (response.status != 'ok')
                    alert(response.details);

                console.log(response);
                //values.id = response.id;
                //ft.rows.add(values);

                //onSaved();
            };

        var url = '';
        var handler = null;

        var formData = new FormData();

        for (var k in values)
            formData.append(k, values[k]);

        if (row instanceof FooTable.Row) {
            url = 'backend.php?controller=EditTaskController';
            handler = editAjaxHandler;

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: handler
            }); 
        } else {
            //console.log($editor.find('#picture'));
            formData.append('picture', $editor.find('#picture').get(0).files[0]);
            //console.log(formData);

            url = 'backend.php?controller=AddTaskController';
            handler = function() {}; //addAjaxHandler;

            values.id = '';
            var row = new FooTable.Row(ft, ft.columns.array, values);
            ft.rows.add(row);
            onSaved();

            var restoreInterface = function() {
                $('.btn.tasklist-save').remove();
                $('.btn.tasklist-discard').remove();
                $('.btn.btn-primary.footable-add').show();
            };

            $('<button type="button" class="btn btn-success tasklist-save">Save changes</button>&nbsp;<button type="button" class="btn btn-danger tasklist-discard">Discard changes</button>').insertBefore('.btn.btn-primary.footable-add');
            $('.btn.btn-primary.footable-add').hide();

            $('.tasklist-save').on('click', function() {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            restoreInterface();

                            if (response.status != 'ok') {
                                alert(response.details);
                                row.delete();
                                return;
                            }

                            console.log(response);
                            values.id = response.id;
                            //row.update();
                            ft.rows.update(row, values);

                            //onSaved();
                        }
                    });
            });

            $('.tasklist-discard').on('click', function() {
                row.delete();
                restoreInterface();
            });
        }       
    });
}); 
