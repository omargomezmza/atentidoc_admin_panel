<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    
    public function admin_index() 
    {
        $data = (new UserService())->getUsersByRole('ADMIN');
        return view('admin.user.list-admin', ['list' => $data]);    
    }

    public function patient_index() 
    {
        $data = (new UserService())->getUsersByRole('PATIENT');
        return view('admin.user.list-patient', ['list' => $data]);    
    }

    public function doctor_index() 
    {
        $data = (new UserService())->getUsersByRole('DOCTOR');
        return view('admin.user.list-doctor', ['list' => $data]);    
    }

    public function create_user() 
    {
        $specialties = Specialty::all()->toArray();
        return view('admin.user.create-user', ['specialties' => $specialties]);
    }

    public function store_user(StoreUserRequest $request)
    {
        // 1. Se obtienen los datos provenientes del formulario
        $requestData = $request->validated();

        // 2. Se formatean los datos para que coincidan con la estructura esperada por el servicio
        [$payload, $roles, $patient, $doctor] = $this->formatUserData($requestData);

        // 3. Se llama al servicio para crear el usuario
        $res = (new UserService())->createUser($payload, $roles, $patient, $doctor);
        
        // 4. Se maneja la respuesta del servicio
        if (isset($res['error']) && $res['error'] === true) {
            return redirect()->back()->with([
                'error' => $res['message'] ?? 'La operación falló.',
                'details' => $res['details'] ?? [],
            ])->withInput();
            // A: Caso de error
        }

        return redirect()->back()->with('success', 'Usuario creado correctamente.');
        // A: Caso de éxito
    }

    public function show_user(int $user_id) 
    {
        $specialties = Specialty::all()->toArray();
        $user = (new UserService)->getUserById((int) $user_id);
        return view('admin.user.show-user', [
            'specialties' => $specialties, 
            'user' => $user
        ]);
    }

    public function edit_user(int $user_id) 
    {
        $specialties = Specialty::all()->toArray();
        $user = (new UserService)->getUserById((int) $user_id);
        return view('admin.user.edit-user', [
            'specialties' =>  $specialties, 
            'user' => $user 
        ]);
    }

    public function update_user(UpdateUserRequest $request, int $user_id)
    {
        // 1. Se obtienen los datos provenientes del formulario
        $requestData = $request->all();

        // 2. Se formatean los datos para que coincidan con la estructura esperada por el servicio
        [$payload, $roles, $patient, $doctor] = $this->formatUserData($requestData, true);

        // 3. Se llama al servicio para actualizar el usuario
        $res = (new UserService())->updateUser((int) $user_id, $payload, $roles, $patient, $doctor);

        // 4. Se maneja la respuesta del servicio
        if (isset($res['error']) && $res['error'] === true) {
            return redirect()->back()->with([
                'error' => $res['message'] ?? 'La operación falló.',
                'details' => $res['details'] ?? [],
            ])->withInput();
            // A: Caso de error
        }

        return redirect()->back()->with('success', 'Usuario creado correctamente.');
        // A: Caso de éxito
    }

    public function delete_user(int $user_id) 
    {
        $res = (new UserService())->deleteUser((int) $user_id);

        if (isset($res['error']) && $res['error'] === true) {
            return response()->json([
                'status' => 'KO',
                'error' => $res['message'] ?? 'La operación falló.',
                'details' => $res['details'] ?? [],
            ]);
            // A: Caso de error
        }

        session()->flash('success', 'El usuario fue eliminado correctamente.');
        
        return response()->json([
            'status' => 'OK',
        ]);
    }

    /* 
        ||--- FUNCIONES AUXILIARES ---||
    */

    // Función auxiliar para formatear datos de usuario
    private function formatUserData(array $data, $is_update = false) : array
    {
        // Si la operación es de actualización, la contraseña es opcional
        $password = $is_update ? 
            (isset($data['password']) && !is_null($data['password']) ?  bcrypt($data['password']) : null) 
            : bcrypt($data['password']);
        // Si la operación es de actualización, el email puede no cambiar
        $email = $is_update ? 
            ($data['email'] ?? null) 
            : $data['email'];
        //dd("data formateable", $data);
        // Construir payload JSON con estructura esperada por backend
        $payload = [
            'email'            => $email,
            'password_hash'     => $password,
            'email_verified'    => true,
            'status'           => 'ACTIVE',
            'provider'          => 'LOCAL',
            'avatar_url'        => $data['avatar_url'] ?? null,
            'first_name'        => $data['first_name'] ?? null,
            'last_name'         => $data['last_name'] ?? null,
            'phone'            => $data['phone'] ?? null,
            'document_id'       => $data['document_id'] ?? null,
            'birth_date'        => $data['birth_date'] ?? null,
            'gender'           => $data['gender'] ?? null,
            'latitude'         => $data['latitude'] ?? null,
            'longitude'        => $data['longitude'] ?? null,
        ];
        // A: "payload" es para la tabla "users"

        if ($is_update && is_null($payload['password_hash'])) {
            unset($payload['password_hash']);
        }
        if ($is_update && is_null($payload['email'])) {
            unset($payload['email']);
        }
        // DOCTOR FIELDS
        $doctor = [
                'license_number'     => $data['license_number'] ?? null,
                'specialties'       => $data['specialties'] ?? [],
                'address'           => $data['address'] ?? null,
                'bio'               => $data['bio'] ?? null,
                'experience_years'    => $data['experience_years'] ?? null,
                'cv'                => $data['cv'] ?? null,
                'consultation_price' => $data['consultation_price'] ?? null,
                'cbu'               => $data['cbu'] ?? null,
                'bank'              => $data['bank'] ?? null,
                'patients_count'     => $data['patients_count'] ?? 0,
                'ratings_count'      => $data['ratings_count'] ?? 0,
                'ratings_sum'        => $data['ratings_sum'] ?? 0,
                'slot_minutes'    => $data['slot_minutes'] ?? 30,
                'payout_accrued' => '0.00',
                'availability' => 'OFFLINE',
        ];
        
        // PATIENT FIELDS
        $patient = [
            'insurance'       => $data['insurance'] ?? null,
            'affiliate_number' => $data['affiliate_number'] ?? null,
        ];

        // ROLES FIELD
        $roles = $data['role'];

        return [
            $payload, 
            $roles, 
            $patient, 
            $doctor
        ];
    }
}
