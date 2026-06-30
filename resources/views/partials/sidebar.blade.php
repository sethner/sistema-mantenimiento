@extends('layouts.app')

@section('content')

<h2 class="text-2xl font-bold mb-6">Dashboard</h2>

<div class="grid grid-cols-3 gap-6">

    <div class="bg-white p-4 rounded shadow">
        <h3>Total Equipos</h3>
        <p class="text-2xl font-bold">0</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <h3>Equipos en Falla</h3>
        <p class="text-2xl font-bold">0</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <h3>Mantenimientos</h3>
        <p class="text-2xl font-bold">0</p>
    </div>

</div>

@endsection