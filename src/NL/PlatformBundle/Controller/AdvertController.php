<?php

namespace NL\PlatformBundle\Controller;

use NL\PlatformBundle\Entity\Advert;
use NL\PlatformBundle\Entity\AdvertSkill;
use NL\PlatformBundle\Entity\Application;
use NL\PlatformBundle\Entity\Image;
use NL\PlatformBundle\Form\AdvertType;
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
        $formBuilder = $this->get('form.factory')->createBuilder('form', $advert);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('date',       'date')
            ->add('title',      'text')
            ->add('content',    'textarea')
            ->add('author',     'text')
            ->add('published',  'checkbox', array(
                'required' => false
            ))
            ->add('save',       'submit')
        ;

        $form = $formBuilder->getForm();

        // On fait le lien Requete <-> Formulaire
        // A partir de maintenant, la valeur $advert contient les valeurs entrées dans le formulaire par le visiteur
        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes
        // (Nous verrons la validation des objets en détail dans le prochain chapitre)
        if ($form->isValid())
            // On enregistre notre objet $advert dans la BDD, par exemple
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créer
            return $this->redirect($this->generateUrl('nl_platform_view', array('id' => $advert->getId())));
        }

        return $this->render('NLPlatformBundle:Advert:edit.html.twig', array(
            'advert' => $advert,
            'form' => $form->createView(),
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

        // Ici, on préremplit avec la date d'aujourd'hui, par exemple
        // Cette date sera donc préaffichée dans le formulaire, cela facilite le travail de l'utilisateur
        $advert->setDate(new \Datetime());

        // On créer le FormBuilder grace au service form factory
        $form = $this->createForm(new AdvertType(), $advert);

        if ($form->handleRequest($request)->isValid())
            // On enregistre notre objet $advert dans la BDD, par exemple
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créer
            return $this->redirect($this->generateUrl('nl_platform_view', array('id' => $advert->getId())));
        }

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
    public function indexAction()
    {

        // On récupére notre Paginator
        $allAdvert = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('NLPlatformBundle:Advert')
            ->getAdverts()
        ;

        // On donne toutes les informations nécessaires à la vue
        return $this->render('NLPlatformBundle:Advert:index.html.twig', array(
            'listAdvert' => $allAdvert,
        ));
    }
}
