<?php
namespace App\Services;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService { 
    
    public function getUsersByRole(string $roleName) : array
    {
        // 1. Buscamos los usuarios que tienen el rol específico en la tabla user_roles
        // 2. Usamos 'with' para traer los datos de las otras tablas en solo 2 o 3 consultas totales
        $users = User::whereHas('roles', function ($query) use ($roleName) {
            $query->where('role', $roleName);
        })
        ->with(['roles', 'patient', 'doctor'])
        ->get();

        // 3. Transformamos la colección al formato de array
        $content = $users->map(function ($user) {
            //$collect = new \stdClass();
            $collect = [];
            $collect['id'] = $user->id;
            $collect['email'] = $user->email;
            $collect['first_name'] = $user->first_name;
            $collect['last_name'] = $user->last_name;
            $collect['phone'] = $user->phone;
            $collect['document_id'] = $user->document_id;
            $collect['birth_date'] = $user->birth_date;
            $collect['gender'] = $user->gender;
            $collect['status'] = $user->status;
            $collect['email_verified'] = $user->email_verified;
            $collect['avatar_url'] = $user->avatar_url;
            $collect['created_at'] = $user->created_at;
            
            // Datos de Paciente
            if ($user->patient) {
                $collect['patient'] = [
                    'affiliate_number' => $user->patient->affiliate_number,
                    'card_alias'       => $user->patient->card_alias,
                    'card_mask'        => $user->patient->card_mask,
                    'insurance'        => $user->patient->insurance,
                ];
            }

            // Datos de Doctor
            if ($user->doctor) {
                $collect['doctor'] = [
                    'address'            => $user->doctor->address,
                    'bank'               => $user->doctor->bank,
                    'bio'                => $user->doctor->bio,
                    'cbu'                => $user->doctor->cbu,
                    'consultation_price' => $user->doctor->consultation_price,
                    'cv'                 => $user->doctor->cv,
                    'experience_years'   => $user->doctor->experience_years,
                    'license_number'     => $user->doctor->license_number,
                    'patients_count'     => $user->doctor->patients_count,
                ];
            }

            // Lista de roles (plana)
            $collect['role'] = $user->roles->pluck('role')->toArray();

            return $collect;
        })->toArray();

        // 4. Devolver el array final
        $data =  [
            'content' => $content
        ];
        // TODO: falta paginación y otros metadatos en el array data
        return $data;
    }

    public function getUserById(int $user_id) : array
    {
        $user = User::where('id', $user_id)->with(['roles', 'patient', 'doctor'])->first();//->toArray();
        //dd($user);
        $collect = [];
        $collect['id'] = $user->id;
        $collect['email'] = $user->email;
        $collect['first_name'] = $user->first_name;
        $collect['last_name'] = $user->last_name;
        $collect['phone'] = $user->phone;
        $collect['document_id'] = $user->document_id;
        $collect['birth_date'] = $user->birth_date;
        $collect['gender'] = $user->gender;
        $collect['status'] = $user->status;
        $collect['email_verified'] = $user->email_verified;
        $collect['avatar_url'] = $user->avatar_url;
        $collect['created_at'] = $user->created_at;
        
        // Datos de Paciente
        if ($user->patient) {
            $collect['patient'] = [
                'affiliate_number' => $user->patient->affiliate_number,
                'card_alias'       => $user->patient->card_alias,
                'card_mask'        => $user->patient->card_mask,
                'insurance'        => $user->patient->insurance,
            ];
        }

        // Datos de Doctor
        if ($user->doctor) {
            $collect['doctor'] = [
                'address'            => $user->doctor->address,
                'bank'               => $user->doctor->bank,
                'bio'                => $user->doctor->bio,
                'cbu'                => $user->doctor->cbu,
                'consultation_price' => $user->doctor->consultation_price,
                'cv'                 => $user->doctor->cv,
                'experience_years'   => $user->doctor->experience_years,
                'license_number'     => $user->doctor->license_number,
                'patients_count'     => $user->doctor->patients_count,
            ];
            // Obtener especialidades del doctor
            $specialties = DB::table('doctor_specialty')->where('doctor_id', $user->doctor->id)
                ->get()->map(fn ($specialty) => Specialty::find($specialty->specialty_id))->toArray();
            $collect['doctor']['specialties'] = $specialties;
        }

        // Lista de roles (plana)
        $collect['role'] = $user->roles->pluck('role')->toArray();

        //dd($collect);
        return $collect;
    }

    public function createUser(array $userData, array $rolesData, array|null $patientData = null, array|null $doctorData = null) : array
    {
        $res = []; // Resultado de la creación

        // Inicio de Transacción de Base de datos
        DB::beginTransaction();

        $user = null; $patient = null; $doctor = null; $roleAdmin = null;

        try {

            // Crear el usuario
            DB::table('users')->insert($userData);
            $user = User::where('email', $userData['email'])->first();

            // Crear datos de paciente si se proporcionan
            if (in_array('PATIENT', $rolesData)) {
                $patientData['user_id'] = $user->id;
                DB::table('patients')->insert($patientData);
                $patient = Patient::where('user_id', $user->id)->first();
                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role' => 'PATIENT',
                ]);
            }

            // Crear datos de doctor si se proporcionan
            if (in_array('DOCTOR', $rolesData)) {
                $doctorData['user_id'] = $user->id;
                $specialties = $doctorData['specialties'];
                unset($doctorData['specialties']);
                DB::table('doctors')->insert($doctorData);
                $doctor = Doctor::where('user_id', $user->id)->first();

                foreach ($specialties as $specialty) {
                    //dd($key . ' => ' . $value);
                    DB::table('doctor_specialty')->insert([
                        'doctor_id' => $doctor->id,
                        'specialty_id' => $specialty,
                    ]);
                }
                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role' => 'DOCTOR',
                ]);
            }

            // Asignar rol de Admin si se proporciona
            if (in_array('ADMIN', $rolesData)) {
                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role' => 'ADMIN',
                ]);
            }
            
            // Confirmar la transacción
            DB::commit();   
        }
        catch (\Exception $e) {
            // En caso de error, revertir la transacción
            DB::rollBack();
            // Manejar el error (puede ser logging, rethrow, etc.)
            //throw $e;
            $res['message'] = 'Error creando usuario: ' . $e->getMessage();
            $res['error'] = true;
            return $res;
        }

        return $res;
    }

    public function updateUser(int $user_id, array $userData, array $rolesData, array|null $patientData = null, array|null $doctorData = null) : array
    {
        $res = []; // Resultado de la actualización
        
        // Validar unicidad de email de usuario
        $checkErrors = $this->checkUserData($user_id, $userData);
        if ($checkErrors['hasError']) {
            $res['message'] = 'Error en los datos del usuario.';
            $res['error'] = true;
            $res['details'] = $checkErrors['errors'];
            return $res;
        }
        // Validar unicidad de license_number de doctor si aplica
        if (in_array('DOCTOR', $rolesData)) {
            $checkDoctorErrors = $this->checkDoctorData($user_id, $doctorData);
            if ($checkDoctorErrors['hasError']) {
                $res['message'] = 'Error en los datos del doctor.';
                $res['error'] = true;
                $res['details'] = $checkDoctorErrors['errors'];
                return $res;
            }
        }

        // Inicio de Transacción de Base de datos
        DB::beginTransaction();

        $user = null; $patient = null; $doctor = null; $roleAdmin = null;

        try {
            DB::table('users')->where('id', $user_id)->update($userData);
            $user = User::find($user_id);
            
            // Eliminar roles anteriores
            DB::table('user_roles')->where('user_id', $user_id)->delete();
            
            // Crear datos de paciente si se proporcionan
            if (in_array('PATIENT', $rolesData)) {
                DB::table('patients')->where('user_id', $user_id)->update($patientData);
                //$patient = Patient::where('user_id', $user->id)->first();
                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role' => 'PATIENT',
                ]);
            } 
            else {
                $patient = Patient::where('user_id', $user->id)->first();
                if ($patient) {
                    // Eliminar datos de paciente si el rol ya no está presente
                    DB::table('patients')->where('user_id', $user_id)->delete();
                }
            }

            // Crear datos de doctor si se proporcionan
            if (in_array('DOCTOR', $rolesData)) {
                $specialties = $doctorData['specialties'];
                unset($doctorData['specialties']);
                
                DB::table('doctors')->where('user_id', $user_id)->update($doctorData);
                $doctor = Doctor::where('user_id', $user->id)->first();

                // Borrar especialidades anteriores 
                DB::table('doctor_specialty')->where('doctor_id', $doctor->id)->delete();
                
                // Asignar nuevas especialidades
                foreach ($specialties as $specialty) {
                    DB::table('doctor_specialty')->insert([
                        'doctor_id' => $doctor->id,
                        'specialty_id' => $specialty,
                    ]);
                }

                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role' => 'DOCTOR',
                ]);
            }
            else {
                $doctor = Doctor::where('user_id', $user->id)->first();
                if ($doctor) {
                    // Eliminar datos de doctor si el rol ya no está presente
                    DB::table('doctors')->where('user_id', $user_id)->delete();
                }
            }

            // Asignar rol de Admin si se proporciona
            if (in_array('ADMIN', $rolesData)) {
                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role' => 'ADMIN',
                ]);
            }
            
            // Confirmar la transacción
            DB::commit(); 
        }
        catch (\Exception $e) {
            // En caso de error, revertir la transacción
            DB::rollBack();
            // Manejar el error (puede ser logging, rethrow, etc.)
            $res['message'] = 'Error actualizando usuario: ' . $e->getMessage();
            $res['error'] = true;
            return $res;
        }

        return $res;
    }

    public function deleteUser(int $user_id) : array
    {
        $res = []; // Resultado de la eliminación

        // Inicio de Transacción de Base de datos
        DB::beginTransaction();

        try {
            // Eliminar roles
            DB::table('user_roles')->where('user_id', $user_id)->delete();

            // Eliminar datos de paciente
            DB::table('patients')->where('user_id', $user_id)->delete();

            // Eliminar datos de doctor
            DB::table('doctors')->where('user_id', $user_id)->delete();

            // Eliminar usuario
            DB::table('users')->where('id', $user_id)->delete();

            // Confirmar la transacción
            DB::commit(); 
        }
        catch (\Exception $e) {
            // En caso de error, revertir la transacción
            DB::rollBack();
            // Manejar el error (puede ser logging, rethrow, etc.)
            $res['message'] = 'Error eliminando usuario: ' . $e->getMessage();
            $res['error'] = true;
            return $res;
        }

        return $res;
    }

    private function checkUserData(int $user_id, array $userData) : array
    {
        $user = User::find($user_id);
        $currentEmail = $user->email; 
        $newEmail = $userData['email'];

        $hasError = false;
        $errors = [];
        if ($currentEmail !== $newEmail) {
            // Aquí podríamos agregar lógica para verificar si el nuevo email ya existe
            $usersWithNewEmail = User::where('email', $newEmail)->count();
            if ($usersWithNewEmail > 0) {
                $hasError = true;
                
                $errors[] = [
                    'field' => 'Email', 
                    'message' => 'El nuevo email ya está en uso por otro usuario.'
                ];
            }
        }
        return [
            'hasError' => $hasError,
            'errors'   => $errors,
        ];
    }

    private function checkDoctorData(int $user_id, array $doctorData) : array
    {
        $doctor = Doctor::where('user_id', $user_id)->first();
        $currentLicenseNumber = $doctor->license_number; 
        $newLicenseNumber = $doctorData['license_number'];

        $hasError = false;
        $errors = [];
        if ($currentLicenseNumber !== $newLicenseNumber) {
            $doctorsWithNewLicenseNumber = Doctor::where('license_number', $newLicenseNumber)->count();
            if ($doctorsWithNewLicenseNumber > 0) {
                $hasError = true;
                $errors[] = [
                    'field' => 'Número de Matrícula', 
                    'message' => 'El nuevo número de matrícula ya está en uso por otro doctor.'
                ];
            }
        }
        return [
            'hasError' => $hasError,
            'errors'   => $errors,
        ];
    }
}