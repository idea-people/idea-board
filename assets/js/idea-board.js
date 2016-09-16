(function ($) {
    $(document).ready(function () {
        var $idea_board = $('#idea-board');

        $idea_board
            .find('.idea-board-file-inputs')
            .css('visibility', 'visible')
            .find("input[type=file]")
            .nicefileinput({
                label: IDEA_BOARD.lang.File
            });

        $('.idea-board-validate').validate();
    });
})(jQuery);