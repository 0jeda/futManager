<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Equipo - {{ $team->name }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .team-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .team-logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .player-photo {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <div class="team-header">
        @if($team->logo_path)
            <img src="{{ public_path('storage/' . $team->logo_path) }}" class="team-logo" alt="Logo">
        @endif
        <h1>{{ $team->name }}</h1>
        <p>
            <strong>Entrenador:</strong> {{ $team->coach_name ?? 'N/A' }}<br>
            <strong>Contacto:</strong> {{ $team->contact_email ?? 'N/A' }}
        </p>
    </div>

    <h2>Jugadores</h2>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">Foto</th>
                <th>Nombre</th>
                <th>Posición</th>
                <th>Número</th>
                <th>CURP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($team->players as $player)
                <tr>
                    <td style="text-align: center;">
                        @if($player->photo_path)
                            <img src="{{ public_path('storage/' . $player->photo_path) }}" class="player-photo" alt="Foto">
                        @else
                            <span>-</span>
                        @endif
                    </td>
                    <td>{{ $player->first_name }} {{ $player->last_name }}</td>
                    <td>{{ $player->position }}</td>
                    <td>{{ $player->number ?? '-' }}</td>
                    <td>{{ $player->curp ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>