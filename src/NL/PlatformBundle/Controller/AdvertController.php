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
    // On fixe en dur une liste ici, bien entendu par la suite
    // on la récupérera depuis la BDD !
        /**
         * A supprimer - Ici pour test
         */
        $listAdverts = array(
            array('id' => 2, 'title' => 'Recherche développeur Symfony2'),
            array('id' => 5, 'title' => 'Mission de webmaster'),
            array('id' => 9, 'title' => 'Offre de stage webdesigner')
        );
        return $this->render('NLPlatformBundle:Advert:menu.html.twig', array(
    // Tout l'intérêt est ici : le contrôleur passe
    // les variables nécessaires au template !
            'listAdverts' => $listAdverts
        ));
    }
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository("NLPlatformBundle:Advert")->find($id);

        if( null === $advert) {
            throw new NotFoundHttpException("L'anonce d'id ".$id." n'existe pas.");
        }

        // La methode findAll retourne toutes les categories de la base de données
        $listeCategories = $em->getRepository('NLPlatformBundle:Category')->findAll();

        // On boucle sur les catégories pour les lies à l'annonce
        foreach($listeCategories as $category)
        {
            $advert->addCategory($category);
        }

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine
        // Étape 2 : On déclenche l'enregistrement
        $em->flush();

        // Même mécanisme que pour l'ajout
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

            return $this->redirect($this->generateUrl('nl_platform_view', array('id' => 5)));
        }
        return $this->render('NLPlatformBundle:Advert:edit.html.twig', array(
            'advert' => $advert,
        ));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository("NLPlatformBundle:Advert")->find($id);

        if( null === $advert) {
            throw new NotFoundHttpException("L'anonce d'id ".$id." n'existe pas.");
        }

        // On boucle sur les categories de l'annonce pour les supprimer
        foreach($advert->getCategories() as $category){
            $advert->removeCategory($category);
        }

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine
        // On déclenche la modification
        $em->flush();

        return $this->render('NLPlatformBundle:Advert:delete.html.twig');
    }
    public function addAction(Request $request)
    {
        // Creation de l'entité
        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony2.');
        $advert->setAuthor('Nicholas');
        $advert->setContent('Nous recherchons un dev Symfony 2 sur Panama blablabla ...');
        // On peut ne pas définir ni la date ni la publication, car cest attributs sont définis automatiquement dans le constructeur

        // Creation de 'limage
        $image = new Image();
        $image->setUrl("http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg");
        $image->setAlt("Job de rêve");

        // Création d'une première candidature
        $application1 = new Application();
        $application1->setAuthor('Marine');
        $application1->setContent("J'ai toutes les qualités requises.");

        // Création d'une deuxième candidature par exemple
        $application2 = new Application();
        $application2->setAuthor('Pierre');
        $application2->setContent("Je suis très motivé.");

        // On lie les candidatures à l'annonce
        $application1->setAdvert($advert);
        $application2->setAdvert($advert);

        // On lie l'image à l'annonce
        $advert->setImage($image);

        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // Test pour mettre a jour une annonce (updateDate) - OK
        $em->getRepository("NLPlatformBundle:Advert")
            ->find(18)
            ->updateDate()
        ;
        $em->getRepository("NLPlatformBundle:Advert")
            ->find(19)
            ->updateDate()
        ;
        // On récupère toutes les compétences possibles
        $listSkills = $em->getRepository('NLPlatformBundle:Skill')->findAll();
        // Pour chaque compétence
        foreach ($listSkills as $skill) {
            // On crée une nouvelle « relation entre 1 annonce et 1 compétence »
            $advertSkill = new AdvertSkill();
            // On la lie à l'annonce, qui est ici toujours la même
            $advertSkill->setAdvert($advert);
            // On la lie à la compétence, qui change ici dans la boucle foreach
            $advertSkill->setSkill($skill);
            // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
            $advertSkill->setLevel('Expert');

            // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
            $em->persist($advertSkill);
        }

        // Etape 1 : On "persiste" l'entité
        $em->persist($advert);

        // Etape 1 bis : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est definie dans l'entité Application et non Advert. On doit donc tout persiter à la main ici.
        $em->persist($application1);
        $em->persist($application2);

        // Etape 2 : On "flush" tout ce qui a été persisté avant
        $em->flush();

        // La gestion d'un formulaire est particulière, mais l'idée est la suivante :
        // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST')) {
            // Ici, on s'occupera de la création et de la gestion du formulaire
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            // Puis on redirige vers la page de visualisation de cettte annonce
            return $this->redirect($this->generateUrl('nl_platform_view', array('id' => $advert->getId())));
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

        // On récupère la liste des candidatures de cette annonce
        $listeApplications = $em
            ->getRepository('NLPlatformBundle:Application')
            ->findBy(array('advert' => $advert))
        ;

        // On récupère maintenant la liste des AdvertSkill
        $listAdvertSkills = $em
            ->getRepository('NLPlatformBundle:AdvertSkill')
            ->findBy(array('advert' => $advert))
        ;

        return $this->render('NLPlatformBundle:Advert:view.html.twig',array(
            'advert' => $advert,
            'listeApplication' => $listeApplications,
            'listAdvertSkills' => $listAdvertSkills
        ));
    }
    public function indexAction($page)
    {
        if($page<1)
        {
            throw new NotFoundHttpException('Page '.$page.' inexistante.');
        }
        // Ici, on récupérera la liste des annonces, puis on la passera au template
        // Mais pour l'instant, on ne fait qu'appeler le template
        /**
         * A supprimer - Ici pour test
         */
        $listAdverts = array(
            array(
                'title' => 'Recherche développpeur Symfony2',
                'id' => 1,
                'author' => 'Alexandre',
                'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
                'date' => new \Datetime()),
            array(
                'title' => 'Mission de webmaster',
                'id' => 2,
                'author' => 'Hugo',
                'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date' => new \Datetime()),
            array(
                'title' => 'Offre de stage webdesigner',
                'id' => 3,
                'author' => 'Mathieu',
                'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date' => new \Datetime())
        );
        return $this->render('NLPlatformBundle:Advert:index.html.twig', array(
            'listAdvert' => $listAdverts
        ));
    }
}
