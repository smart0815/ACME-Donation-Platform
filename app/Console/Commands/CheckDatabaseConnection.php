<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDatabaseConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check PostgreSQL database connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Check connection and PostgreSQL version
            $version = DB::select('SELECT version()');
            $this->info('Connected to PostgreSQL:');
            $this->info($version[0]->version);
            
            // Check if required extensions are installed
            $extensions = DB::select("SELECT extname FROM pg_extension");
            $this->info('Installed extensions:');
            foreach ($extensions as $extension) {
                $this->info('- ' . $extension->extname);
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Database connection failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}