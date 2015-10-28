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
        // On créer un objet Advert
        $advert = new Advert();

        // On créer le FormBuilder grace au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder('form', $advert);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('date',       'date')
            ->add('title',      'text')
            ->add('content',    'textarea')
            ->add('author',     'text')
            ->add('published',  'checkbox')
            ->add('save',       'submit')
        ;
        // Pour l'instant, pas de candidatures, catégories, etc., on les gérera plus tard

        //A partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('NLPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView()
        ));
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
            throw $this->createNotFoundException('Page '.$page.' inexistante.');
        }

        // On peut fixe le nombre d'annonces par page à 2
        // Mais bien sur il faudrait utiliser un paramètre, et y accéder via
        // $this->container->getParameter('nb_per_page')
        $nbPerPage = $this->container->getParameter('nb_per_page');

        // On récupére notre Paginator
        $allAdvert = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('NLPlatformBundle:Advert')
            ->getAdverts($page, $nbPerPage)
        ;

        // On calcule le nbr total de pages grace au count($allAdverts) qui retourne le nombre total d'annonces
        //ceil() arrondi au chiffre supèrieur
        $nbPages = ceil(count($allAdvert)/$nbPerPage);

        // Si la page n'existe pas, on retourne une 404
        if ($page > $nbPages) {
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        }

        // On donne toutes les informations nécessaires à la vue
        return $this->render('NLPlatformBundle:Advert:index.html.twig', array(
            'listAdvert' => $allAdvert,
            'nbPages' => $nbPages,
            'page' => $page
        ));
    }
}
