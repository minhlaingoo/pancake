<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\MqttComponent;
use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\Users\Create as UsersCreate;
use App\Livewire\Users\Edit as UsersEdit;

use App\Livewire\RolePermissions\Index as RolePermissionsIndex;
use App\Livewire\RolePermissions\Create as RolePermissionsCreate;
use App\Livewire\RolePermissions\Edit as RolePermissionsEdit;

use App\Livewire\ActivityLog\Index as ActivityLogIndex;
use App\Livewire\ActivityLog\Detail as ActivityLogDetail;
use App\Livewire\BrokerSetting;
use App\Livewire\Protocols\Index as ProtocolsIndex;
use App\Livewire\Protocols\Create as ProtocolsCreate;
use App\Livewire\Protocols\FinalLab as ProtocolsFinalLab;

use App\Livewire\Devices\Index as DevicesIndex;
use App\Livewire\Devices\Create as DevicesCreate;
use App\Livewire\Devices\Detail as DevicesDetail;
use App\Livewire\Devices\DeviceComponents as DevicesComponents;
use App\Livewire\Devices\Setting as DevicesSetting;
use App\Livewire\Devices\Log as DeviceLog;
use App\Livewire\Phase\InitializationCycleSetup;
use App\Livewire\Phase\StorageCycleSetup;
use App\Livewire\Phase\SystemCleaningCycleSetup;
use App\Livewire\ProtocolProcessing;
use App\Livewire\Protocols\HistoryList;
use App\Livewire\DeviceComponents\Edit as DeviceComponentsEdit;
use App\Livewire\DeviceComponents\Index as DeviceComponentsIndex;

use App\Livewire\Setting;

// Authentication Routes
require __DIR__ . '/auth.php';
// Authenticated Routes
Route::middleware('auth')->group(function () {


    # Default Pages
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('/profile', \App\Livewire\Profile\Index::class)->name('user-profile');
    Route::get('/user-setting', \App\Livewire\UserSetting::class)->name('user-setting');
    Route::get('/setting', Setting::class)->name('setting');
    Route::get('/broker-setting', BrokerSetting::class)->name('broker-setting');

    # Phase Routes
    Route::prefix('phase')->name('phase.')->group(function () {
        Route::get('initialization-cycle-setup', InitializationCycleSetup::class)->name('initialization-cycle-setup');
        Route::get('storage-cycle-setup', StorageCycleSetup::class)->name('storage-cycle-setup');
        Route::get('system-cleaning-setup', SystemCleaningCycleSetup::class)->name('system-cleaning-setup');
    });

    // Users Routes
    Route::prefix('users')->group(function () {
        Route::get('/', UsersIndex::class)->name('users.index')->middleware('can:view,user');
        Route::get('/create', UsersCreate::class)->name('users.create')->middleware('can:create,user');
        Route::get('/{id}/edit', UsersEdit::class)->name('users.edit')->middleware('can:update,user');
    });

    // Role & Permissions Routes
    Route::prefix('role-permissions')->group(function () {
        Route::get('/', RolePermissionsIndex::class)->name('role-permissions.index')->middleware('can:view,role');
        Route::get('/create', RolePermissionsCreate::class)->name('role-permissions.create')->middleware('can:create,role');
        Route::get('/{role}/edit', RolePermissionsEdit::class)->name('role-permissions.edit')->middleware('can:update,role');
    });

    Route::prefix('activity-logs')->group(function () {
        Route::get('/', ActivityLogIndex::class)->name('activity-logs.index')->middleware('can:view,activity-log');
        Route::get('/{id}', ActivityLogDetail::class)->name('activity-logs.detail')->middleware('can:view,activity-log');
    });

    Route::prefix('devices')->middleware('can:view,device')->group(function () {
        Route::get('/', DevicesIndex::class)->name('devices.index')->middleware('can:view,device');
        Route::get('/create', DevicesCreate::class)->name('devices.create')->middleware('can:create,device');
        Route::get('/{id}/detail', DevicesDetail::class)->name('devices.detail');
        Route::get('/{id}/components', DevicesComponents::class)->name('devices.deviceComponents');
        Route::get('/{id}/components/{deviceComponent}/edit', DeviceComponentsEdit::class)->name('deviceComponents.edit')->middleware('can:update,device');
        Route::get('/{id}/logs', DeviceLog::class)->name('devices.logs');
        Route::get('/{id}/setting', DevicesSetting::class)->name('devices.setting')->middleware('can:update,device');
    });

    Route::prefix('protocols')->group(function () {
        Route::get('/', ProtocolsIndex::class)->name('protocols.index');
        Route::get('/create', ProtocolsCreate::class)->name('protocols.create');
        Route::get('/{sample_id}/final-lab', ProtocolsFinalLab::class)->name('protocols.final-lab');
        Route::get('/{sample_id}/edit', App\Livewire\Protocols\Edit::class)->name('protocols.edit');
        Route::get('/run', App\Livewire\Protocols\Run::class)->name('protocols.run');
        Route::get('/{protocol}/{uid}/processing', ProtocolProcessing::class)->name('protocols.processing');
        Route::get('/histories', HistoryList::class)->name('protocols.histories');
    });

    Route::prefix('device-components')->group(function () {
        Route::get('/', DeviceComponentsIndex::class)->name('device-components.index');
    });

    Route::prefix('presets')->group(function () {
        Route::get('/', App\Livewire\Presets\Index::class)->name('presets.index');
        Route::get('/create', App\Livewire\Presets\Create::class)->name('presets.create');
        Route::get('/{preset}/edit', App\Livewire\Presets\Edit::class)->name('presets.edit');
    });




    Route::get('/mqtt', MqttComponent::class);
    Route::get('/sessions', fn() => view('sessions'));
});
