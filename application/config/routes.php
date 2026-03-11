<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller']   = 'Dashboard/index';
$route['404_override']         = '';
$route['translate_uri_dashes'] = FALSE;

// Auth
$route['login']      = 'Auth/login';
$route['login_post'] = 'Auth/login_post';
$route['logout']     = 'Auth/logout';

// Dashboard
$route['dashboard'] = 'Dashboard/index';

// Expenses (transaction history)
$route['expenses']                    = 'Expenses/index';
$route['expenses/store']              = 'Expenses/store';
$route['expenses/update/(:num)']      = 'Expenses/update/$1';
$route['expenses/delete/(:num)']      = 'Expenses/delete/$1';
$route['expenses/get/(:num)']         = 'Expenses/get/$1';

// Categories
$route['categories']                  = 'Categories/index';
$route['categories/store']            = 'Categories/store';
$route['categories/get/(:num)']       = 'Categories/get/$1';
$route['categories/update/(:num)']    = 'Categories/update/$1';
$route['categories/delete/(:num)']    = 'Categories/delete/$1';

// Users
$route['users']                       = 'Users/index';
$route['users/store']                 = 'Users/store';
$route['users/get/(:num)']            = 'Users/get/$1';
$route['users/update/(:num)']         = 'Users/update/$1';
$route['users/delete/(:num)']         = 'Users/delete/$1';
$route['users/toggle-status/(:num)']  = 'Users/toggle_status/$1';

// Exchange Rates
$route['exchange-rates']              = 'ExchangeRates/index';
$route['exchange-rates/update']       = 'ExchangeRates/update';
$route['exchange-rates/fetch']        = 'ExchangeRates/fetch';

// Profile
$route['profile']                     = 'Profile/index';
$route['profile/update']              = 'Profile/update';
$route['profile/change-password']     = 'Profile/change_password';
$route['profile/remove-avatar']       = 'Profile/remove_avatar';

// Export
$route['export']                      = 'Export/index';
$route['export/csv']                  = 'Export/csv';
$route['export/pdf']                  = 'Export/pdf';
