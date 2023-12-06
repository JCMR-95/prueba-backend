<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Pagination\LengthAwarePaginator;

class RickAndMortyController extends Controller
{
    public function getData(Request $request)
    {
        try {
            // Configuración para desactivar la verificación SSL
            $client = new Client([
                'verify' => false,
            ]);

            // Realizar una solicitud GET a la API de Rick and Morty
            $response = $client->request('GET', 'https://rickandmortyapi.com/api/character');

            // Paginación
            $perPage = 6;

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

                // Omitir el detalle "created"
                if (isset($characterDetails['created'])) {
                    unset($characterDetails['created']);
                }

                $character['details'] = $characterDetails;
            }

            // Verificar si se está aplicando el filtro
            if ($request->all() !== []) {
                if($request->input('statusFilter') != null || $request->input('speciesFilter') != null){
                    $characters = $this->filterCharacters($characters, $request->input('statusFilter'), $request->input('speciesFilter'));
                    $perPage = count($characters);
                }
            }

            // Extraer status y species que serán utilizados para el filtro
            $statusOptions = array_unique(array_column($characters, 'status'));
            $speciesOptions = array_unique(array_column($characters, 'species'));

            // Paginar los resultados
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = array_slice($characters, ($currentPage - 1) * $perPage, $perPage);
            $characters = new LengthAwarePaginator($currentItems, count($characters), $perPage);

            // Renderizar la vista welcome.blade.php con los datos ordenados, detalles paginados, status y species
            return view('welcome', compact('characters', 'statusOptions', 'speciesOptions'));
        } catch (GuzzleException $e) {
            // Manejar errores
            return view('welcome', ['error' => 'Error getting data from API']);
        }
    }

    // Función para filtrar personajes según los datos de los filtros
    private function filterCharacters($characters, $statusFilter, $speciesFilter)
    {
        return collect($characters)->filter(function ($character) use ($statusFilter, $speciesFilter) {
            $statusMatch = !$statusFilter || $character['details']['status'] === $statusFilter;
            $speciesMatch = !$speciesFilter || $character['details']['species'] === $speciesFilter;

            return $statusMatch && $speciesMatch;
        })->toArray();
    }
}
