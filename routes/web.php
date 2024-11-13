<?php

use Livewire\Volt\Volt;

Volt::route('/', 'pages.index')->name('home');
Volt::route('/inventory', 'pages.inventory')->name('inventory');
