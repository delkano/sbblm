// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

// Place any jQuery/helper plugins in here.
$(function() {
    var url = $('#avatarbox').data("url");
    $('#avatarbox').singleupload({
        action: url,
        inputId: 'avatarupload',
        onError: function(code) {
            console.debug('error code '+res.code);
        }
        /* ,onSucess: function(url, data) {} */
        /*,onProgress: function(loaded, total) {} */
    });

    $("textarea.beautiful").trumbowyg();
});
