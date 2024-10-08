<style>
    body {
        background-color: black;
        color: white
    }
</style>

<form method="post" action="/messageSend">
    <fieldset>
        <legend>Cihaz Seç:</legend>

        <table border="1">
            <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>IP</th>
                <th>Seri No</th>
                <th>Modem</th>
                <th>Version</th>
                <th>IMEI</th>
                <th>IMSI</th>
                <th>Telefon No</th>
                <th>Signal</th>
                <th>Hash</th>
                <th>Bağlanma</th>
                <th>Log</th>
            </tr>
            </thead>
            <tbody>
            @foreach($devices as $device)
                <tr>
                    <td><input type="radio" name="device_id" value="{{$device->id}}" required></td>
                    <td>{{$device->id}}</td>
                    <td>{{$device->ip}}</td>
                    <td>{{$device->serial_number}}</td>
                    <td>{{$device->model}}</td>
                    <td>{{$device->version}}</td>
                    <td>{{$device->imei}}</td>
                    <td>{{$device->imsi}}</td>
                    <td>{{$device->phone_number}}</td>
                    <td>{{$device->signal_quality}}</td>
                    <td>{{$device->last_hash}}</td>
                    <td>{{$device->connected_at->format('d.m.Y H:i:s')}}</td>
                    <td>
                        <button><a href="/logs/{{$device->id}}">Loglar</a></button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </fieldset>

    <fieldset>
        <legend>Mesaj Seç:</legend>

        <table border="1">
            <thead>
            <tr>
                <th></th>
                <th>Mesaj</th>
                <th>Paket</th>
                <th>Açıklama</th>
            </tr>
            </thead>
            <tbody>
            @foreach(app('airothon')->message()->all() as $index => $message)
                @if(method_exists($message, 'send'))
                    <tr>
                        <td>
                            <button type="submit" name="message" value="{{$index}}">Gönder</button>
                        </td>
                        <td>{{$index}}</td>
                        <td>{{$message->context()}}</td>
                        <td>{{$message->getDescription()}}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </fieldset>

    <fieldset>
        <legend>Özel Mesaj:</legend>
        <textarea name="customMessage" rows="3"></textarea>
        <button type="submit" formaction="customSend">Gönder</button>
    </fieldset>
</form>
