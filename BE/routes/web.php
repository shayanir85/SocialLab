<?php

use Illuminate\Support\Facades\Route;

Route::fallback(function(){
    abort(404);
});
require __DIR__.'/settings.php';
