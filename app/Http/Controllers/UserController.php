<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class UserController extends Controller
{
    protected ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function admin_index() 
    {
        $endpoint = "api/admin/read/admins";
        $apiResponse = $this->apiService->makeAuthenticatedRequest(
                'get', $endpoint
            );
        //dd($apiResponse);
        if ($apiResponse['ok']) {
            return view('admin.user.list-admin', ['list' => $apiResponse['data']]);
        }

        else {
            return view('admin.user.list-admin', [
                    'list' => null, 
                    'error' => $apiResponse['message'] ?? 'La operación falló.',
                ]);
        }
    }

    public function patient_index() 
    {
        $endpoint = "api/admin/read/patients";
        $apiResponse = $this->apiService->makeAuthenticatedRequest(
                'get', $endpoint
            );
        //dd($apiResponse);
        if ($apiResponse['ok']) {
            return view('admin.user.list-patient', ['list' => $apiResponse['data']]);
        }
        else {
            return view('admin.user.list-patient', [
                    'list' => null, 
                    'error' => $apiResponse['message'] ?? 'La operación falló.',
                ]);
        }
    }

    public function doctor_index() 
    {
        $endpoint = "api/admin/read/doctors";
        $apiResponse = $this->apiService->makeAuthenticatedRequest(
            'get', $endpoint
        );
        //dd($apiResponse);
        if ($apiResponse['ok']) {
            return view('admin.user.list-doctor', ['list' => $apiResponse['data']]);
        }
        else {
            return view('admin.user.list-doctor', [
                    'list' => null, 
                    'error' => $apiResponse['message'] ?? 'La operación falló.',
                ]);
        }
    }

    public function create_user() 
    {
        $endpoint = "api/search/filters/specialties";
        $apiResponse = $this->apiService->makeAuthenticatedRequest(
                'get', $endpoint
            );
        //dd($apiResponse);
        if ($apiResponse['ok']) {
            return view('admin.user.create-user', ['specialties' => $apiResponse['data']]);
        }
    }

    public function store_user(Request $request)
    {

        $data = $request->all();

        // 1. Construimos el payload JSON con la estructura esperada por tu backend
        $payload = [
            'email'            => $data['email'] ?? null,
            'passwordHash'     => $data['password'] ?? null,
            'role'             => $data['role'] ?? [],
            'emailVerified'    => false,
            'status'           => 'ACTIVE',   // o el valor que corresponda
            'avatarUrl'        => $data['avatar'] ?? null,
            'firstName'        => $data['firstName'] ?? null,
            'lastname'         => $data['lastname'] ?? null,
            'phone'            => $data['phone'] ?? null,
            'documentId'       => $data['documentId'] ?? null,
            'birthDate'        => $data['birthDate'] ?? null,
            'gender'           => $data['gender'] ?? null,
            'latitude'         => $data['latitude'] ?? null,
            'longitude'        => $data['longitude'] ?? null,
        ];

        
        // DOCTOR FIELDS
        $doctor = [
                'licenseNumber'     => $data['licenseNumber'] ?? null,
                'specialties'       => $data['specialties'] ?? [],
                'address'           => $data['address'] ?? null,
                'bio'               => $data['bio'] ?? null,
                'experienceYear'    => $data['experienceYear'] ?? null,
                'cv'                => $data['cv'] ?? null,
                'consultationPrice' => $data['consultationPrice'] ?? null,
                'cbu'               => $data['cbu'] ?? null,
                'bank'              => $data['bank'] ?? null,
                'patientsCount'     => $data['patientsCount'] ?? 0,
                'ratingsCount'      => $data['ratingsCount'] ?? 0,
                'ratingsSum'        => $data['ratingsSum'] ?? 0,
        ];
        // incluir campo 'doctor'
        if (in_array('DOCTOR', $payload['role'])) {
            $payload['doctor'] = $doctor;
        }

        // PATIENT FIELDS
        $patient = [
            'insurance'       => $data['insurance'] ?? null,
            'affiliateNumber' => $data['affiliateNumber'] ?? null,
        ];
        // incluir campo 'patient'
        if (in_array('PATIENT',$payload['role'])) {
            $payload['patient'] = $patient;
        }

        //dd($data, $payload);
        // 2. Hacemos la llamada a la API enviando JSON correctamente formateado
        $endpoint = "api/admin/create/user";

        try {
            $apiResponse = $this->apiService->makeAuthenticatedRequest(
                'post',
                $endpoint,
                $payload,        // Enviamos JSON estructurado
                ['Content-Type' => 'application/json']
            );

            if ($apiResponse['ok']) {
                return redirect()->back()->with('success', 'Usuario creado correctamente.');
            }

            // === Caso de errores específicos (validación remota) ===
            if (!empty($apiResponse['errors'])) {
                //dd("errors", $apiResponse);
                return redirect()
                    ->back()
                    ->withErrors($apiResponse['errors'])
                    ->withInput()
                    ->with([
                        'error' => $apiResponse['message'] ?? 'Error en la operación',
                        'details' => $apiResponse['details'] ?? [],
                        /* 'message' => $apiResponse['details']
                                ? $apiResponse['details']['message']
                                : null,
                        'field' => $apiResponse['details'] 
                                ? $apiResponse['details']['field']
                            : null */
                    ]);
            }

            //dd("errors 2", $apiResponse);
            // === Errores genéricos ===
            return redirect()
                ->back()
                ->with([
                    'error' => $apiResponse['message'] ?? 'La operación falló.',
                    'details' => $apiResponse['details'] ?? [],
                    /* 'message' => $apiResponse['details']
                            ? $apiResponse['details']['message']
                            : null,
                    'field' => $apiResponse['details'] 
                            ? $apiResponse['details']['field']
                        : null */
                ])
                ->withInput();
        }

        catch (\Exception $err) {
            //dd("CATCH", $err->getMessage());
            return redirect()->back()->with([
                'error' => 'La operación falló. Intenelo de nuevo más tarde.',
                'exception' => 'Ocurrió un error: ' . $err->getMessage(),
            ])->withInput();;
        }
    }

    public function show_user(int $user_id) 
    {

        $endpoint_specialties = "api/search/filters/specialties";
        $specialties = $this->apiService->makeAuthenticatedRequest(
                'get', $endpoint_specialties
            );
        $endpoint = "api/admin/read/". ((string) $user_id);
        $apiResponse = $this->apiService->makeAuthenticatedRequest(
                'get', $endpoint
            );
            
        /*  
            $content = []; 
            foreach ($apiResponse as $key => $value) {
                
                if ($key === 'firstName' || $key === 'firstname') {
                    $content['first_name'] = $value;
                }
                else if ($key === 'lastName' || $key === 'lastname') {
                    $content['last_name'] = $value;
                }
                else if ($key === 'doctor' && !is_null($key)) {
                    //$content[$key]['specialty'] = array_shift($value['specialties']);
                    $content[$key] = [
                        ...$value,
                        ...['specialty' => array_shift($value['specialties'])]
                    ];
                }
                
                else {
                    $content[$key] = $value; 
                }
            }
        */
        // dd($content); 

        if ($apiResponse['ok']) {
            return view('admin.user.show-user', [
                'specialties' => $specialties['data'], 
                'user' => $apiResponse['data']
            ]);
        }
    }

    public function edit_user(int $user_id) 
    {
       $endpoint_specialties = "api/search/filters/specialties";
        $specialties = $this->apiService->makeAuthenticatedRequest(
                'get', $endpoint_specialties
            );
        $endpoint = "api/admin/read/". ((string) $user_id);
        $apiResponse = $this->apiService->makeAuthenticatedRequest(
                'get', $endpoint
            );

        /* 
            $content = []; 
            foreach ($apiResponse as $key => $value) {
                
                if ($key === 'firstName' || $key === 'firstname') {
                    $content['first_name'] = $value;
                }

                else if ($key === 'lastName' || $key === 'lastname') {
                    $content['last_name'] = $value;
                }
                
                else {
                    $content[$key] = $value; 
                }
            } 
        */
        //dd($content);
        if ($apiResponse['ok']) {
            return view('admin.user.edit-user', [
                'specialties' => $specialties['data'], 
                'user' => $apiResponse['data']
            ]);
        }
    }

    /* 
        public function update_user(Request $request, int $user_id) 
        {
            $endpoint = "api/admin/update/user/" . ((string) $user_id);
            dd($endpoint, $request->all());
            $apiResponse = $this->apiService->makeAuthenticatedRequest(
                    'patch', $endpoint
                );
            return ['status' => 'OK'];
        } 
    */
    public function update_user(Request $request, int $user_id)
    {
        $data = $request->all();

        // Construir payload JSON con estructura esperada por backend
        $payload = [
            'email'         => $data['email'] ?? null,
            'passwordHash'  => $data['password'] ?? null,
            'role'          => $data['role'] ?? [],
            'emailVerified' => $data['emailVerified'] ?? false,
            'status'        => $data['status'] ?? 'ACTIVE',
            'avatarUrl'     => $data['avatar'] ?? null,
            'firstName'     => $data['firstName'] ?? null,
            'lastname'      => $data['lastname'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'documentId'    => $data['documentId'] ?? null,
            'birthDate'     => $data['birthDate'] ?? null,
            'gender'        => $data['gender'] ?? null,
            'latitude'      => $data['latitude'] ?? null,
            'longitude'     => $data['longitude'] ?? null,

            'doctor' => [
                'licenseNumber'     => $data['licenseNumber'] ?? null,
                'address'           => $data['address'] ?? null,
                'bio'               => $data['bio'] ?? null,
                'experienceYear'    => $data['experienceYear'] ?? null,
                'cv'                => $data['cv'] ?? null,
                'consultationPrice' => $data['consultationPrice'] ?? null,
                'cbu'               => $data['cbu'] ?? null,
                'bank'              => $data['bank'] ?? null,
                'patientsCount'     => $data['patientsCount'] ?? null,
                'ratingsCount'      => $data['ratingsCount'] ?? null,
                'ratingsSum'        => $data['ratingsSum'] ?? null,
            ],

            'patient' => [
                'insurance'       => $data['insurance'] ?? null,
                'affiliateNumber' => $data['affiliateNumber'] ?? null,
            ],
        ];

        $endpoint = "api/admin/update/user/" . $user_id;

        try {
            $apiResponse = $this->apiService->makeAuthenticatedRequest(
                'patch',
                $endpoint,
                $payload,
                ['Content-Type' => 'application/json']
            );

            if ($apiResponse['ok']) {
                return redirect()->back()->with('success', 'Usuario creado correctamente.');
            }

            // === Caso de errores específicos (validación remota) ===
            if (!empty($apiResponse['errors'])) {

                //dd("errors", $apiResponse);
                return redirect()
                    ->back()
                    ->withErrors($apiResponse['errors'])
                    ->withInput()
                    ->with([
                        'error' => $apiResponse['message'] ?? 'La operación falló.',
                        'details' => $apiResponse['details'] ?? [],
                        /* 'message' => $apiResponse['details']
                                ? $apiResponse['details']['message']
                                : null,
                        'field' => $apiResponse['details'] 
                                ? $apiResponse['details']['field']
                            : null */
                    ]);
            }

            // === Errores genéricos ===
            return redirect()
                ->back()
                ->with([
                    'error' => $apiResponse['message'] ?? 'La operación falló.',
                    'details' => $apiResponse['details'] ?? [],
                    /* 'message' => $apiResponse['details']
                            ? $apiResponse['details']['message']
                            : null,
                    'field' => $apiResponse['details'] 
                            ? $apiResponse['details']['field']
                            : null */
                ])
                ->withInput();
        }
        catch (\Exception  $err) {
            return redirect()->back()->with([
                'error' => 'La operación falló. Intenelo de nuevo más tarde.',
                'exception' => 'Ocurrió un error: ' . $err->getMessage()
            ])->withInput();

        }
    }


    public function delete_user(int $user_id) 
    {
        $endpoint = "api/admin/delete/user/" . ((string) $user_id);
        try {
            $apiResponse = $this->apiService->makeAuthenticatedRequest(
                    'delete', $endpoint
                );
            if ($apiResponse['ok']) {
                session()->flash('success', 'El usuario fue eliminado correctamente.');
                return ['status' => 'OK'];
            }
            else {
                return [
                    'status' => 'KO',
                    'msg' => $apiResponse['message']
                ];
            }
        }
        catch (\Exception $err) {
            return ['status' => 'KO'];
        }
    }
}
