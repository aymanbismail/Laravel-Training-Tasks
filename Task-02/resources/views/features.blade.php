<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Features</title>
</head>

<body>
    <h1>Features</h1>

    <ul>
        @foreach($features as $feature)
        <li>{{ $feature }}</li>
        @endforeach
    </ul>

    <p><a href="/">Home</a> | <a href="/about">About</a> | <a href="/team">Team</a></p>
</body>

</html>