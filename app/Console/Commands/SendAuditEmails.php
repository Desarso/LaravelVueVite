<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Reports\ReportChecklistRepository;

class SendAuditEmails extends Command
{
    protected $signature = 'command:send_audit_emails';

    protected $description = 'Comando que envía correos electrónicos a los departamentos con base en las auditorías realizadas';

    protected $reportChecklistRepository;  

    public function __construct()
    {
        parent::__construct();
        $this->reportChecklistRepository = new ReportChecklistRepository;
    }

    public function handle()
    {
        $this->reportChecklistRepository->sendEmailAudit();
    }
}
