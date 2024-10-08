<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('', function () {
    return view('test', [
        'devices' => \App\Models\System\Device::get(),
    ]);
});
Route::post('messageSend', function () {
    $device = \App\Models\System\Device::find(request('device_id'));
    $message = app('airothon')->message()->get(request('message'));

    $message->send($device);

    return redirect('/logs/' . $device->id);
});

Route::post('customSend', function () {
    $device = \App\Models\System\Device::find(request('device_id'));

    telnet($device, request('customMessage'));

    return redirect('/logs/' . $device->id);
});

Route::get('logs/{id}', function ($id) {
    $device = \App\Models\System\Device::find($id);
    $logs = \App\Models\Log\DeviceLog::where(['serial_number' => $device->serial_number])->orderByDesc('created_at')->get();

    return view('logs', [
        'device' => $device,
        'logs' => $logs,
    ]);
});
