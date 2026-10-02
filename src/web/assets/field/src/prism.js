;(function($){
    $.fn.prismField = function(settings) {
        var $el = $(this).find('code'),
            $wrapper = $(this),
            $textarea = $wrapper.find('.js--prism-textarea');

        var element = $el.get(0);
        var editor = bililiteRange.fancyText(element, Prism.highlightElement);

        // init the undo/redo
        bililiteRange(editor).undo(0).data().autoindent = true;

        // Handle formatting shortcuts
        $el.on('keydown', function(e) {
            switch (e.keyCode) {
                // Tab
                case 9:
                    e.preventDefault();
                    $el.sendkeys('\t');
                    break;
                case 219:
                    if ((e.ctrlKey || e.metaKey)) {
                        e.preventDefault();
                        bililiteRange(element).bounds('selection').unindent();
                    }

                    break;
                case 221:
                    if ((e.ctrlKey || e.metaKey)) {
                        e.preventDefault();
                        bililiteRange(element).bounds('selection').indent('\t');
                    }

                    break;
            }

            // control/cmd z
            if ((e.ctrlKey || e.metaKey) && e.which == 90) {
                e.preventDefault(); bililiteRange.undo(e);
            }

            // control/cmd y
            if ((e.ctrlKey || e.metaKey) && e.which == 89){
                e.preventDefault(); bililiteRange.redo(e);
            }
        }).on('keyup', function(e) {
            $textarea.val(bililiteRange(element).text());
        }).trigger('keyup');
    };
})(jQuery);
