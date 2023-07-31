<html>
    <body>
    <table>
<tr><td>id</td><td>status</td><td>created</td></tr>
@foreach ($logs as $log)
    <tr><td>{{ $log->id }}</td><td>{{ $log->result }}</td><td>{{ $log->created_at }}</td></tr>
    @endforeach
    </table>

    </body>
</html>