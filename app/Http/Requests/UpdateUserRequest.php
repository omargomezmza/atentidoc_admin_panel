<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true; // Cambiar a true para permitir la validación
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        // Verificamos si el rol DOCTOR está presente en el array de roles
        $isDoctor = in_array('DOCTOR', $this->input('role', []));

        return [
            // 1. Campos requeridos básicos
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'phone'       => 'required|string',
            'birth_date'  => 'required|date',
            'gender'      => 'required|in:MALE,FEMALE,OTHER',
            
            // 2. Role debe ser un array y tener al menos un elemento
            'role'        => 'required|array|min:1',

            // 3, 4 y 6. Reglas condicionales para DOCTOR
            'license_number' => [
                Rule::requiredIf($isDoctor),
            ],
            'address' => [
                Rule::requiredIf($isDoctor),
            ],
            'consultation_price' => [
                Rule::requiredIf($isDoctor),
            ],
            'experience_years' => [
                Rule::requiredIf($isDoctor),
            ],
            'specialties' => [
                Rule::requiredIf($isDoctor),
                'array',
                $isDoctor ? 'min:1' : '',
            ],
        ];
    }

    /**
     * Mensajes personalizados para cada falla.
     */
    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'unique'   => 'El :attribute ingresado ya se encuentra registrado.',
            'email'    => 'Debe ingresar un correo electrónico válido.',
            'array'    => 'El campo :attribute debe ser una lista.',
            'min'      => [
                'array' => 'Debe seleccionar al menos :min elemento(s).',
                'string' => 'El campo debe tener al menos :min caracteres.',
            ],
            'license_number.required_if' => 'La matrícula es obligatoria para el rol de Doctor.',
            'address.required_if'        => 'La dirección es obligatoria para el rol de Doctor.',
            'consultation_price.required_if' => 'El precio de consulta es obligatorio para el rol de Doctor.',
            'experience_years.required_if'    => 'Los años de experiencia son obligatorios para el rol de Doctor.',
            'specialties.min'            => 'Debe seleccionar al menos una especialidad.',
        ];
    }

    /**
     * Nombres de atributos amigables.
     */
    public function attributes(): array
    {
        return [
            'first_name'     => 'nombre',
            'last_name'      => 'apellido',
            'email'          => 'correo electrónico',
            'document_id'    => 'DNI/Documento',
            'license_number' => 'número de matrícula',
            'specialties'    => 'especialidades',
        ];
    }
}