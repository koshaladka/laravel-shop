<?php

    namespace Services\Telegram;

    use Illuminate\Support\Facades\Http;
    use Services\Telegram\Exceptions\TelegramBotApiException;

    class TelegramBotApiFake extends TelegramBotApi
    {
        protected static bool $success = true;

        public function returnTrue(): static
        {
            static::$success = true;

            return $this;
        }

        public function returnFalse(): static
        {
            static::$success = false;

            return $this;
        }

        public static function sendMessage(string $token, int $chatId, string $message): bool
        {
            return static::$success;
        }
    }
