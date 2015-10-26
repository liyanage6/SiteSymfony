<?php

namespace NL\PlatformBundle\DoctrineListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use NL\PlatformBundle\Entity\Application;

class ApplicationNotification
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // On veut envoyer un mail que pour les entités Applications
        if (!$entity instanceof Application)
        {
            return;
        }

        $message = new \Swift_Message(
            'Nouvelle candidature',
            'Vous avez reçu une nouvelle candidature.'
        );

        $message
            ->addTo($entity->getAdvert()->getAuthor()) // Ici bien sur il faudrait un attribut "email", j'utilise "author" à la place
            ->addFrom('admin@votresite.com')
            ;

        $this->mailer->send($message);
    }
}