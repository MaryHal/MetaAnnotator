(function ($) {
    var youtubePlayer;
    var annotations;
    var annotationTimer;

    function openYoutubePlayer(youtubeId) {
        youtubePlayer = new YT.Player('player', {
            height: '390',
            width: '640',
            videoId: youtubeId
        });
    }

    function repopulateAnnotationList(data) {
        var list = $('#annotationList');
        list.empty();
        $.each(data, function (i, annotation) {
            list.append(
                $('<li></li>').text(annotation.time + ": " + annotation.text));
        });
    }

    function showAnnotations(data) {
        if (youtubePlayer !== null) {
            var list = $('#annotationList');

            var state = youtubePlayer.getPlayerState();
            if (state !== 1) {
                return;
            }

            var time = youtubePlayer.getCurrentTime();
            time = Math.floor(time);

            $.each(data, function (i, annotation) {
                var timeDiff = time - annotation.time;
                if (annotation.shown === false && timeDiff > -3) {
                    annotation.shown = true;

                    var annotationListItem = $('<li></li>').text(annotation.time + ": " + annotation.text);
                    list.append(annotationListItem);

                    annotationListItem.hide().fadeIn();

                    setTimeout(function () {
                        annotationListItem.fadeOut(
                            2000,
                            function () {
                                annotationListItem.remove();
                            });
                    }, 8000);
                }
            });
        }
    }

    window.addEventListener('load', function () {
        $('#startyt').click(function () {
            var youtubeId = $('#youtubeUrl').val();

            openYoutubePlayer(youtubeId);

            $.post(`http://localhost:8000/api/v1/index.php?endpoint=annotationset&youtubeid=${youtubeId}`,
                   '',
                   function (data, success) {
                       $('#uid').val(data['hash']);
                   },
                  'json');
        });

        $('#startuid').click(function () {
            var uid = $('#uid').val();
            $.get(`http://localhost:8000/api/v1/index.php?endpoint=annotationset&uid=${uid}`,
                   '',
                   function (data, success) {
                       var youtubeId = data['youtubeId'];
                       $('#youtubeUrl').val(youtubeId);

                       youtubePlayer = new YT.Player('player', {
                           height: '390',
                           width: '640',
                           playerVars: {
                               'start': 0, // Why doesn't this work?
                               'autoplay': 0,
                               'controls': 1
                           },
                           videoId: youtubeId,
                           events: {
                               'onReady': function () {
                                   annotations = data['annotations'];

                                   var list = $('#annotationList');
                                   list.empty();

                                   // repopulateAnnotationList(annotations);

                                   for (var i  = 0; i < annotations.length; i++) {
                                       annotations[i]['shown'] = false;
                                   }

                                   clearInterval(annotationTimer);
                                   setInterval(function () {
                                       showAnnotations(annotations);
                                   }, 500);
                               }
                           }
                       });
                   },
                  'json');
        });

        $('#insert').click(function() {
            var uid = $('#uid').val();
            var text = $('#annotation').val();
            var time = youtubePlayer.getCurrentTime();
            time = Math.floor(time);
            $.post(`http://localhost:8000/api/v1/index.php?endpoint=annotation&uid=${uid}&text=${text}&time=${time}`,
                   '',
                   function (data, success) {
                       annotations.push(data);

                       annotations.sort(function (a, b) {
                           return a.time - b.time;
                       });

                       repopulateAnnotationList(annotations);
                   },
                   'json');
        });
    }, false);
}
)(jQuery);
