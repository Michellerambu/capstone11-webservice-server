<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Halaman Utama - Sekarang pakai filter auth
$routes->get('/', 'Home::index', ['filter' => 'auth']);

// Rute Autentikasi (Jangan dikasih filter auth, nanti malah gak bisa login)
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// Rute Halaman Produk & Keranjang - Pakai filter auth
$routes->get('produk', 'ProdukController::index', ['filter' => 'auth']);
$routes->get('keranjang', 'TransaksiController::index', ['filter' => 'auth']);
$routes->get('/profile', 'ProfileController::index', ['filter' => 'auth']);