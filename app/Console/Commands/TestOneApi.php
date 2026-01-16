<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OneApiService;

class TestOneApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oneapi:test {recipient} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test OneAPI integration by sending a message';

    /**
     * Execute the console command.
     */
    public function handle(OneApiService $api)
    {
        $recipient = $this->argument('recipient');
        $message = $this->argument('message');

        $this->info("Sending message to {$recipient}...");

        $result = $api->sendMessage($recipient, $message);

        if ($result['success']) {
            $this->info('Message sent successfully!');
            $this->table(['Key', 'Value'], collect($result['data'])->map(fn($v, $k) => [$k, is_array($v) ? json_encode($v) : $v]));
        } else {
            $this->error('Failed to send message.');
            $this->error('Error: ' . $result['error']);
        }
    }
}
