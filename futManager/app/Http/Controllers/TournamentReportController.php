<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class TournamentReportController extends Controller
{
    public function downloadSchedule(Tournament $tournament): Response
    {
        $tournament->load(['matches.participants.team', 'matches.field']);

        $matches = $tournament->matches->sortBy('scheduled_at');

        $pdf = Pdf::loadView('reports.schedule-pdf', compact('tournament', 'matches'));

        return $pdf->download('torneo-' . $tournament->id . '-calendario.pdf');
    }
}
