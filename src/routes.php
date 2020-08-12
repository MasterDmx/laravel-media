<?php

use Illuminate\Support\Facades\Route;

$mediaConfig = config('media', 'vendor/media/manager');

Route::group([
    'namespace'  => 'MasterDmx\LaravelMedia\Controllers',
], function () use ($mediaConfig) {

    Route::get($mediaConfig['manager_route_preffix'] . '/init', 'ManagerController@init')->name('media-manager.init');
    Route::get($mediaConfig['manager_route_preffix'] . '/files', 'ManagerController@files')->name('media-manager.files');
    Route::post($mediaConfig['manager_route_preffix'] . '/files/upload', 'ManagerController@upload')->name('media-manager.upload');
    Route::delete($mediaConfig['manager_route_preffix'] . '/files/{id}', 'ManagerController@remove')->name('media-manager.remove');
});
