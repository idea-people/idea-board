(function ($) {
    $(document).ready(function () {
        var $idea_board = $('#idea-board');

        $idea_board
            .find('.idea-board-file-inputs')
            .css('visibility', 'visible')
            .find("input[type=file]")
            .nicefileinput({
                label: 'FILE'
            });

        $('.idea-board-validate').validate();
    });
})(jQuery);