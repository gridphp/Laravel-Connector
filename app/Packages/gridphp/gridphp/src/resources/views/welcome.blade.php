<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">


    <!-- these css and js files are required by php grid -->
    <link rel="stylesheet" href="/vendor/phpgrid22/js/themes/redmond/jquery-ui.custom.css">
    <link rel="stylesheet" href="/vendor/phpgrid22/js/jqgrid/css/ui.jqgrid.css">
    <script src="/vendor/phpgrid22/js/jquery.min.js" type="text/javascript"></script>
    <script src="/vendor/phpgrid22/js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
    <script src="/vendor/phpgrid22/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
    <script src="/vendor/phpgrid22/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
    <!-- these css and js files are required by php grid -->

</head>

<body>

    <div style="margin:10px">
        {!! $out or '$out not defined'!!}
    </div>

    <div style="margin:10px">
        {!! $out2 or '$out2 not defined'!!}
    </div>

    @include("phpgrid::tools.token-fetcher")

</body>
</html>