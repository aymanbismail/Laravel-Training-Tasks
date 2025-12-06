<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Team</title>
    <style>
        table {
            border-collapse: collapse;
            width: 60%;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    @include('partials.navbar')
    <h1>Team Members</h1>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($team as $member)
            <tr>
                <td>{{ $member['name'] }}</td>
                <td>{{ $member['role'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>