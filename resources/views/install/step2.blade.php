@extends('install.layout')

@section('content')
    <h2 class="text-2xl font-bold mb-6 text-center text-text">Configuration de la base de données</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('install.storeStep2') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-text font-medium mb-2">Type de base de données</label>
            <select name="db_connection" id="db_connection"
                    class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo">
                <option value="mysql">MySQL / MariaDB</option>
                <option value="pgsql">PostgreSQL</option>
                <option value="sqlite">SQLite</option>
            </select>
        </div>

        <div id="sql-fields">
            <div>
                <label class="block text-text font-medium mb-2">Hôte</label>
                <input type="text" name="db_host" id="db_host" value="127.0.0.1"
                       class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo">
            </div>

            <div>
                <label class="block text-text font-medium mb-2">Port</label>
                <input type="number" name="db_port" id="db_port" value="3306"
                       class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo">
            </div>

            <div>
                <label class="block text-text font-medium mb-2">Nom de la base de données</label>
                <input type="text" name="db_database" placeholder="Nom de la base"
                       class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo">
            </div>

            <div>
                <label class="block text-text font-medium mb-2">Utilisateur</label>
                <input type="text" name="db_username" value="root"
                       class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo">
            </div>

            <div>
                <label class="block text-text font-medium mb-2">Mot de passe</label>
                <input type="password" name="db_password" placeholder="Mot de passe"
                       class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo">
            </div>
        </div>

        <div id="sqlite-fields" style="display: none;">
            <div>
                <label class="block text-text font-medium mb-2">Chemin du fichier SQLite</label>
                <input type="text" name="db_database_sqlite" value="database/database.sqlite"
                       class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo">
            </div>
        </div>

        <button type="submit"
                class="w-full bg-primary text-white px-6 py-3 rounded-lg shadow-skeuo hover:bg-blue-600 transition">
            Suivant
        </button>
    </form>

    <script>
        const connectionSelect = document.getElementById('db_connection');
        const sqlFields = document.getElementById('sql-fields');
        const sqliteFields = document.getElementById('sqlite-fields');
        const dbPort = document.getElementById('db_port');

        function updateFormDisplay() {
            const type = connectionSelect.value;

            if (type === 'sqlite') {
                sqlFields.style.display = 'none';
                sqliteFields.style.display = 'block';
            } else {
                sqlFields.style.display = 'block';
                sqliteFields.style.display = 'none';

                if (type === 'mysql') {
                    dbPort.value = '3306';
                } else if (type === 'pgsql') {
                    dbPort.value = '5432';
                }
            }
        }

        connectionSelect.addEventListener('change', updateFormDisplay);
        window.addEventListener('DOMContentLoaded', updateFormDisplay);
    </script>
@endsection
