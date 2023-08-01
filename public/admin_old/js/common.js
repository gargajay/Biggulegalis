var imagesPreview = function (input, placeToInsertImagePreview) {

    if (input.files) {
        var filesAmount = input.files.length;

        for (i = 0; i < filesAmount; i++) {
            var reader = new FileReader();

            reader.onload = function (event) {
                $("#preview-field").attr('src', event.target.result);
                $(".image-preview-wrapper").show();
            }

            reader.readAsDataURL(input.files[i]);
        }
    }

};

$(document).ready(function () {
    $('.delete-confirm').on('click', function () {
        bootbox.confirm({
            title: 'Confirm dialog',
            message: 'Native confirm dialog has been replaced with Bootbox confirm box.',
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-primary'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-link'
                }
            },
            callback: function (result) {
                bootbox.alert({
                    title: 'Confirmation result',
                    message: 'Confirm result: ' + result
                });
            }
        });
    });

    // Hide notification message
    $("div.alert").fadeTo(2000, 500).slideUp(500, function () {
        $("div.alert").slideUp(500);
    });

    $(".file-upload-field").on("change", function () {
        imagesPreview(this, 'div.gallery');
    });
    

    // Basic datatable
    $('.my-datatable').DataTable({
        "bPaginate": false,
        "searching": false,
        'columnDefs': [{
            'targets': 0,
            // 'className': 'dt-body-center'
        }]
    });
});
