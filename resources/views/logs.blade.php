<style>
    body {
        background-color: black;
        color: white
    }
</style>

<form method="post">
    <fieldset>
        <legend>Cihaz:</legend>

        <table border="1">
            <thead>
            <tr>
                <th>ID</th>
                <th>IP</th>
                <th>Seri No</th>
                <th>IMEI</th>
                <th>Telefon</th>
                <th>Bağlanma</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{$device->id}}</td>
                <td>{{$device->ip}}</td>
                <td>{{$device->serial_number}}</td>
                <td>{{$device->imei}}</td>
                <td>{{$device->phone_number}}</td>
                <td>{{$device->connected_at->format('d.m.Y H:i:s')}}</td>
            </tr>
            </tbody>
        </table>
    </fieldset>

    <fieldset>
        <legend>Loglar:</legend>

        <table border="1">
            <thead>
            <tr>
                <th>Tarih</th>
                <th>Yön</th>
                <th>Request</th>
            </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{$log->created_at->format('d.m.Y H:i:s')}}</td>
                    <td>@if($log->direction === 0)
                            -->
                        @else
                            <--
                        @endif
                    </td>
                    <td>{{$log->message}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </fieldset>
</form>
