<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'affiliate_number',
        'card_alias',
        'card_mask',
        'insurance',
        'mp_card_id',
        'mp_customer_id',
    ];
    /* 
        +"id": 26,
        +"address": "Calle Mala 321",
        +"availability": "OFFLINE",
        +"bank": null,
        +"bio": "Medico",
        +"cbu": null,
        +"consultation_price": "50.00",
        +"cv": "cv-davidsandez-dev.pdf",
        +"experience_years": 2,
        +"license_number": "MP-98126573444",
        +"patients_count": 0,
        +"payout_accrued": "0.00",
        +"ratings_count": 0,
        +"ratings_sum": 0,
        +"slot_minutes": 30,
        +"user_id": 43,
        +"birth_date": null,
        +"first_name": null,
        +"gender": null,
        +"last_name": null,
        +"latitude": null,
        +"longitude": null,
        +"phone": null,
    */
    /* 
        +"id": 10,
        +"affiliate_number": "1515323",
        +"card_alias": null,
        +"card_mask": null,
        +"insurance": "NINGUNA",
        +"mp_card_id": null,
        +"mp_customer_id": null,
        +"user_id": 34,
        +"birth_date": null,
        +"document_id": null,
        +"first_name": null,
        +"gender": null,
        +"last_name": null,
        +"latitude": null,
        +"longitude": null,
        +"phone": null,
    */
}
