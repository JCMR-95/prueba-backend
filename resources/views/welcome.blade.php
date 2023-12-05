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

                            @if(isset($character['details']))
                                @foreach($character['details'] as $key => $value)
                                    @if (!is_array($value))
                                        <p class="card-text"><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</p>
                                    @endif
                                @endforeach
                            @else
                                <p class="card-text"><strong>Detalles no disponibles</strong></p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $characters->links() }} <!-- Agrega esta línea para mostrar la paginación -->
    @else
        <div class="alert alert-info" role="alert">
            Details not available.
        </div>
    @endif
</div>
<script>

    const hideSign = document.querySelectorAll('.w-5.h-5');
    const hideNumbers = document.querySelectorAll('.relative.z-0.inline-flex.shadow-sm.rounded-md');

    hideSign.forEach(element => {
        element.style.display = 'none';
    });

    hideNumbers.forEach(element => {
        element.style.display = 'none';
    });
</script>
@endsection
