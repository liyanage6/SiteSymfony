<?php

namespace NL\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
    public function editAction($id, Request $request)
    {
        // Ici, on récupérera l'annonce correspondante à $id

        // Même mécanisme que pour l'ajout
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

            return $this->redirect($this->generateUrl('nl_platform_view', array('id' => 5)));
        }
        return $this->render('NLPlatformBundle:Advert:edit.html.twig');
    }

    public function deleteAction($id)
    {
        // Ici, on récupérera l'annonce correspondant à $id

        // Ici, on gérera la suppression de l'annonce en question
        return $this->render('NLPlatformBundle:Advert:delete.html.twig');
    }
    public function addAction(Request $request)
    {
        // La gestion d'un formulaire est particulière, mais l'idée est la suivante :
        // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST')) {
            // Ici, on s'occupera de la création et de la gestion du formulaire

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // Puis on redirige vers la page de visualisation de cettte annonce
            return $this->redirect($this->generateUrl('nl_platform_view', array('id' => 5)));
        }

        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('NLPlatformBundle:Advert:add.html.twig');
    }
    public function viewAction($id)
    {
        // Ici, on récupérera l'annonce correspondante à l'id $id
        return $this->render('NLPlatformBundle:Advert:view.html.twig',array('id' => $id));
    }
    public function indexAction($page)
    {
        if($page<1)
        {
            throw new NotFoundHttpException('Page '.$page.' inexistante.');
        }
        // Ici, on récupérera la liste des annonces, puis on la passera au template
        // Mais pour l'instant, on ne fait qu'appeler le template
        return $this->render('NLPlatformBundle:Advert:index.html.twig');
    }
}
