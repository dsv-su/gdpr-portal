<!DOCTYPE html>
<html>
<head>
    @include('layouts.partials.head_swe')
    <link href="https://vjs.zencdn.net/7.6.6/video-js.css" rel="stylesheet" />
    <!-- If you'd like to support IE8 (for Video.js versions prior to v7) -->
    <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
    <style>
    /* Show the controls (hidden at the start by default) */
    .video-js .vjs-control-bar {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    }
    </style>
</head>
<body>
<div id="container">
    <a class="accessibility-link" accesskey="s" href="#content-top" title="Skip navigation"></a>
    <div id="top-links"></div>
    @include('layouts.partials.header_swe')
    <div id="contents">
        <a class="accessibility-link" name="content-top"></a>
    </div>
    <div class="row">
        <video id="video1" class="video-js vjs-default-skin" controls autoplay preload="auto" width="500" height="300" poster="https://video-js.zencoder.com/oceans-clip.png" data-setup="{}">
            <source src="//vjs.zencdn.net/v/oceans.mp4" type='video/mp4' />
            <track kind="captions" src="captions.vtt" srclang="en" label="English" />
        </video>
        <video id="video2" class="video-js vjs-default-skin" autoplay preload="auto" width="500" height="300" poster="https://video-js.zencoder.com/oceans-clip.png" data-setup="{}">
            <source src="//vjs.zencdn.net/v/oceans.mp4" type='video/mp4' />
            <track kind="captions" src="captions.vtt" srclang="en" label="English" />
        </video>
    </div>
    <div class="row">
        <video id="video3" class="video-js vjs-default-skin vjs-big-play-centered" preload width="500" height="300" poster="" data-setup='{"fluid": false, "playbackRates": [0.5, 1, 2], "html5": {"nativeTextTracks": false}}'>
            <source src="//vjs.zencdn.net/v/oceans.mp4" type="video/mp4">
            <track kind="captions" src="captions.vtt" srclang="en" label="English" />
        </video>

        <video id="video4" class="video-js vjs-default-skin vjs-big-play-centered" preload width="500" height="300" poster="" data-setup='{"fluid": false, "playbackRates": [0.5, 1, 2], "html5": {"nativeTextTracks": false}}'>
            <source src="//vjs.zencdn.net/v/oceans.mp4" type="video/mp4">
            <track kind="captions" src="captions.vtt" srclang="en" label="English" />
        </video>
    </div>

    <div class="clear"></div>
</div>
</body>

<script>
    var medias = Array.prototype.slice.apply(document.querySelectorAll('audio,video'));
    medias.forEach(function(media) {
        media.addEventListener('play', function(event) {
            medias.forEach(function(media) {
                if(event.target != media) media.play();
            });
        });
        media.addEventListener('pause', function(event) {
            medias.forEach(function(media) {
                if(event.target != media) media.pause();
            });
        });
    });


</script>
</html>
