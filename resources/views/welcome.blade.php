<!-- resources/views/welcome.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Bienvenido a la aplicación JC</h1>

    @if(isset($characters) && count($characters) > 0)
        <div class="row">
            @foreach($characters as $character)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="{{ $character['image'] }}" class="card-img-top" alt="{{ $character['name'] }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $character['name'] }}</h5>

                            <!-- Agrega más detalles según la estructura de la respuesta de la API -->
                            @foreach($character['details'] as $key => $value)
                                @if (!is_array($value))
                                    <p class="card-text"><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        <div class="alert alert-info" role="alert">
            No hay personajes disponibles.
        </div>
    @endif
</div>
@endsection
