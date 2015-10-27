<?php

namespace NL\PlatformBundle\Controller;

use NL\PlatformBundle\Entity\Advert;
use NL\PlatformBundle\Entity\AdvertSkill;
use NL\PlatformBundle\Entity\Application;
use NL\PlatformBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
    public function menuAction($limit)
    {
        $allAdvert = $this->getDoctrine()
            ->getManager()
            ->getRepository('NLPlatformBundle:Advert')
            ->findBy(
                array(),                    // Pas de critère
                array('date' => 'desc'),    // On trie par date décroissante
                $limit,                     // On sélectionne la $limit annonces
                0                           // A partir du premier
            );

        return $this->render('NLPlatformBundle:Advert:menu.html.twig', array(
    // Tout l'intérêt est ici : le contrôleur passe
    // les variables nécessaires au template !
            'allAdvert' => $allAdvert
        ));
    }
    public function editAction($id, Request $request)
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // On récupère l'entité correspondant à l'id $id
        $advert = $em->getRepository("NLPlatformBundle:Advert")->find($id);

        // Si l'annonce n'existe pas, on affiche une erreur 404
        if( null === $advert) {
            throw $this->createNotFoundException("L'anonce d'id ".$id." n'existe pas.");
        }

        // Ici, on s'occupera de la création et de la gestion du formulaire

        return $this->render('NLPlatformBundle:Advert:edit.html.twig', array(
            'advert' => $advert,
        ));
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository("NLPlatformBundle:Advert")->find($id);

        if( null === $advert) {
            throw $this->createNotFoundException("L'anonce d'id ".$id." n'existe pas.");
        }

        if($request->isMethod('POST')) {
            // Si la requête est en POST, on delete l'article

            $request->getSession()->getFlashBag()->add('info', 'Annonce bien supprimée.');

            // Puis on redirige vers l'accueil
            return $this->redirect($this->generateUrl('nl_platform_home'));
        }

        // Si la requete est en GET, on affiche une page de confirmation avant de delete
        return $this->render('NLPlatformBundle:Advert:delete.html.twig', array(
            'advert' => $advert
        ));
    }

    public function addAction(Request $request)
    {
        // La gestion d'un formulaire est particulière, mais l'idée est la suivante :
        // Si la requête est en POST, c'est que le visiteur a soumis le formulaire

        if ($request->isMethod('POST')) {
            // Ici, on s'occupera de la création et de la gestion du formulaire

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // Puis on redirige vers la page de visualisation de cettte annonce
            return $this->redirect($this->generateUrl('nl_platform_view', array('id' => 1)));
        }

        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('NLPlatformBundle:Advert:add.html.twig');
    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em
            ->getRepository('NLPlatformBundle:Advert')
            ->find($id)
        ;

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // On récupère maintenant la liste des AdvertSkill
        $listAdvertSkills = $em
            ->getRepository('NLPlatformBundle:AdvertSkill')
            ->findBy(array('advert' => $advert))
        ;

        return $this->render('NLPlatformBundle:Advert:view.html.twig',array(
            'advert' => $advert,
            'listAdvertSkills' => $listAdvertSkills
        ));
    }
    public function indexAction($page)
    {
        if($page<1)
        {
            throw new NotFoundHttpException('Page '.$page.' inexistante.');
        }

        // Pour récupérer la liste de toutes les annonces : on utilise findAll()
        $allAdvert = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('NLPlatformBundle:Advert')
            ->getAdverts()
        ;

        return $this->render('NLPlatformBundle:Advert:index.html.twig', array(
            'listAdvert' => $allAdvert
        ));
    }
}
