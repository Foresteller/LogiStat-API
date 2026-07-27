<?php

namespace App\Http\Controllers\Api;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "Production-ready REST API для управления складскими остатками",
    title: "Logistat B2B Logistics API",
    contact: new OA\Contact(email: "developer@logistat.local")
)]
#[OA\Server(
    url: "http://localhost:8080",
    description: "Local Docker Environment"
)]
class OpenApi
{

}
