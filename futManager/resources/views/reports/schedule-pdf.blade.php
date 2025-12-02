<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Calendario - {{ $tournament->name }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            color: #333;
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
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .status {
            text-transform: capitalize;
        }
    </style>
</head>

<body>
    <h1>{{ $tournament->name }}</h1>

    <div style="margin-bottom: 20px;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; width: 50%; vertical-align: top;">
                    <strong>Categoría:</strong> {{ $tournament->category ?? 'N/A' }}<br>
                    <strong>Formato:</strong> {{ $tournament->format ?? 'N/A' }}<br>
                    <strong>Estado:</strong> <span class="status">{{ $tournament->status }}</span>
                </td>
                <td style="border: none; width: 50%; vertical-align: top;">
                    <strong>Fecha Inicio:</strong> {{ $tournament->start_date->format('d/m/Y') }}<br>
                    <strong>Fecha Fin:</strong>
                    {{ $tournament->end_date ? $tournament->end_date->format('d/m/Y') : 'N/A' }}<br>
                    <strong>Cancha Principal:</strong> {{ $tournament->field->name ?? 'N/A' }}
                </td>
            </tr>
        </table>
        @if($tournament->description)
            <div style="margin-top: 10px; padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd;">
                <strong>Descripción:</strong><br>
                {{ $tournament->description }}
            </div>
        @endif
    </div>

    <h2>Calendario de Partidos</h2>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Local</th>
                <th>Visitante</th>
                <th>Cancha</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matches as $match)
                <tr>
                    <td>{{ $match->scheduled_at->format('d/m/Y') }}</td>
                    <td>{{ $match->scheduled_at->format('H:i') }}</td>
                    <td>
                        @php
                            $home = $match->participants->where('is_home', true)->first();
                        @endphp
                        {{ $home ? $home->team->name : 'TBD' }}
                    </td>
                    <td>
                        @php
                            $away = $match->participants->where('is_home', false)->first();
                        @endphp
                        {{ $away ? $away->team->name : 'TBD' }}
                    </td>
                    <td>{{ $match->field->name }}</td>
                    <td class="status">{{ $match->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>