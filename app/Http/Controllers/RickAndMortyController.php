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

            // Extraer status y species
            $statusOptions = array_unique(array_column($characters, 'status'));
            $speciesOptions = array_unique(array_column($characters, 'species'));

            // Paginar los resultados
            $perPage = 6; // El número de personajes por página
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = array_slice($characters, ($currentPage - 1) * $perPage, $perPage);
            $characters = new LengthAwarePaginator($currentItems, count($characters), $perPage);

            // Renderizar la vista welcome.blade.php con los datos ordenados, detalles paginados, status y species
            return view('welcome', compact('characters', 'statusOptions', 'speciesOptions'));
        } catch (GuzzleException $e) {
            // Manejar errores de solicitud
            return view('welcome', ['error' => 'Error al obtener datos de la API']);
        }
    }

    public function getDataFilter(Request $request)
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

            // Obtener detalles para cada personaje
            foreach ($characters as &$character) {
                $detailsResponse = $client->request('GET', 'https://rickandmortyapi.com/api/character/' . $character['id']);
                $characterDetails = json_decode($detailsResponse->getBody(), true);
                $character['details'] = $characterDetails;
            }

            // Filtrar personajes según los datos de los filtros
            $filteredCharacters = $this->filterCharacters($characters, $request->input('statusFilter'), $request->input('speciesFilter'));

            // Extraer status y species de los personajes filtrados
            $statusOptions = array_unique(array_column($filteredCharacters, 'status'));
            $speciesOptions = array_unique(array_column($filteredCharacters, 'species'));

            // Ordenar los personajes alfabéticamente por el campo 'name'
            usort($filteredCharacters, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            // Paginar los resultados
            $perPage = 10; // El número de personajes por página
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = array_slice($filteredCharacters, ($currentPage - 1) * $perPage, $perPage);
            $characters = new LengthAwarePaginator($currentItems, count($filteredCharacters), $perPage);

            // Renderizar la vista welcome.blade.php con los datos ordenados, detalles paginados, status y species
            return view('welcome', compact('characters', 'statusOptions', 'speciesOptions'));
        } catch (GuzzleException $e) {
            // Manejar errores de solicitud
            return view('welcome', ['error' => 'Error al obtener datos de la API']);
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
