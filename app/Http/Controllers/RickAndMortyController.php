<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Pagination\LengthAwarePaginator;

class RickAndMortyController extends Controller
{
    public function getData()
    {
        try {
            // Configuración para desactivar la verificación SSL (para desarrollo)
            $client = new Client([
                'verify' => false,
            ]);

            // Realizar una solicitud GET a la API de Rick and Morty
            $response = $client->request('GET', 'https://rickandmortyapi.com/api/character');

            // Decodificar la respuesta JSON
            $characters = json_decode($response->getBody(), true)['results'];

            // Ordenar los personajes alfabéticamente por el campo 'name'
            usort($characters, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });


            // Obtener detalles para cada personaje
            foreach ($characters as &$character) {
                $detailsResponse = $client->request('GET', 'https://rickandmortyapi.com/api/character/' . $character['id']);
                $characterDetails = json_decode($detailsResponse->getBody(), true);
                $character['details'] = $characterDetails;
            }

            // Paginar los resultados
            $perPage = 10; // El número de personajes por página
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = array_slice($characters, ($currentPage - 1) * $perPage, $perPage);
            $characters = new LengthAwarePaginator($currentItems, count($characters), $perPage);

            // Renderizar la vista welcome.blade.php con los datos ordenados y detalles paginados
            return view('welcome', compact('characters'));


            // Renderizar la vista welcome.blade.php con los datos ordenados y detalles
            return view('welcome', compact('characters'));
        } catch (GuzzleException $e) {
            // Manejar errores de solicitud
            return view('welcome', ['error' => 'Error al obtener datos de la API']);
        }
    }


}
