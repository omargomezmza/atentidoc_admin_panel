@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <x-dynamic-form
        title="Crear Nuevo Usuario"
        subtitle="Complete el formulario para registrar un nuevo usuario en el sistema"
        action="#"
        method="POST"
        submitText="Crear Usuario"
        submitColor="teal"
        cancelText="Cancelar"
        :cancelRoute="'#'"
        :fields="[
            [
                'type' => 'text',
                'name' => 'name',
                'label' => 'Nombre Completo',
                'placeholder' => 'Ingrese el nombre completo',
                'required' => true,
                'helperText' => 'Nombre y apellido del usuario',
            ],
            [
                'type' => 'email',
                'name' => 'email',
                'label' => 'Correo Electrónico',
                'placeholder' => 'correo@example.com',
                'required' => true,
            ],
            [
                'type' => 'password',
                'name' => 'password',
                'label' => 'Contraseña',
                'placeholder' => '••••••••',
                'required' => true,
                'helperText' => 'Mínimo 8 caracteres, incluya mayúsculas, minúsculas y números',
            ],
            [
                'type' => 'password',
                'name' => 'password_confirmation',
                'label' => 'Confirmar Contraseña',
                'placeholder' => '••••••••',
                'required' => true,
            ],
            [
                'type' => 'select',
                'name' => 'role',
                'label' => 'Rol del Usuario',
                'required' => true,
                'options' => [
                    'admin' => 'Administrador',
                    'profesional' => 'Profesional de Salud',
                    'paciente' => 'Paciente',
                ],
                'action' => 'role = $event.target.value; isAdmin = (role === \'admin\'); isProfessional = (role === \'profesional\')',
            ],
            [
                'type' => 'text',
                'name' => 'department',
                'label' => 'Departamento',
                'placeholder' => 'Ej: Recursos Humanos',
                'condition' => 'isAdmin',
                'conditionDefault' => false,
                'helperText' => 'Solo visible para administradores',
            ],
            [
                'type' => 'group',
                'groupTitle' => 'Información Profesional',
                'groupSubtitle' => 'Datos adicionales para profesionales de salud',
                'condition' => 'isProfessional',
                'conditionDefault' => false,
                'fields' => [
                    [
                        'type' => 'text',
                        'name' => 'license_number',
                        'label' => 'Número de Licencia',
                        'placeholder' => 'MP-12345',
                        'required' => true,
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
                        'type' => 'select-2',
                        'name' => 'specialty',
                        'label' => 'Especialidad',
                        'required' => true,
                        'optValue' => 'id',
                        'optLabel' => 'name',
                        'options' => $specialties
                    ],
                    [
                        'type' => 'number',
                        'name' => 'years_experience',
                        'label' => 'Años de Experiencia',
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
            ],
            [
                'type' => 'textarea',
                'name' => 'bio',
                'label' => 'Biografía',
                'placeholder' => 'Escriba una breve biografía...',
                'rows' => 4,
                'helperText' => 'Máximo 500 caracteres',
            ],
            [
                'type' => 'file',
                'name' => 'avatar',
                'label' => 'Foto de Perfil',
                'accept' => 'image/*',
                'helperText' => 'Formatos permitidos: JPG, PNG. Máximo 2MB',
            ],
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
        ]"
    />
</div>
@endsection