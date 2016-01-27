<?php

namespace NL\PlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Antiflood extends Constraint
{
    public $message = " Vous avez déjà poster une annonce il y a moins de 30 secondes, merci d'attendre un peu.";

    public function validateBy(){
        return 'nl_platform_antiflood';
    }
}