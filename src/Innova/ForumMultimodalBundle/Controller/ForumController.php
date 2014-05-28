<?php

namespace Innova\ForumMultimodalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Innova\ForumMultimodalBundle\Entity\Subject;
use Innova\ForumMultimodalBundle\Entity\Contribution;

/**
 * Forum Controller
 * @category   Controller
 * @package    Innova
 * @author Mahmoud Charfeddine <[charfeddine.mahmoud@gmail.com]>
 * @copyright  2014 Mahmoud Charfeddine.
 * @version    0.1
 */

class ForumController extends Controller
{

  /**
   * allows to view the home page
   * 
   * [welcomeAction description]
   * @return Response
   */
  public function welcomeAction()
  {
    return $this->render('InnovaForumMultimodalBundle:Collectorama:reception.html.twig');
  }

  /**
   * displays the page Collectorama
   * 
   * [indexAction description]
   * @return Response
   */
  public function indexAction()
  {
    return $this->render('InnovaForumMultimodalBundle:Collectorama:index.html.twig');
  }

  /**
   * add a new Collectorama
   * 
   * [addSubjectAction description]
   * @return Response
   */
  public function addSubjectAction()
  {

      // une instance de l'entity Contribution
      $contributionEntity = new Contribution();
      // On crée un objet Subject
      $Subject = new Subject();
      $user = $this->get('security.context')->getToken()->getUser();
      // print_r($user);
      // print_r($user->getId());
      // $userId = $user->getId();
      $Subject->setDate(new \Datetime());
      $Subject->setAuteur($user);

      $form = $this->createFormBuilder($Subject)
                  ->add('sujet',       'text',array('label' => 'Nom'))
                  ->add('Consigne', 'textarea')
                  ->getForm();

      // On récupère la requête
      $request = $this->get('request');

      // On vérifie qu'elle est de type POST
      if ($request->getMethod() == 'POST') {
        // On fait le lien Requête <-> Formulaire
        // À partir de maintenant, la variable $article contient les valeurs entrées dans le formulaire par le visiteur
        $form->bind($request);
        // On vérifie que les valeurs entrées sont correctes
        // (Nous verrons la validation des objets en détail dans le prochain chapitre)
        if ($form->isValid()) {
          // On l'enregistre notre objet $article dans la base de données
          $em = $this->getDoctrine()->getManager();
          $em->persist($Subject);
          $em->flush();

          // On redirige vers la page de visualisation de l'article nouvellement créé
          // return $this->redirect($this->generateUrl('innova_forum_multimodal_homepage', array('id' => $Subject->getId())));
          return $this->redirect($this->generateUrl('innova_forum_multimodal_add_subject'));
        }
    }

    // tableau pour compter le nombre de Contribution de chaque sujet 
    $tableauCountContribution = array();
    // on recupere tous les sujets
    $emSubject = $this->getDoctrine()->getManager();
    $entitiesSubject = $emSubject->getRepository('InnovaForumMultimodalBundle:Subject')->findAll();
    foreach($entitiesSubject as $sujet)
        {
          // pour chaque sujet, on recupere son id
          $id = $sujet->getId();
          // on compte le nombre de contribution pour chaque sujet
          $emContribution = $this->getDoctrine()->getManager();
          $listeContributions = $emContribution->getRepository('InnovaForumMultimodalBundle:Contribution')->findBy(array('subject' => $id));
          $countContribution = count($listeContributions);
          // on rajoute le noombre de contribution par sujet dans le tableau : tableauCountContribution
          array_push($tableauCountContribution,$countContribution);
        }

    // À ce stade :
    // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
    // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau
    $em2 = $this->getDoctrine()->getManager();
    $entities = $em2->getRepository('InnovaForumMultimodalBundle:Subject')->findAll();
    // selectionner les sons dans le dossier uploads
    $tableauCountContribution = array();
    foreach($entities as $entity)
        {
         $idSubject = $entity->getId();    
         $emContribution = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('InnovaForumMultimodalBundle:Contribution');
        $listeContributions = $emContribution->findBy(array('subject' => $idSubject));
        $countContribution = count($listeContributions);
        array_push($tableauCountContribution,$countContribution);
        }

    return $this->render('InnovaForumMultimodalBundle:Collectorama:index.html.twig', array(
      'form' => $form->createView(),'entities' => $entities,'tableauCountContribution' => $tableauCountContribution,
    ));
  }
  /**
   * [deleteSubjectAction description]
   * @return Response
   */
  public function deleteSubjectAction()
  {
    return $this->render('InnovaForumMultimodalBundle:Collectorama:index.html.twig');
  }
  /**
   * [updateSubjectAction description]
   * @return Response
   */
  public function updateSubjectAction()
  {
    return $this->render('InnovaForumMultimodalBundle:Collectorama:index.html.twig');
  }
}