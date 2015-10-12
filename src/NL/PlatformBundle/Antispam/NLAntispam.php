<?php
// src/NL/PlatformBundle/Antispam/NLAntispam.php

namespace NL\PlatformBundle\Antispam;

class NLAntispam
{
    private $mailer;
    private $locale;
    private $minLength;

    public function _construct($mailer, $locale, $minLength)
    {
        $this->mailer = $mailer;
        $this->locale = $locale;
        $this->minLength = $minLength;
    }
    /**
     * Vérifie si le texte est un spam ou non
     *
     * @param string $text
     * @return bool
     */
    public function isSpam($text)
    {
        return strlen($text) < $this->minLength;
    }
}