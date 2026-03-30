<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ── GUEST BOOKING (no login required) ────────────────────
$routes->get('/',     'Home::index');
$routes->post('book', 'Home::book');

// ── QUEUE TICKET (no login required) ─────────────────────
$routes->get('queue/status/(:num)', 'Queue::status/$1');

// ── API (no login required) ───────────────────────────────
$routes->get('api/queue-status/(:num)', 'API::queueStatus/$1');
$routes->get('api/queue-live',          'API::queueLive');


// ── AUTH ──────────────────────────────────────────────────
$routes->get('login',     'Auth::index');
$routes->post('login',    'Auth::login');
$routes->get('logout',    'Auth::logout');

// Patient Dashboard
$routes->get('patient/dashboard', 'Patient::dashboard');

// ── ADMIN (login required) ────────────────────────────────
$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {
    $routes->get('dashboard',               'Admin::index');
    $routes->get('appointments',            'Admin::appointments');
    $routes->post('appointments/update',    'Admin::updateStatus');
    $routes->post('appointments/delete',    'Admin::deleteAppointment');
    $routes->get('queue',                   'Admin::queue');
    $routes->post('queue/next',             'Queue::next');
    $routes->get('users',                   'Admin::users');
    $routes->get('services',               'Admin::services');
    $routes->post('services/store',        'Admin::storeService');
    $routes->post('services/toggle/(:num)','Admin::toggleService/$1');
});