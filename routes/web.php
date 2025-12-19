<?php

use App\Models\User;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;






//Route::get('/', fn () => view('admin.dashboard'));
//Route::get('/login', fn () => view('auth.login'));
//Route::post('/login', fn () => view('auth.login'))->name('login');

Route::get("/hola", fn () => "Hola");

Route::get("/hash_check", function () {
    //$user = User::where('email', 'admin@atentidoc.com')->first();
    $user = User::where('email', 'admin@atentidoc.com')->first();
    //$password = 'Admin1234';
    dd('Usuario', $user);
    $password = 'Hola12345';
    $hash = $user->password_hash; 
    dd(bcrypt($password), Hash::make($password), $hash);
    $is_auth = password_verify($password, $hash);
    if ($is_auth) {
        return 'hola';
    }
    else {
        return 'chau';
    }
});

Route::get("/create_user", function () {
    $user = DB::table('users')->where('email', 'usuario_de_prueba_con_laravel@atentidoc.com')->first();

    if (!$user) {
        DB::table('users')->insert([
            'email' => 'usuario_de_prueba_con_laravel@atentidoc.com',
            'first_name' => 'Usuario',
            'last_name' => 'de Prueba',
            'password_hash' => bcrypt('Hola12345'),
            'phone' =>'+5492615550005',
            'document_id' => '35344691',
            'birth_date' => '1995-03-31',
            'gender' => 'MALE',
            //'role' => bcrypt('Hola12345'),
            'status' => 'ACTIVE',
            'email_verified' => true,
            'avatar_url' => null,
            'provider' => 'LOCAL'
        ]);
        $user = DB::table('users')->where('email', 'usuario_de_prueba_con_laravel@atentidoc.com')->first();
    }

    $patient = DB::table('patients')->where('user_id', $user->id)->first();
    
    if (!$patient) {
        DB::table('patients')->insert([
            'affiliate_number' => '1515323',
            'card_alias' => null,
            'card_mask' => null,
            'insurance' => 'NINGUNA',
            'mp_card_id' => null,
            'mp_customer_id' => null,
            'user_id' => $user->id,
        ]);
        $patient = DB::table('patients')->where('user_id', $user->id)->first();
    }

    $user_role = DB::table('user_roles')->where('user_id', $user->id)->first();
    
    if (!$user_role) {
        DB::table('user_roles')->insert([
            'role' => 'PATIENT',
            'user_id' => $user->id,
        ]);
        $user_role = DB::table('user_roles')->where('user_id', $user->id)->first();
    }

    $collect = new stdClass();

    $collect->email = $user->email;
    $collect->first_name = $user->first_name;
    $collect->last_name = $user->last_name;
    $collect->phone = $user->phone;
    $collect->document_id = $user->document_id;
    $collect->birth_date = $user->birth_date;
    $collect->gender = $user->gender;
    $collect->status = $user->status;
    $collect->email_verified = $user->email_verified;
    $collect->avatar_url = $user->avatar_url;

     $patient = DB::table('patients')->where('user_id', $user->id)->first();
    if ($patient) {
        $collect_patient = new stdClass();
        $collect_patient->affiliate_number = $patient->affiliate_number;
        $collect_patient->card_alias = $patient->card_alias;
        $collect_patient->card_mask = $patient->card_mask;
        $collect_patient->insurance = $patient->insurance;
       

        $collect->patient = $collect_patient;
    } 

    $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
    if ($doctor) {
        $collect_doctor = new stdClass();
        $collect_doctor->address = $doctor->address;
        $collect_doctor->bank = $doctor->bank;
        $collect_doctor->bio = $doctor->bio;
        $collect_doctor->cbu = $doctor->cbu;

        $collect_doctor->consultation_price = $doctor->consultation_price;
        $collect_doctor->cv = $doctor->cv;
        $collect_doctor->experience_years = $doctor->experience_years;
        $collect_doctor->license_number = $doctor->license_number;       
        $collect_doctor->patients_count = $doctor->patients_count;

        $collect->doctor = $collect_doctor;
    }

    $user_roles = DB::table('user_roles')->where('user_id', $user->id)->get();
    if ($user_roles->count() > 0) {
        $roles = [];
        foreach ($user_roles as $user_role) {
            $roles[] = $user_role->role;
        }
        $collect->roles = $roles;
    }
    dd('Usuario con Roles', $collect);

});

// Rutas públicas
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login.show');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');


// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
    
    Route::get('/', fn () => view('admin.dashboard'))->name('admin.dashboard');

    Route::get('/user-list/doctors', [\App\Http\Controllers\UserController::class, 'doctor_index'])->name('admin.list.doctor');
    Route::get('/user-list/patients', [\App\Http\Controllers\UserController::class, 'patient_index'])->name('admin.list.patient');
    Route::get('/user-list/admins', [\App\Http\Controllers\UserController::class, 'admin_index'])->name('admin.list.admin');

    Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create_user'])->name('admin.create.user');
    Route::post('/users/store', [\App\Http\Controllers\UserController::class, 'store_user'])->name('admin.store.user');


    Route::get('/users/show/{user_id?}', [\App\Http\Controllers\UserController::class, 'show_user'])->name('admin.show.user');
    
    Route::get('/users/edit/{user_id?}', [\App\Http\Controllers\UserController::class, 'edit_user'])->name('admin.edit.user');
    Route::post('/users/update/{user_id?}', [\App\Http\Controllers\UserController::class, 'update_user'])->name('admin.update.user');
    
    Route::delete('/users/delete/{user_id?}', [\App\Http\Controllers\UserController::class, 'delete_user'])->name('admin.delete.user');

    
});