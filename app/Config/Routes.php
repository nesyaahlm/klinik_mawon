<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

$routes->get('/', 'Landing::index'); 
$routes->get('/home', 'Home::index');

$routes->get('schedule/(:num)', 'Home::schedule/$1');
$routes->post('schedule/process', 'Home::scheduleProcess');
$routes->get('confirmation/(:num)', 'Confirmation::index/$1');

$routes->post('confirmation/manual/(:num)', 'Confirmation::manual/$1');


$routes->get('contact', 'Contact::index');
$routes->post('contact/send', 'Contact::send');

// user
$routes->group('', ['filter' => 'login'], function($routes) {

    
    
    // Profile
    $routes->get('profile', 'Profile::index');
    $routes->get('profile/edit', 'Profile::edit');
    $routes->post('profile/update', 'Profile::update');
    $routes->get('profile/password', 'Profile::password');
    $routes->post('profile/password/update', 'Profile::updatePassword');

    // History
    $routes->get('history', 'Home::history');

   $routes->get('payment', 'Payment::index');          
$routes->post('payment/process', 'Payment::process'); 
$routes->get('queue/(:num)', 'Queue::index/$1');      

});

// role user
$routes->group('', ['filter' => 'role:user'], function($routes) {
    $routes->get('layanan', 'Home::layanan');
    $routes->get('services', 'Services::index');
    $routes->get('doctors', 'Doctors::index');
    $routes->get('kontak', 'Contact::index');
$routes->post('kontak/send', 'Contact::send');

});

// role admin
$routes->group('admin', ['filter' => 'role:admin'], function($routes) {

    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('kontak', 'Admin\Kontak::index');

   $routes->get('payment/confirm/(:num)', 'Admin\Payment::confirm/$1');

    // CRUD Appointments (Admin)
$routes->get('appointment', 'Admin\Appointment::index');
$routes->get('appointment/create', 'Admin\Appointment::create');
$routes->post('appointment/store', 'Admin\Appointment::store');
$routes->get('appointment/edit/(:num)', 'Admin\Appointment::edit/$1');
$routes->post('appointment/update/(:num)', 'Admin\Appointment::update/$1');
$routes->get('appointment/delete/(:num)', 'Admin\Appointment::delete/$1');


    // CRUD Users (Admin)
$routes->get('users', 'Admin\Users::index');
$routes->get('users/create', 'Admin\Users::create');
$routes->post('users/store', 'Admin\Users::store');
$routes->get('users/edit/(:num)', 'Admin\Users::edit/$1');
$routes->post('users/update/(:num)', 'Admin\Users::update/$1');
$routes->get('users/delete/(:num)', 'Admin\Users::delete/$1');


    // CRUD Dokters (Admin)
   $routes->get('dokter', 'Admin\Doctors::index');
$routes->get('dokter/create', 'Admin\Doctors::create');
$routes->post('dokter/store', 'Admin\Doctors::store');
$routes->get('dokter/edit/(:num)', 'Admin\Doctors::edit/$1');
$routes->post('dokter/update/(:num)', 'Admin\Doctors::update/$1');
$routes->get('dokter/delete/(:num)', 'Admin\Doctors::delete/$1');


    // CRUD Pasien
    $routes->get('pasien', 'Admin\Pasien::index');
    $routes->get('pasien/create', 'Admin\Pasien::create');
    $routes->post('pasien/store', 'Admin\Pasien::store');
    $routes->get('pasien/edit/(:num)', 'Admin\Pasien::edit/$1');
    $routes->post('pasien/update/(:num)', 'Admin\Pasien::update/$1');
    $routes->get('pasien/delete/(:num)', 'Admin\Pasien::delete/$1');

    // Logout
    $routes->get('logout', function() {
        service('authentication')->logout();
        return redirect()->to('/home');
    });

});

// Flutter REST API. Web routes above remain session-based and unchanged.
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/login', 'AuthController::login');

    $routes->get('doctors', 'DoctorController::index');
    $routes->get('doctors/(:num)', 'DoctorController::show/$1');
    $routes->get('doctors/(:num)/schedules', 'DoctorController::schedules/$1');

    $routes->group('', ['filter' => 'apiAuth'], function($routes) {
        $routes->post('auth/logout', 'AuthController::logout');

        $routes->get('profile', 'ProfileController::show');
        $routes->put('profile', 'ProfileController::update');

        $routes->post('appointments', 'AppointmentController::create');
        $routes->get('appointments', 'AppointmentController::index');
        $routes->get('appointments/(:num)', 'AppointmentController::show/$1');
        $routes->patch('appointments/(:num)/cancel', 'AppointmentController::cancel/$1');
        $routes->get('appointments/(:num)/queue', 'AppointmentController::queue/$1');
    });

    $routes->add('(:any)', 'ErrorController::notFound');
});
