<?php

namespace App\Service;

class BadWordFilter
{
    private array $badWords;

    public function __construct(array $badWords)
    {
        $this->badWords = $badWords;
    }

    public function hasBadWords(string $text): bool
    {
        // Convertir le texte et les mots interdits en minuscules pour une vérification insensible à la casse
        $textLower = strtolower($text);
        
        foreach ($this->badWords as $word) {
            if (str_contains($textLower, strtolower($word))) {
                return true;
            }
        }

        return false;
    }
}