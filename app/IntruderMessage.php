<?php

namespace App;

class IntruderMessage
{
    public static array $emojiList = ['😠', '😡', '😤'];

    public static function getDefaultMessages(): array
    {
        return [
            "Your malevolent nature is unmistakable.",
            "Your malicious intent is clear.",
            "Your actions reek of malevolence.",
            "Your malevolent essence is undeniable.",
            "Your nefarious motives are evident. You cannot disconnect other users' providers.",
            "Your conduct exudes malevolence. You cannot disconnect other users' providers.",
        ];
    }

    public function make(string $message): string
    {
       $addition = $this->randomMessage();
       $emoji = $this->randomEmoji();
        return rand(0, 1)
            ? $message . $addition . $emoji
            : $addition . $emoji . $message;

    }

    public function randomEmoji()
    {
        return self::$emojiList[array_rand(self::$emojiList)];
    }

    public function randomMessage(): string
    {
        $messages = self::getDefaultMessages();
        return $messages[array_rand($messages)];
    }
}
