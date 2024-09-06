<?php

    namespace Services\Telegram;

    use Illuminate\Support\Facades\Http;
    use Services\Telegram\Exceptions\TelegramBotApiException;

    final class TelegramBotApi
    {
        public const HOST = 'https://api.telegram.org/bot';
        public static function sendMessage(string $token, int $chatId, string $message): bool
        {
            try {
                $response = Http::get(static::HOST . $token . '/sendMessage', [
                    'chat_id' => $chatId,
                    'text' => $message,
                ])->throw()->json();

                return $response['ok'] ?? false;

            } catch (\Throwable $exception) {
                report(new TelegramBotApiException($exception->getMessage()));
                return false;
            }
        }
    }
