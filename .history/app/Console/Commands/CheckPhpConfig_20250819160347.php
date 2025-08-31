<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckPhpConfig extends Command
{
    protected $signature = 'check:php-config';
    protected $description = 'Check current PHP configuration for uploads';

    public function handle()
    {
        $this->info('Current PHP Configuration:');
        $this->line('upload_max_filesize: ' . ini_get('upload_max_filesize'));
        $this->line('post_max_size: ' . ini_get('post_max_size'));
        $this->line('memory_limit: ' . ini_get('memory_limit'));
        $this->line('max_execution_time: ' . ini_get('max_execution_time'));
        
        $this->info('');
        $this->info('Converted to bytes:');
        $this->line('upload_max_filesize: ' . $this->convertToBytes(ini_get('upload_max_filesize')) . ' bytes');
        $this->line('post_max_size: ' . $this->convertToBytes(ini_get('post_max_size')) . ' bytes');
        
        return 0;
    }
    
    private function convertToBytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = (int)$val;
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }
}
