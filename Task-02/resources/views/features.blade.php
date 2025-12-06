<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Features</title>
</head>

<body>
    @include('partials.navbar')
    <h1>Features</h1>

    <ul>
        @foreach($features as $feature)
        <li>{{ $feature }}</li>
        @endforeach
    </ul>
</body>

</html>