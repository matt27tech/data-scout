<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use danog\MadelineProto\API;
use danog\MadelineProto\messages;
use Exception;
use Illuminate\Console\Command;

class TgGetUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tg-get-updates{phrases* : The array of phrases}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search for phrases in Telegram messages';
    private API $api;
    private array $data;
    private string $forwardChat = '-902552421';
    private messages|array $messages;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->auth();
        $phrases = $this->argument('phrases');
        $dialogs = $this->api->getDialogs();
        $lastUpTime = Carbon::now()->subMinutes(5)->timestamp;
        foreach ($dialogs as $dialog) {
            $peer = $dialog['_'];
            if ($peer === 'peerUser') {
                $userId = $dialog['user_id'];
                $user = $this->api->getFullInfo($userId);
                $this->data = $this->setData($userId, $user['User']['username'] ?? '');
            } elseif ($peer === 'peerChat') {
                $chatId = '-' . $dialog['chat_id'];
                $chat = $this->api->getFullInfo($chatId);
                $this->data = $this->setData($chatId, $chat['Chat']['title'] ?? '');
            } elseif ($peer === 'peerChannel') {
                $channelId = '-100' . $dialog['channel_id'];
                $channel = $this->api->getFullInfo($channelId);
                $this->data = $this->setData($channelId, $channel['Chat']['title'] ?? '');
            }
            if ($this->data['peer'] != $this->forwardChat) {
                try {
                    $this->messages = $this->api->messages->getHistory([
                        'peer' => $this->data['peer'],
                        'limit' => 100
                    ]);
                } catch (Exception) {
                }
                foreach ($this->messages['messages'] as $message) {
                    if ($message['date'] > $lastUpTime) {
                        foreach ($phrases as $phrase) {
                            if (isset($message['message']) && mb_stripos($message['message'], $phrase) !== false) {
                                $this->forwardToGroup($message, $phrase);
                            }
                        }

                        try {
                            if ($peer === 'peerUser') {
                                $this->api->messages->readHistory(['peer' => $this->data['peer']]);
                            } else {
                                $this->api->channels->readHistory(['channel' => $this->data['peer']]);
                            }
                        } catch (Exception) {
                        }
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    private function auth(): void
    {
        try {
            $settings = [
                'logger' =>
                    [
                        'logger' => 0,
                    ],
            ];
            $this->api = new API('session.madeline', $settings);

            $self = $this->api->get_self();

            if ($self) {
                $this->info("User {$self['first_name']} authorized!");
            } else {
                $phoneNumber = readline("Enter your phone number: ");
                $this->api->phone_login($phoneNumber);
                $authorizationCode = readline("Enter authorization code: ");
                $this->api->complete_phone_login($authorizationCode);
                $this->info("Authorization success!");
            }
        } catch (Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }

    /**
     * @param int $peer
     * @param string $name
     * @return array
     */
    private function setData(int $peer, string $name): array
    {
        return [
            'peer' => $peer,
            'name' => $name,
        ];
    }

    /**
     * @param $message
     * @param $phrase
     * @return void
     */
    private function forwardToGroup($message, $phrase): void
    {
        try {
            $this->api->messages->forwardMessages([
                'from_peer' => $this->data['peer'],
                'id' => [$message['id']],
                'to_peer' => $this->forwardChat,
            ]);
            sleep(1);
            $this->api->messages->sendMessage([
                'peer' => $this->forwardChat,
                'message' => "Message found in "
                    . $this->data['name']
                    . ' Phrase: ' . $phrase
                    . ' --- Date: '
                    . Carbon::createFromTimestamp($message['date'])
                        ->format('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
