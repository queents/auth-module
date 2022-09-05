
@php echo "<?php";
@endphp

Route::post('{{ lcfirst($model) }}/register', [Modules\{{ $module }}\Http\Controllers\Api\{{ $model }}Auth::class,'register'])->name('{{ lcfirst($model) }}.register');
Route::post('{{ lcfirst($model) }}/login', [Modules\{{ $module }}\Http\Controllers\Api\{{ $model }}Auth::class,'login'])->name('{{ lcfirst($model) }}.login');

Route::post('{{ lcfirst($model) }}/forgetPassword', [Modules\{{ $module }}\Http\Controllers\Api\{{ $model }}Auth::class,'forgetPassword'])->name('{{ lcfirst($model) }}.forgetPassword');
Route::post('{{ lcfirst($model) }}/resetPassword', [Modules\{{ $module }}\Http\Controllers\Api\{{ $model }}Auth::class,'resetPassword'])->name('{{ lcfirst($model) }}.resetPassword');


Route::middleware(['auth:sanctum'])->group(function () {
Route::post('{{ lcfirst($model) }}/verify-account', [Modules\{{ $module }}\Http\Controllers\Api\{{ $model }}Auth::class,'verifyAccount'])->name('{{ lcfirst($model) }}.verify-account');
Route::post('{{ lcfirst($model) }}/changePassword', [Modules\{{ $module }}\Http\Controllers\Api\{{ $model }}Auth::class,'changePassword'])->name('{{ lcfirst($model) }}.changePassword');
Route::post('{{ lcfirst($model) }}/logout', [Modules\{{ $module }}\Http\Controllers\Api\{{ $model }}Auth::class,'logout'])->name('{{ lcfirst($model) }}.logout');
Route::post('{{ lcfirst($model) }}/updateProfile', [Modules\{{ $module }}\Http\Controllers\Api\{{ $model }}Auth::class,'updateProfile'])->name('{{ lcfirst($model) }}.updateProfile');
Route::get('{{ lcfirst($model) }}/profile', [Modules\{{ $module }}\Http\Controllers\Api\{{ $model }}Auth::class,'profile'])->name('{{ lcfirst($model) }}.profile');
});
