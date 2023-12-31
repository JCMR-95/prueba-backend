
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Backend Test</h1>

    <!-- Filtros -->
    <form action="{{ route('getData') }}" method="GET" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <label for="statusFilter" class="form-label">Status:</label>
                <select id="statusFilter" name="statusFilter" class="form-select">
                    <option value="">All</option>
                    @foreach($statusOptions as $status)
                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="speciesFilter" class="form-label">Species:</label>
                <select id="speciesFilter" name="speciesFilter" class="form-select">
                    <option value="">All</option>
                    @foreach($speciesOptions as $species)
                        <option value="{{ $species }}">{{ ucfirst($species) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary mt-2">Search</button>
            </div>
        </div>
    </form>

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
                                        <p class="card-text"><strong>{{ ucfirst($key) }}:</strong> {{ $value ?: '-' }}</p>
                                    @endif
                                @endforeach
                            @else
                                <p class="card-text"><strong>Details not available.</strong></p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $characters->links() }}
    @else
        <div class="alert alert-info" role="alert">
            Details not available.
        </div>
    @endif
</div>
<script>

    // Se arreglan algunos estilos de la Paginación
    const hideSign = document.querySelectorAll('.w-5.h-5');
    const pages = document.querySelectorAll('.text-sm.text-gray-700.leading-5');

    hideSign.forEach(element => {
        element.style.display = 'none';
    });

    pages.forEach(element => {
        element.style.paddingTop = '10px';
    });

</script>
@endsection
