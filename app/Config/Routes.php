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

// api admin doctor
$routes->group('api', function($routes){
    $routes->get('admin_doctor', 'Api\AdminDoctor::index');
    $routes->get('admin_doctor/(:num)', 'Api\AdminDoctor::show/$1');
    $routes->post('admin_doctor', 'Api\AdminDoctor::create');
    $routes->put('admin_doctor/(:num)', 'Api\AdminDoctor::update/$1');
    $routes->delete('admin_doctor/(:num)', 'Api\AdminDoctor::delete/$1');
});
// api appoinment
$routes->group('api', function($routes){
    $routes->get('appointment', 'Api\Appointment::index');
    $routes->get('appointment/(:num)', 'Api\Appointment::show/$1');
    $routes->post('appointment', 'Api\Appointment::create');
    $routes->put('appointment/(:num)', 'Api\Appointment::update/$1');
    $routes->delete('appointment/(:num)', 'Api\Appointment::delete/$1');
});
// api dashboard
$routes->group('api', function($routes){
    $routes->get('dashboard', 'Api\Dashboard::index');
});
// api doctors
$routes->group('api', function($routes){
    $routes->get('doctors', 'Api\Doctors::index');
    $routes->get('doctors/(:num)', 'Api\Doctors::show/$1');
    $routes->post('doctors', 'Api\Doctors::create');
    $routes->post('doctors/update/(:num)', 'Api\Doctors::update/$1');
    $routes->delete('doctors/(:num)', 'Api\Doctors::delete/$1');
});
// api kontak
$routes->group('api', function($routes){
    $routes->get('kontak', 'Api\Kontak::index');
    $routes->get('kontak/(:num)', 'Api\Kontak::show/$1');
    $routes->post('kontak', 'Api\Kontak::create');
    $routes->delete('kontak/(:num)', 'Api\Kontak::delete/$1');
});
