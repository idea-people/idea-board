(function ($) {

    $(document).ready(function () {
        $('.idea-nopriv-upload-btn').click(function (e) {
            e.preventDefault();
            var $file = $(this).parent().find('input[type="file"]');
            $file.trigger('click');
        });

        $('.idea-nopriv-upload-btn-wrap input[type="file"]').change(function (e) {
            var $editor = $(this).parent().siblings('.wp-editor-wrap');
            var $textarea = $editor.find('textarea');
            var $progress = $(this).parent().find('.idea-nopriv-upload-progress');

            var file_param = $(this).data('file_param');
            var ajax_url = $(this).data('ajax_url');
            var formData = new FormData();
            var files = this.files;

            $.each(files, function (i, file) {
                formData.append(file_param + '[]', file);
            });

            $.ajax({
                async: true,
                method: "post",
                url: ajax_url,
                processData: false,
                data: formData,
                contentType: false,
                beforeSend: function () {
                    $progress.show();
                },
                success: function (response) {
                    if (response.success) {
                        var attachments = response.attachments;

                        $.each(attachments, function (i, attachment) {
                            var $image = $('<img>', {
                                'src': attachment.guid
                            });

                            var output = $image.clone().wrapAll("<div/>").parent().html();

                            if (typeof tinyMCE != 'undefined') {
                                if ($editor.hasClass('html-active')) {
                                    var $area = $editor.find('.wp-editor-area');
                                    var v = $area.val();
                                    $area.val(v + output);
                                } else {
                                    tinyMCE.activeEditor.execCommand('mceInsertContent', false, output);
                                }

                            } else {
                                $textarea.val(function (index, value) {
                                    return value + (!value ? '' : ' ') + output;
                                });
                            }
                        });
                    } else {
                        alert(response.data[0].message);
                    }

                    $progress.hide();
                }
            });
        });
    });


})(jQuery);