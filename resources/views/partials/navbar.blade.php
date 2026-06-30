<div class="bg-white shadow p-4 flex justify-between">

    <h1 class="font-bold text-lg">Panel</h1>

    <div>
        {{ auth()->user()->name }}

        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button class="text-red-500 ml-4">Cerrar sesión</button>
        </form>
    </div>

</div>