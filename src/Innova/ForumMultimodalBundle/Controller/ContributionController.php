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
/**
 * Contribution Controller
 * @category   Controller
 * @package    Innova
 * @author Mahmoud Charfeddine <[charfeddine.mahmoud@gmail.com]>
 * @copyright  2014 Mahmoud Charfeddine.
 * @version    0.1
 */
class ContributionController extends Controller
{
  
  /**
   * allows to visualize the contributions and add a contribution of text-editing
   * 
   * [showContributionAction description]
   * @param  Subject $subj
   * @return Response
   * 
   */
  public function showContributionAction(Subject $subj)
  {
        // tableau pour mettre le contenu de chaque contribution
        $tableauSons = array();
        // tableau de contribution père
        $tableauContributionFather = array();
        // tableau pour compter le nombre de Contribution fils de chaque contribution 
		$tableauCountContributionSon = array();
		// tableau pour mettre les id de chaque contribution
		$tableauIdContribution = array();
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
	          // echo $idCon."*****";
	          // on compte le nombre de contribution fils pour chaque contribution
	          $emContribution = $this->getDoctrine()->getManager();
	          $listeContributionsSon = $emContribution->getRepository('InnovaForumMultimodalBundle:Contribution')->findBy(array('father' => $idCon));
	          $countContributionSon = count($listeContributionsSon);
	          // on rajoute le nombre de contribution par sujet dans le tableau : tableauCountContribution
	          array_push($tableauCountContributionSon,$countContributionSon);

	          array_push($tableauIdContribution,$idCon);
	          // echo $idCon."*****";
	          // echo $idCon."==>".$countContributionSon."****";
	        }
	        // combiner les deux tableaux tableauIdContribution et tableauCountContributionSon
	        $tabCombine = array_combine($tableauIdContribution, $tableauCountContributionSon);
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
                // le nombre d'ecoute par default est à zero
                $contributionEntity->SetListen(0);
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
        return $this->render('InnovaForumMultimodalBundle:Contribution:choiceContribution.html.twig', array('id' => $id,'form2' => $form2->createView(),'listeContributions' => $tableauContributionFather,'tableauCountContributionSon' => $tabCombine,'countContribution' => $countContribution,'subject' => $subject,'consigne' => $consigne,));
  }
  /**
   * add a contribution deposit type file
   * 
   * [addContributionFileAction description]
   * @param Subject $subj
   * @return Response
   */
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
	        $contributionEntity->SetListen(0);
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
  /**
   * view comments of contribution
   * 
   * [showCommentaireAction description]
   * @param  Contribution $contri
   * @return Response
   */
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
        // type de la contribution
        $type = $contri->getType();
        // subject de la contribution
        $subject = $contri->getSubject();
        // user de la contribution
        $user = $contri->getUser();
        // date de la contribution
        $date = $contri->getDate();
        // time de la contribution
        $time = $contri->getTime();
        // debut remplissage tableau $tableauContributionFather
        $emContribution = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('InnovaForumMultimodalBundle:Contribution');
        $listeContributions = $emContribution->findBy(array('father' => $id));
        // le nombre de contribution de ce sujet
        $countContribution = count($listeContributions);
        $em = $this->getDoctrine()->getManager();
        $contrib = $em->getRepository('InnovaForumMultimodalBundle:Contribution')->findOneById($id);
        $idSubjectContribution = $contrib->getSubject()->getId();
        return $this->render('InnovaForumMultimodalBundle:Commentaire:commentaireContribution.html.twig', array('id' => $id,'idSubjectContribution' => $idSubjectContribution,'contents' => $contents,'subject' => $subject,'user' => $user,'date' => $date,'time' => $time,'type' => $type,'listeContributions' => $listeContributions,'extension' => $extension,'countContribution' => $countContribution,));
  }
  /**
   * add a comment in text format
   * 
   * [AddCommentaireTextAction description]
   * @param Contribution $contri
   * @return Response 
   */
  public function AddCommentaireTextAction(Contribution $contri)
  {
  	//On récupère la requête
    $request = $this->getRequest();
    if($request->getMethod() == 'POST')
    {
    	$contributionEntity = new Contribution();
    	// l'id de sujet
        $id = $contri->getId();
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
        // contenu
        $contributionEntity->setContents($request->request->get('editeur'));
        // type texte
        $contributionEntity->setType("texte");
        // id father
        $contributionEntity->setFather($request->request->get('idFather'));
        // le nombre d'ecoute par default est à zero
        $contributionEntity->SetListen(0);
        // id subjectObject
        $em = $this->getDoctrine()->getManager();
    	$contrib = $em->getRepository('InnovaForumMultimodalBundle:Contribution')->findOneById($request->request->get('idFather'));
        $contributionEntity->setSubject($contrib->getSubject());
        // enregistrer notre objet dans la base des données
        $em2 = $this->getDoctrine()->getManager();
        $em2->persist($contributionEntity);
        $em2->flush();
    }
  	 // on redirige vers la page de visualisation, ici vers showCommentaireAction
	return $this->redirect($this->generateUrl('innova_forum_multimodal_voir_commentaire', array('id' => $id)));
  	// return new Response("Hello World !");
  }
  /**
   * add a comment in file format
   * 
   * [AddCommentaireFileAction description]
   * @param Contribution $contri
   * @return Response
   */
  public function AddCommentaireFileAction(Contribution $contri)
  {
  	//On récupère la requête
    $request = $this->getRequest();
    if($request->getMethod() == 'POST')
    {
    	$contributionEntity = new Contribution();
    	// subjectObject
        $em = $this->getDoctrine()->getManager();
    	$contrib = $em->getRepository('InnovaForumMultimodalBundle:Contribution')->findOneById($request->request->get('pereoufils'));
        $contributionEntity->setSubject($contrib->getSubject());
        $subject = $contrib->getSubject()->getSujet();
    	// l'id de sujet
        $id = $contri->getId();
    	// on met la date actuelle pour l'ajout d'une nouvelle contribution
        $contributionEntity->setDate(new \Datetime());
        // on recupere l'objet user
        $user = $this->get('security.context')->getToken()->getUser();
        // on affecte l'objet user à la contribution
        $contributionEntity->setUser($user);
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
        // on met l'heure actuelle pour l'ajout d'une novelle contribution
        $contributionEntity->setTime(new \Datetime());
        // le nombre d'ecoute par default est à zero
        $contributionEntity->SetListen(0);
        // enregistrer notre objet dans la base des données
        $em2 = $this->getDoctrine()->getManager();
        $em2->persist($contributionEntity);
        $em2->flush();
    }
  	 // on redirige vers la page de visualisation, ici vers showCommentaireAction
	return $this->redirect($this->generateUrl('innova_forum_multimodal_voir_commentaire', array('id' => $id)));
  	// return new Response("Hello World !");
  }


  /**
   * [showSlideshowAction description]
   * @param  Subject $subj [description]
   * @return Response
   */
  public function showSlideshowAction(Subject $subj)
  {

    //On récupère la requête
        $request = $this->getRequest();
        // l'id de sujet
        $id = $subj->getId();
        // la consigne donner avec le sujet
        $consigne = $subj->getConsigne();
        // l'auteur du sujet
        $author = $subj->getAuteur();
        // date d'ajout du sujet
        $date = $subj->getDate();
        // le sujet
        $subject = $subj->getSujet();
        // debut remplissage tableau $tableauContributionFather
        $emContribution = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('InnovaForumMultimodalBundle:Contribution');
        $listeContributions = $emContribution->findBy(array('subject' => $id));
    return $this->render('InnovaForumMultimodalBundle:Contribution:diaporama.html.twig', array('id' => $id,'date' => $date,'author' => $author,'listeContributions' => $listeContributions,'subject' => $subject,'consigne' => $consigne,));
  }
  
  /**
   * delete contribution and comments related thereto
   * 
   * [deleteContributionAction description]
   * @param  Contribution $contri
   * @return Response
   */
  public function deleteContributionAction(Contribution $contri)
  {

  	// l'id de sujet
    $id = $contri->getId();
  	$em = $this->getDoctrine()->getManager();
	$contrib = $em->getRepository('InnovaForumMultimodalBundle:Contribution')->findOneById($id);
	$idSubject = $contrib->getSubject()->getId();
	$emContribution = $this->getDoctrine()->getManager();
	$listeContributionsSon = $emContribution->getRepository('InnovaForumMultimodalBundle:Contribution')->findBy(array('father' => $id));
	foreach($listeContributionsSon as $cont)
	{
		$emContribution->remove($cont);
    	$emContribution->flush();
	}
  	$em->remove($contrib);
    $em->flush();

    return $this->redirect($this->generateUrl('innova_forum_multimodal_voir_contribution', array('id' => $idSubject)));
  }
  /**
   * update a contribution
   * 
   * [updateContributionAction description]
   * @param  Contribution $contri
   * @return Response
   */
  public function updateContributionAction(Contribution $contri)
  {
  	// l'id de sujet
    $id = $contri->getId();
    $em = $this->getDoctrine()->getManager();
	$contrib = $em->getRepository('InnovaForumMultimodalBundle:Contribution')->findOneById($id);
	$idSubject = $contrib->getSubject()->getId();

    return $this->redirect($this->generateUrl('innova_forum_multimodal_voir_contribution', array('id' => $idSubject)));
  }
}
