<?php

    namespace App\Services\Telegram;

    use Illuminate\Support\Facades\Http;

    final class TelegramBotApi
    {
        public const HOST = 'https://api.telegram.org/bot';
        public static function sendMessage(string $token, int $chatId, string $message): bool
        {
            try {
                $response = Http::get(static::HOST . $token . '/sendMessage', [
                    'chat_id' => $chatId,
                    'text' => $message,
                ]);

                if ($response->status() == 200) {
                    return true;
                }

            } catch (\Exception $exception) {

            }
            return false;
        }
    }
