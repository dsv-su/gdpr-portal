<!DOCTYPE html>
<html>
<head>
    @include('layouts.partials.head_swe')
</head>
<body>
<div id="container">
    <a class="accessibility-link" accesskey="s" href="#content-top" title="Skip navigation"></a>
    <div id="top-links">&nbsp;</div>
    @include('layouts.partials.header_swe')
    <div id="contents">
        <a class="accessibility-link" name="content-top"></a>
        @yield('content')
        <div class="clear">
        </div>
    </div>
    @include('layouts.partials.footer-scripts_swe')
</div>
</body>
</html>
