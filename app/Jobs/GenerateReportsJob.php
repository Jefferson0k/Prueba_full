<?php

namespace App\Jobs;

use App\Models\Branch;
use App\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateReportsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reportType;
    protected $branchId;
    protected $dateFrom;
    protected $dateTo;
    protected $userId;

    public function __construct($reportType, $branchId = null, $dateFrom = null, $dateTo = null, $userId = null)
    {
        $this->reportType = $reportType;
        $this->branchId = $branchId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->userId = $userId;
    }

    public function handle(ReportService $reportService)
    {
        $report = $reportService->generate(
            $this->reportType,
            $this->branchId,
            $this->dateFrom,
            $this->dateTo
        );

        // Guardar reporte en storage
        $filename = "reports/{$this->reportType}_{$this->branchId}_{$this->dateFrom}_{$this->dateTo}.pdf";
        Storage::put($filename, $report);

        // Notificar al usuario que solicitó el reporte
        if ($this->userId) {
            $user = \App\Models\User::find($this->userId);
            if ($user) {
                $user->notify(new \App\Notifications\ReportGenerated($filename));
            }
        }
    }
}