<?php

namespace NL\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NLUserBundle extends Bundle
{
    public function getParent ()
    {
        return 'FOSUserBundle';
    }
}
