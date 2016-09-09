(function ($) {
    $(document).ready(function () {
        $('#idea-board .idea-board-file-inputs').find("input[type=file]").nicefileinput({
            label: '파일선택'
        });

        $('#idea-board').find('.idea-board-file-inputs').css('visibility', 'visible');

        $('.idea-board-validate').validate({});
    });
})(jQuery);