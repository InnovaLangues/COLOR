<?php

namespace Innova\ForumMultimodalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Innova\ForumMultimodalBundle\Form\TinymceForm;
use Innova\ForumMultimodalBundle\Form\FileForm;
use Innova\ForumMultimodalBundle\Entity\Contribution;
use Innova\ForumMultimodalBundle\Entity\Subject;

class ContributionController extends Controller
{
  
  public function showContributionAction(Subject $subj)
  {
        // tableau pour mettre le contenu de chaque contribution
        $tableauSons = array();
        // tableau de contribution père
        $tableauContributionFather = array();
        // tableau pour compter le nombre de Contribution fils de chaque contribution 
		$tableauCountContributionSon = array();
         //On crée le FormBuilder grâce à la méthode du contrôleur. Toujours sans entité
        $form2 = $this->createForm(new TinymceForm());
        //On récupère la requête
        $request = $this->getRequest();
        // l'id de sujet
        $id = $subj->getId();
        // la consigne donner avec le sujet
        $consigne = $subj->getConsigne();
        // le sujet
        $subject = $subj->getSujet();
        // debut remplissage tableau $tableauContributionFather
        $emContribution = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('InnovaForumMultimodalBundle:Contribution');
        $listeContributions = $emContribution->findBy(array('subject' => $id));
         foreach($listeContributions as $cont)
		        {
		          // $cont est une instance de Contribution
		        	if($cont->getFather() == "0")
		        	{
		                     array_push($tableauContributionFather,$cont); 
		            }
		        }
		// fin remplissage tableau $tableauContributionFather
        // le nombre de contribution de ce sujet
        $countContribution = count($tableauContributionFather);
        // debut remplissage tableau $tableauCountContributionSon
	    // on recupere toutes les contributions
	    $emCont = $this->getDoctrine()->getManager();
	    $entitiesCont = $emCont->getRepository('InnovaForumMultimodalBundle:Contribution')->findAll();
	    foreach($entitiesCont as $con)
	        {
	          // pour chaque contribution, on recupere son id
	          $idCon = $con->getId();
	          // on compte le nombre de contribution fils pour chaque contribution
	          $emContribution = $this->getDoctrine()->getManager();
	          $listeContributionsSon = $emContribution->getRepository('InnovaForumMultimodalBundle:Contribution')->findBy(array('father' => $idCon));
	          $countContributionSon = count($listeContributionsSon);
	          // on rajoute le noombre de contribution par sujet dans le tableau : tableauCountContribution
	          array_push($tableauCountContributionSon,$countContributionSon);
	        }
	    // fin remplissage tableau $tableauCountContributionSon
        if($request->getMethod() == 'POST')
        {
            $form2->bind($request);
            //On vérifie que les valeurs entrées sont correctes
            if($form2->isValid())
            {
            	$id = $subj->getId();
                // une instance de l'entity Contribution
                $contributionEntity = new Contribution();
                // on met la date actuelle pour l'ajout d'une nouvelle contribution
                $contributionEntity->setDate(new \Datetime());
                // on recupere l'objet user
                $user = $this->get('security.context')->getToken()->getUser();
                // on affecte l'objet user à la contribution
                $contributionEntity->setUser($user);
                // on affecte l'extension
                $contributionEntity->setExtension("null");
                // on met le lien à null
                $contributionEntity->setLien("null");
                // on met l'heure actuelle pour l'ajout d'une novelle contribution
                $contributionEntity->setTime(new \Datetime());
                // cette contribution et de type texte
                // la contribution de type oral est dans AjaxController
                $contributionEntity->setType("texte");
                // on recupere l'id de contribution pere
                $pereoufils = $form2["token"]->getData();
                $contributionEntity->setFather($pereoufils);
                // recuperer le contenu de l'editeur
                $contenuEditeur = $form2["Editeur"]->getData();
                $data = $this->getRequest()->request->get('innova_forummultimodalbundle_TinymceForm');
                // attribuer la valeur de l'editeur à notre attribut de l'entité contribution
                $contributionEntity->setContents($contenuEditeur);
                // debut : attribuer l'objet subject à notre attribut de l'entité contribution
                $emzou = $this->getDoctrine()->getManager();
                $subjectObject = $emzou->getRepository('InnovaForumMultimodalBundle:Subject')->find($id);
                $contributionEntity->setSubject($subjectObject);
                // fin : attribuer l'objet subject à notre attribut de l'entité contribution
                // debut : enregistrer notre objet contribution dans la base de données
                $em2 = $this->getDoctrine()->getManager();
                $em2->persist($contributionEntity);
                $em2->flush();
                // fin : enregistrer notre objet contribution dans la base de données
                // on redirige vers la page de visualisation, ici vers notre premiere action de controller
                return $this->redirect($this->generateUrl('innova_forum_multimodal_voir_contribution', array('id' => $id)));
            }           
        }
        // À ce stade :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau
        return $this->render('InnovaForumMultimodalBundle:Forum:choiceContribution.html.twig', array('id' => $id,'form2' => $form2->createView(),'listeContributions' => $tableauContributionFather,'tableauCountContributionSon' => $tableauCountContributionSon,'countContribution' => $countContribution,'subject' => $subject,'consigne' => $consigne,));
  }
  public function addContributionFileAction(Subject $subj)
  {
        //On récupère la requête
        $request = $this->getRequest();
        if($request->getMethod() == 'POST')
        {
	        // l'id de sujet
	        $id = $subj->getId();
	        // la consigne donner avec le sujet
	        $consigne = $subj->getConsigne();
	        // le sujet
	        $subject = $subj->getSujet();
	        // recuperer toutes les contributions d'un sujet
	        $emContribution = $this->getDoctrine()
	                           ->getManager()
	                           ->getRepository('InnovaForumMultimodalBundle:Contribution');
	        $listeContributions = $emContribution->findBy(array('subject' => $id));
	        // le nombre de contribution de ce sujet
	        $countContribution = count($listeContributions);
	        // une instance de l'entity Contribution
	        $contributionEntity = new Contribution();
	        // on met la date actuelle pour l'ajout d'une nouvelle contribution
	        $contributionEntity->setDate(new \Datetime());
	        // on recupere l'objet user
	        $user = $this->get('security.context')->getToken()->getUser();
	        // on affecte l'objet user à la contribution
	        $contributionEntity->setUser($user);
	        // on met l'heure actuelle pour l'ajout d'une novelle contribution
	        $contributionEntity->setTime(new \Datetime());
	        // cette contribution et de type texte
	        // la contribution de type oral est dans AjaxController
	        // le dossier où on va mettre les fichiers uploadés
	        $dir = $this->get('kernel')->getRootDir().'/../web/uploads/files/';
	        $contributionEntity->setType("fichier");
	        $pereoufils = $request->request->get('pereoufils');
	        $contributionEntity->setFather($pereoufils);
	  		foreach($request->files as $uploadedFile) 
	  		{
	  			$filename = $uploadedFile->getClientOriginalName();
	  			$extension = pathinfo($filename, PATHINFO_EXTENSION);
	  			$contributionEntity->setExtension($extension);
	  			$name = "file".rand(1, 99999).$subject.".".$extension;
			    $file = $uploadedFile->move($dir, $name);
			}
	        if($request->request->get('lien') != "")
	        {
	            $lien = $request->request->get('lien');
	            $contributionEntity->setLien($lien);
	        }
	        else
	        {
	            // on met le lien à null
	            $contributionEntity->setLien("null");
	        }
	        $pathFile = "uploads/files/".$name;
	        // attribuer le nom de fichier à notre attribut de l'entité contribution
	        $contributionEntity->setContents($pathFile);
	        // debut : attribuer l'objet subject à notre attribut de l'entité contribution
	        $emzou = $this->getDoctrine()->getManager();
	        $subjectObject = $emzou->getRepository('InnovaForumMultimodalBundle:Subject')->find($id);
	        $contributionEntity->setSubject($subjectObject);
	        // fin : attribuer l'objet subject à notre attribut de l'entité contribution
	        // debut : enregistrer notre objet contribution dans la base de données
	        $em2 = $this->getDoctrine()->getManager();
	        $em2->persist($contributionEntity);
	        $em2->flush();
    	}
        // fin : enregistrer notre objet contribution dans la base de données
        // on redirige vers la page de visualisation, ici vers notre premiere action de controller
		return $this->redirect($this->generateUrl('innova_forum_multimodal_voir_contribution', array('id' => $id)));
  }
    public function showCommentaireAction(Contribution $contri)
  {
       	//On récupère la requête
        $request = $this->getRequest();
        // l'id de sujet
        $id = $contri->getId();
        // la contents de la contribution
        $contents = $contri->getContents();
        // l'extension de la contribution
        $extension = $contri->getExtension();
        print_r($id);
        print_r($contents);
        print_r($extension);
        exit();
        // debut remplissage tableau $tableauContributionFather
        $emContribution = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('InnovaForumMultimodalBundle:Contribution');
        $listeContributions = $emContribution->findBy(array('subject' => $id));
         foreach($listeContributions as $cont)
		        {
		          // $cont est une instance de Contribution
		        	if($cont->getFather() == "0")
		        	{
		                     array_push($tableauContributionFather,$cont); 
		            }
		        }
		// fin remplissage tableau $tableauContributionFather
        // le nombre de contribution de ce sujet
        $countContribution = count($tableauContributionFather);
        // debut remplissage tableau $tableauCountContributionSon
	    // on recupere toutes les contributions
	    $emCont = $this->getDoctrine()->getManager();
	    $entitiesCont = $emCont->getRepository('InnovaForumMultimodalBundle:Contribution')->findAll();
	    foreach($entitiesCont as $con)
	        {
	          // pour chaque contribution, on recupere son id
	          $idCon = $con->getId();
	          // on compte le nombre de contribution fils pour chaque contribution
	          $emContribution = $this->getDoctrine()->getManager();
	          $listeContributionsSon = $emContribution->getRepository('InnovaForumMultimodalBundle:Contribution')->findBy(array('father' => $idCon));
	          $countContributionSon = count($listeContributionsSon);
	          // on rajoute le noombre de contribution par sujet dans le tableau : tableauCountContribution
	          array_push($tableauCountContributionSon,$countContributionSon);
	        }
	    // fin remplissage tableau $tableauCountContributionSon
        if($request->getMethod() == 'POST')
        {
            $form2->bind($request);
            //On vérifie que les valeurs entrées sont correctes
            if($form2->isValid())
            {
            	$id = $subj->getId();
                // une instance de l'entity Contribution
                $contributionEntity = new Contribution();
                // on met la date actuelle pour l'ajout d'une nouvelle contribution
                $contributionEntity->setDate(new \Datetime());
                // on recupere l'objet user
                $user = $this->get('security.context')->getToken()->getUser();
                // on affecte l'objet user à la contribution
                $contributionEntity->setUser($user);
                // on affecte l'extension
                $contributionEntity->setExtension("null");
                // on met le lien à null
                $contributionEntity->setLien("null");
                // on met l'heure actuelle pour l'ajout d'une novelle contribution
                $contributionEntity->setTime(new \Datetime());
                // cette contribution et de type texte
                // la contribution de type oral est dans AjaxController
                $contributionEntity->setType("texte");
                // on recupere l'id de contribution pere
                $pereoufils = $form2["token"]->getData();
                $contributionEntity->setFather($pereoufils);
                // recuperer le contenu de l'editeur
                $contenuEditeur = $form2["Editeur"]->getData();
                $data = $this->getRequest()->request->get('innova_forummultimodalbundle_TinymceForm');
                // attribuer la valeur de l'editeur à notre attribut de l'entité contribution
                $contributionEntity->setContents($contenuEditeur);
                // debut : attribuer l'objet subject à notre attribut de l'entité contribution
                $emzou = $this->getDoctrine()->getManager();
                $subjectObject = $emzou->getRepository('InnovaForumMultimodalBundle:Subject')->find($id);
                $contributionEntity->setSubject($subjectObject);
                // fin : attribuer l'objet subject à notre attribut de l'entité contribution
                // debut : enregistrer notre objet contribution dans la base de données
                $em2 = $this->getDoctrine()->getManager();
                $em2->persist($contributionEntity);
                $em2->flush();
                // fin : enregistrer notre objet contribution dans la base de données
                // on redirige vers la page de visualisation, ici vers notre premiere action de controller
                return $this->redirect($this->generateUrl('innova_forum_multimodal_voir_contribution', array('id' => $id)));
            }           
        }
        // À ce stade :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau
        return $this->render('InnovaForumMultimodalBundle:Forum:commentaireContribution.html.twig', array('id' => $id,'form2' => $form2->createView(),'listeContributions' => $tableauContributionFather,'tableauCountContributionSon' => $tableauCountContributionSon,'countContribution' => $countContribution,'subject' => $subject,'consigne' => $consigne,));
  }
  public function deleteContributionAction()
  {
    return $this->render('InnovaForumMultimodalBundle:Forum:index.html.twig');
  }
  public function updateContributionAction()
  {
    return $this->render('InnovaForumMultimodalBundle:Forum:index.html.twig');
  }
}
