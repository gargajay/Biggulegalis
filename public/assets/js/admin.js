let adminJs;
var datatablePointer = [];

$(document).ready(function () {
    adminJs = new AdminJs();
    adminJs.init();
});


function AdminJs() {
    let that = this;
    that.init = function () {
        // hide loader
        $('.web-loader:not(.table-loader)').addClass('hide');

        //setup x-csrf token in every ajax request
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-TIMEZONE': Intl.DateTimeFormat().resolvedOptions().timeZone // add the time zone header here
            }
        });

        that.datatable();

        // show and hide password
        $(document).ready(function () {
            $(document).on('click', '.password-input', function () {
                var passwordInput = $(this).closest(".input-group").find('input');
                var icon = $(this).find('i');

                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                }
            });
        });

        $(document).on('keypress', 'form input', function (e) {
            return e.which !== 13;
        });

        //submit form
        $(document).on('click', '.submitBtn', function () {
            let form = $(this).closest('form');
            try {
                $.ajax(
                    {
                        url: form.attr('action'),
                        type: "POST",
                        data: new FormData(form[0]),
                        contentType: false,
                        cache: false,
                        processData: false,
                        beforeSend: function () {
                            $('.web-loader:not(.table-loader)').removeClass('hide');
                        },
                        complete: function () {
                            $('.web-loader:not(.table-loader)').addClass('hide');
                        },
                        success: function (data) {
                            if (data.success === true) {
                                // Check if form has data-form-reset attribute
                                if (form.attr('data-form-reset') === 'true') {
                                    // Reset the form
                                    form[0].reset();
                                }
                                // Check if form has data-form-model-hide attribute
                                if (form.attr('data-form-model-hide') === 'true') {
                                    // Hide the modal
                                    form.closest('.modal').modal('hide')
                                }

                                that.notify(data.message);
                                for (tableId in datatablePointer) {
                                    datatablePointer[tableId].ajax.reload(null, false);
                                }
                            }
                            if (data.success === false) {
                                that.notify('', data.message);
                            }
                        },
                    });
            } catch (error) {
                that.notify('', error);
            }

        });

        //open html in modal using href url
        $(document).on('click', '.modal-link', function (e) {
            try {
                e.preventDefault();
                const $this = $(this);
                const url = $this.is('a') ? $this.attr('href') : $this.data('url');

                if (!url) {
                    that.notify('', 'Modal url not found.');
                    return;
                }

                $('.web-loader:not(.table-loader)').removeClass('hide');
                $.get(url, { _: $.now() })
                    .done(function (data, status) {
                        if (data.success === false) {
                            that.notify('', data.message);
                        } else {
                            $('.web-loader:not(.table-loader)').addClass('hide');
                            $('#commonModal').html(data).modal();
                        }
                    })
                    .fail(function (jqXHR, status, error) {
                        if (jqXHR.responseJSON.message) {
                            that.notify('', jqXHR.responseJSON.message);
                        }
                    });
            } catch (error) {
                that.notify('', error);
            }
        });

        //open html in modal using href url
        $(document).on('click', '.status-link', function (e) {
            try {
                e.preventDefault();
                const $this = $(this);
                const url = $this.is('a') ? $this.attr('href') : $this.data('url');

                if (!url) {
                    that.notify('', 'Modal url not found.');
                    return;
                }

                $('.web-loader:not(.table-loader)').removeClass('hide');
                $.get(url, { _: $.now() })
                    .done(function (data, status) {
                        if (data.success === true) {
                            that.notify(data.message);
                            for (tableId in datatablePointer) {
                                datatablePointer[tableId].ajax.reload(null, false);
                            }
                        }
                        if (data.success === false) {
                            that.notify('', data.message);
                        }
                    })
                    .fail(function (jqXHR, status, error) {
                        if (jqXHR.responseJSON.message) {
                            that.notify('', jqXHR.responseJSON.message);
                        }
                    });
            } catch (error) {
                that.notify('', error);
            }
        });



        // show upload file name
        $(document).on('change', '.upload-file-input', function () {
            let filename = $(this).val().split('\\').pop();
            $(this).parent().find('.upload-file-label').html(filename);
        });


        //delete row
        $(document).on('click', '.delete-link', function (e) {
            try {
                e.preventDefault();
                that.confirmMsg(this);
            } catch (error) {
                that.notify('', error);
            }

        });


        $(document).on('click', '.app-icon-link', function () {
            var url = $(this).data("url");
            window.open(url, "_blank");
        });





    }

    that.deleteRow = function (obj) {
        const url = $(obj).is('a') ? $(obj).attr('href') : $(obj).data('url');

        if (!url) {
            that.notify('', 'Modal url not found.');
            return;
        }

        $('.web-loader:not(.table-loader)').removeClass('hide');
        $.get(url, { _: $.now() })
            .done(function (data, status) {
                if (data.success === true) {
                    that.notify(data.message);
                    for (tableId in datatablePointer) {
                        datatablePointer[tableId].ajax.reload(null, false);
                    }
                }
                if (data.success === false) {
                    that.notify('', data.message);
                }
            })
            .fail(function (jqXHR, status, error) {
                if (jqXHR.responseJSON.message) {
                    that.notify('', jqXHR.responseJSON.message);
                }
            });
    }



    that.confirmMsg = function (obj) {
        swal({
            title: "Are you sure?",
            text: "You want to delete record?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                that.deleteRow(obj);
            }
        });
    }

    that.notify = function (successMsg = '', errorMsg = '') {
        if (successMsg || errorMsg) {
            $('.web-loader:not(.table-loader)').addClass('hide');
            $.notify(
                {
                    title: (successMsg) ? 'Success : ' : 'Error : ',
                    message: (successMsg) ? successMsg : errorMsg,
                    icon: (successMsg) ? 'fa fa-check' : 'fa fa-close'
                },
                {
                    type: (successMsg) ? 'success' : 'danger',
                    placement:
                    {
                        from: "top",
                        align: "right"
                    },

                    delay: 10000,
                    mouse_over: "pause",
                    z_index: 3000,
                    animate: {
                        enter: 'animated fadeInDown',
                        exit: 'animated fadeOutUp'
                    },
                    newest_on_top: true,
                });
        }
    }


    that.datatable = function () {
        // Get all tables with class "data-table-class"
        const tables = document.querySelectorAll("table.data-table-class");

        // Loop through each table
        tables.forEach(table => {
            // Get table ID and URL from data attribute
            const tableId = table.id;
            const tableUrl = table.dataset.url;
            $('.web-loader:not(.table-loader)').removeClass('hide');
            // Send POST request to retrieve column names for the DataTable
            $.post(tableUrl, { _: $.now(), column: true })
                .done(function (data, status) {
                    // If the response indicates failure, display an error message
                    if (data.success === false) {
                        that.notify('', data.message);
                    }

                    // Create an array of objects representing the DataTable columns
                    const columnsName = [];
                    const serachOffColumn = [];
                    const orderOffColumn = [];
                    let defaultOrderColumn = [];
                    let j = 0;
                    for (let i in data.column) {
                        columnsName.push({
                            data: i,
                            title: data.column[i],
                            defaultContent: "N/A", // Display "N/A" if data is not available
                            render: function (data, type, full, meta) {
                                if (type === 'display' && data === undefined && (window.location.hostname == '127.0.0.1' || window.location.hostname == 'localhost')) {
                                    console.error('Requested unknown parameter in row ' + (meta.row + 1) + ' column ' + (meta.col + 1)); // Log error message to console
                                }
                                return data;
                            }
                        });
                        if (!data.search_column.includes(i)) {
                            serachOffColumn.push(j);
                        }
                        if (!data.order_column.includes(i)) {
                            orderOffColumn.push(j);
                        }
                        if (i in data.default_order_column) {
                            defaultOrderColumn = []
                            defaultOrderColumn.push(j);
                            defaultOrderColumn.push(data.default_order_column[i]);
                        }
                        j++;
                    }

                    // Initialize the DataTable
                    datatablePointer[tableId] = $('#' + tableId).DataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": tableUrl,
                            "type": "POST",
                            "data": function (d) {
                                // Add any additional data to the POST request if needed
                            }
                        },
                        "columns": columnsName,
                        "lengthMenu": [10, 25, 50, 75, 100],
                        "columnDefs": [{
                            "searchable": false,
                            "targets": serachOffColumn
                        }, {
                            "orderable": false,
                            "targets": orderOffColumn
                        }],
                        "order": defaultOrderColumn,
                        "dom": 'Qlfrtip',
                        "searchDelay": 1000,
                        "language":
                        {
                            "processing": '<div class="web-loader table-loader hide"><div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div></div>',
                        },
                        "initComplete": function (settings, json) {

                            $('#' + tableId + ' thead').addClass('thead-dark');

                            // Trigger search on Enter key press
                            $('#' + tableId + '_filter input[type=search]').unbind().bind('keyup', function (e) {
                                if (e.keyCode == 13) {
                                    datatablePointer[tableId].search(this.value).draw();
                                }
                            });
                            $('.web-loader:not(.table-loader)').addClass('hide');
                            $('.web-loader.table-loader').removeClass('hide');
                        },
                        "drawCallback": function (settings) {
                            setTimeout(() => {
                                let tableResponsive = $('.table-responsive').width() * 1;
                                let tableWidth = $('#' + tableId).css('width').replace('px', '') * 1;
                                if ((tableResponsive - tableWidth) < 10 && (tableResponsive - tableWidth) > -10) {
                                    $('#' + tableId).css('width', tableResponsive);
                                }
                            }, 100);
                        }
                    });
                })
                .fail(function (jqXHR, status, error) {
                    // Display an error message if the request fails
                    if (jqXHR.responseJSON.message) {
                        that.notify('', jqXHR.responseJSON.message);
                    }
                });
        });
    }



}


