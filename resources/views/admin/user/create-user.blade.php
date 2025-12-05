@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <x-dynamic-form
        title="Crear Nuevo Usuario"
        subtitle="Complete el formulario para registrar un nuevo usuario en el sistema"
        action="{{ route('admin.store.user') }}"
        method="POST"
        submitText="Crear Usuario"
        submitColor="teal"
        cancelText="Cancelar"
        :cancelRoute="route('admin.dashboard')"
        :fields="[
            [
                'type' => 'text',
                'name' => 'firstName',
                'label' => 'Nombre',
                'placeholder' => 'Ingrese su nombre',
                'required' => true,
                'helperText' => '',
            ],
            [
                'type' => 'text',
                'name' => 'lastname',
                'label' => 'Apellido',
                'placeholder' => 'Ingrese su apellido',
                'required' => true,
                'helperText' => '',
            ],
            [
                'type' => 'email',
                'name' => 'email',
                'label' => 'Correo Electrónico',
                'placeholder' => 'correo@example.com',
                'required' => true,
            ],
            [
                'type' => 'phone',
                'name' => 'phone',
                'label' => 'Número de teléfono',
                'placeholder' => '123456789',
                'required' => true,
            ],
            [
                'type' => 'number',
                'name' => 'documentId',
                'label' => 'Número de Documento',
                'placeholder' => '123456789',
                'required' => true,
            ],
            [
                'type' => 'date',
                'name' => 'birthDate',
                'label' => 'Fecha de Nacimiento',
                'placeholder' => 'Ingrese su fecha de nacimiento',
                'required' => true,
            ],
            [
                'type' => 'select',
                'name' => 'gender',
                'label' => 'Género',
                'required' => true,
                'options' =>
                    [
                        'MALE' => 'Hombre',
                        'FEMALE' => 'Mujer',
                        'OTHER' => 'Otro',
                    ], 
            ],
            [
                'type' => 'password',
                'name' => 'password',
                'label' => 'Contraseña',
                'placeholder' => '••••••••',
                'required' => true,
                'helperText' => 'Mínimo 8 caracteres, incluya mayúsculas, minúsculas y números',
            ],
            /* 
                [
                    'type' => 'select',
                    'name' => 'role',
                    'label' => 'Rol del Usuario',
                    'required' => true,
                    'options' => [
                        'ADMIN' => 'Administrador',
                        'DOCTOR' => 'Profesional de Salud',
                        'PATIENT' => 'Paciente',
                    ],
                    'action' => 'role = $event.target.value; isAdmin = (role === \'ADMIN\'); isProfessional = (role === \'DOCTOR\'); isPatient = (role === \'PATIENT\')',
                ], 
            */
            [
                'type' => 'checkbox-group',
                'name' => 'role',
                'label' => 'Roles del Usuario',
                'required' => true,
                'options' => [
                    'ADMIN' => 'Administrador',
                    'DOCTOR' => 'Profesional de Salud',
                    'PATIENT' => 'Paciente',
                ],
                'helperText' => 'Puede asignar múltiples roles',
                'action' => '
                    isAdmin = selectedValues.includes(\'ADMIN\'); 
                    isProfessional = selectedValues.includes(\'DOCTOR\'); 
                    isPatient = selectedValues.includes(\'PATIENT\');
                    console.log(\'Roles:\', {isAdmin, isProfessional, isPatient});
                ',
            ],
            [
                'type' => 'group',
                'name' => 'doctor',
                'groupTitle' => 'Información Profesional',
                'groupSubtitle' => 'Datos adicionales para profesionales de salud',
                'condition' => 'isProfessional',
                'conditionDefault' => false,
                'fields' => [
                    [
                        'type' => 'text',
                        'name' => 'licenseNumber',
                        'label' => 'Número de Licencia',
                        'placeholder' => 'MP-12345',
                        'required' => false,
                    ],
                    [
                        'type' => 'text',
                        'name' => 'address',
                        'label' => 'Dirección de Consultorio',
                        'placeholder' => 'Calle n° 123',
                        'required' => false,
                    ],

                    /* 
                        [
                            'type' => 'select-2',
                            'name' => 'specialty',
                            'label' => 'Especialidad',
                            'required' => true,
                            'optValue' => 'id',
                            'optLabel' => 'name',
                            'options' => $specialties
                        ], 
                    */

                    [
                        'type' => 'multi-select',
                        'name' => 'specialties',
                        'label' => 'Especialidades Médicas',
                        'placeholder' => 'Seleccione una o más especialidades...',
                        'required' => false,
                        'optValue' => 'id',
                        'optLabel' => 'name',
                        'options' => $specialties, // Array de objetos/arrays con id y name
                        'helperText' => 'Puede seleccionar múltiples especialidades',
                        'condition' => 'isProfessional',
                        'conditionDefault' => false,
                    ],

                    [
                        'type' => 'textarea',
                        'name' => 'bio',
                        'label' => 'Biografía',
                        'placeholder' => 'Escriba una breve biografía...',
                        'rows' => 4,
                        'helperText' => 'Máximo 500 caracteres',
                    ],
                    /* [
                        'type' => 'select',
                        'name' => 'specialty',
                        'label' => 'Especialidad',
                        'required' => true,
                        'options' =>
                            [
                                'cardiology' => 'Cardiología',
                                'neurology' => 'Neurología',
                                'pediatrics' => 'Pediatría',
                                'general' => 'Medicina General',
                            ], 
                        
                    ], */
                    [
                        'type' => 'number',
                        'name' => 'experienceYear',
                        'label' => 'Años de Experiencia',
                        'min' => 0,
                        'max' => 50,
                    ],
                    [
                        'type' => 'file',
                        'name' => 'cv',
                        'label' => 'Curriculum Vitae (PDF)',
                        'accept' => 'pdf/*',
                        'helperText' => 'Formatos permitidos: PDF.',
                    ],

                    [
                        'type' => 'number',
                        'name' => 'consultationPrice',
                        'label' => 'Precio de Consulta',
                        'min' => 0,
                        'max' => 50,
                    ],
                    [
                        'type' => 'text',
                        'name' => 'cbu',
                        'label' => 'CBU (clave bancaria única)',
                    ],
                    [
                        'type' => 'text',
                        'name' => 'bank',
                        'label' => 'Nombre de Banco',
                    ],
                    [
                        'type' => 'number',
                        'name' => 'patientsCount',
                        'label' => 'Cantidad de Pacientes',
                        'helperText' => 'Cantidad de pacientes atendidos',
                        'min' => 0,
                    ],
                    [
                        'type' => 'number',
                        'name' => 'ratingsCount',
                        'label' => 'Cantidad de votaciones',
                        'helperText' => 'Cantidad de pacientes que otorgaron puntuación',
                        'min' => 0,
                        'max' => 5,
                    ],
                    [
                        'type' => 'number',
                        'name' => 'ratingsSum',
                        'label' => 'Puntuación obtenida',
                        'helperText' => 'Puntuación otorgada a este profesional por parte de los pacientes',
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
            ],
            
            [
                'type' => 'group',
                'name' => 'patient',
                'groupTitle' => 'Información de Paciente',
                'groupSubtitle' => 'Datos adicionales para pacientes',
                'condition' => 'isPatient',
                'conditionDefault' => false,
                'fields' => [
                    [
                        'type' => 'text',
                        'name' => 'insurance',
                        'label' => 'Obra social',
                        'placeholder' => 'Ingrese su obra social',
                        'required' => false,
                    ],
                    [
                        'type' => 'text',
                        'name' => 'affiliateNumber',
                        'label' => 'Número de afiliado',
                        'placeholder' => 'Ingrese su número de afiliado a obra social',
                        'required' => false,
                    ],
                ],
            ],
            [
                'type' => 'file',
                'name' => 'avatar',
                'label' => 'Foto de Perfil',
                'accept' => 'image/*',
                'helperText' => 'Formatos permitidos: JPG, PNG. Máximo 2MB',
            ],
            /* 
                [
                    'type' => 'checkbox',
                    'name' => 'active',
                    'label' => 'Usuario Activo',
                    'helperText' => 'El usuario podrá acceder al sistema',
                    'checked' => true,
                ],
                [
                    'type' => 'checkbox',
                    'name' => 'send_welcome_email',
                    'label' => 'Enviar correo de bienvenida',
                    'checked' => true,
                ], 
            */
        ]"
        :data="['role' => []]"
    />
</div>
@endsection