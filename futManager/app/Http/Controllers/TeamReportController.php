<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class TeamReportController extends Controller
{
    public function download(Team $team): Response
    {
        $team->load([
            'players' => function ($query) {
                $query->orderByRaw('CASE WHEN number IS NULL THEN 1 ELSE 0 END, number ASC');
            }
        ]);

        $pdf = Pdf::loadView('reports.team-pdf', compact('team'));

        return $pdf->download('equipo-' . $team->id . '.pdf');
    }
}
