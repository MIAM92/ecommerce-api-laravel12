<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="API de E-Commerce Segura",
 *     version="1.0.0",
 *     description="Documentación profesional de los endpoints de la tienda en línea con Laravel 12 y JWT"
 * )
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Servidor de Desarrollo Local"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Introduce tu token JWT en el formato: Bearer {token}"
 * )
 */
class SwaggerConfig
{
    // Este archivo solo sirve para almacenar los metadatos globales de OpenAPI
}
