<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AGGrid Test</title>

    <style>
        .ag-theme-alpine-dark {
					alpine-active-color: #f05340;
					range-selection-border-color: #f05340;
        }

        .text {
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
            Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
        }
        body,
        html {
            height: 100%;
            width: 100%;
        }

    </style>
</head>
<body>
<div class="container-fluid">
    <div id="myGrid" style="width: 100vw; height: 90vh;" class="ag-theme-alpine"></div>
</div>

<script>
window.props = {
	getDataRoute: "{{ route('getData') }}",
	getFieldRoute: "{{ str_replace('bruh', '', route('getValues', 'bruh')) }}",
}
</script>
<script src="{{ @asset('js/app.js') }}"></script>
</body>
</html>
