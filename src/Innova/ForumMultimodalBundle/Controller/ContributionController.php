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
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use \ZipArchive;
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
	        $dir = $this->get('kernel')->getRootDir().'/../web/uploads/';
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
	        $pathFile = "uploads/".$name;
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
        // lien de la contribution
        $lien = $contri->getLien();
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
        return $this->render('InnovaForumMultimodalBundle:Commentaire:commentaireContribution.html.twig', array('id' => $id,'idSubjectContribution' => $idSubjectContribution,'lien' => $lien,'contents' => $contents,'subject' => $subject,'user' => $user,'date' => $date,'time' => $time,'type' => $type,'listeContributions' => $listeContributions,'extension' => $extension,'countContribution' => $countContribution,));
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
        $dir = $this->get('kernel')->getRootDir().'/../web/uploads/';
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
        $pathFile = "uploads/".$name;
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
   * [downloadSlideshowAction description]
   * @param  Subject $subj [description]
   * @return [type]        [description]
   */
  public function downloadSlideshowAction(Subject $subj)
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
        // supprimer les fichiers de dossier uploads
        $folder = $this->get('kernel')->getRootDir().'/../web/diaporama/uploads/'; 
        $dossier=opendir($folder);
        while ($fichier = readdir($dossier))
        {              
          if ($fichier != "." && $fichier != ".." && !is_dir($folder.$fichier))
          {
                  //On selectionne le fichier et on le supprime
                  $Vidage= $folder.$fichier;
                  unlink($Vidage);
          }
        }
        closedir($dossier);  
         $text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                  <html xmlns="http://www.w3.org/1999/xhtml">
                  <head>
                    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.css" type="text/css" />
                    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
                    <link href="css/style.css" rel="stylesheet" type="text/css"/>
                    <link href="css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
                    <script src="js/jquery.js"></script>
                    <script src="js/bootstrap.min.js"></script>
                    <script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
                    <script type="text/javascript" src="js/jquery.cycle.all.min.js"></script>
                    <script type="text/javascript" src="js/slideshow.js"></script>
                  </head>
                  <body id="content" > 
                    <div class="row">
                      <div class="well">
                        <button class="btn btn-warning btn-small" id="prevslide"><i class="icon-backward"></i></button>
                        <button class="btn btn-warning btn-small" id="nextslide"><i class="icon-forward"></i></button>
                        <button class="btn btn-warning btn-small" id="pauseslide"><i class="icon-pause"></i></button>
                        <button class="btn btn-warning btn-small" id="playslide"><i class="icon-play"></i></button> 
                      </div>
                    </div>
                    <br />
                    <div class="row">
                    <div class="span3">
                    </div>
                    <div class="span6"> 
                      <ul id="slider" class="well">';
                      $text .= '<li><p>Film - <strong><font color="red">'.$subject.'</font></strong></p><br /><p><font color="blue">'.$consigne.'</font></p><br /><p>Collectorama ajouter par '.$author.' le '.$date->format('d/m/Y').'</p></li>';
        foreach($listeContributions as $cont)
            {
              if($cont->getType() == "oral")
              {
                $pathFile = $cont->getContents();
                $source = $this->get('kernel')->getRootDir().'/../web/'.$pathFile;
                $destination = $this->get('kernel')->getRootDir().'/../web/diaporama/'.$pathFile; 
                copy($source, $destination); 
                $text .= '<li><audio src="'.$pathFile.'" controls></audio></li>';
              }
              else if($cont->getType() == "fichier")
              {
                $pathFile = $cont->getContents();
                $source = $this->get('kernel')->getRootDir().'/../web/'.$pathFile;
                $destination = $this->get('kernel')->getRootDir().'/../web/diaporama/'.$pathFile; 
                copy($source, $destination); 
                if($cont->getExtension() == "png" || $cont->getExtension() == "jpg" || $cont->getExtension() == "jpeg" || $cont->getExtension() == "gif")
                {
                  $text .= '<li><img src="'.$pathFile.'" alt="" id="img" /></li> ';
                }
                else if($cont->getExtension() == "pdf")
                {
                  $text .= '<li><object id="cv" data="'.$pathFile.'" type="application/pdf"></object></li>';
                }
                else if($cont->getExtension() == "txt")
                {
                  $text .= '<li><object id="cv" data="'.$pathFile.'" type="text/plain"></object></li>';
                }
              }
              else
              {
                $text .= '<li><p>'.$cont->getContents().'</p></li>';
              }
            }
              $text .= '</ul>
                        </div>
                        <div class="span3">
                        </div>
                          
                        </div></body>
                        </html>
                      ';
              $filename = $this->get('kernel')->getRootDir().'/../web/diaporama/'.'index.html';
              $open = fopen($filename, "w");
              fwrite($open, $text); 
              fclose($open); 

                // commencer compression
                $dirZip = $this->get('kernel')->getRootDir().'/../web/diaporama/';
                $this->Zip($dirZip, './Diaporama.zip');
                // proposer telechargement
                header('Content-Transfer-Encoding: binary'); //Transfert en binaire (fichier).
                header('Content-Disposition: attachment; filename="Diaporama.zip"'); //Nom du fichier.
                header('Content-Length: '.filesize('Diaporama.zip')); //Taille du fichier.
                readfile('Diaporama.zip');
                // fin telechargement
                // fin compression
                // suppression zip
                $fileZipToDelete = $this->get('kernel')->getRootDir().'/../web/Diaporama.zip';
                unlink($fileZipToDelete);
                

   return $this->redirect($this->generateUrl('innova_forum_multimodal_view_slideshow', array('id' => $id)));
  }
  /**
   * [Zip description]
   * @param [type] $source      [description]
   * @param [type] $destination [description]
   */
  function Zip($source, $destination)
  {
      if (is_string($source)) $source_arr = array($source); // convert it to array

      if (!extension_loaded('zip')) {
          return false;
      }

      $zip = new ZipArchive();
      if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
          return false;
      }

      foreach ($source_arr as $source)
      {
          if (!file_exists($source)) continue;
          $source = str_replace('\\', '/', realpath($source));

          if (is_dir($source) === true)
          {
              $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

              foreach ($files as $file)
              {
                  $file = str_replace('\\', '/', realpath($file));

                  if (is_dir($file) === true)
                  {
                      $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                  }
                  else if (is_file($file) === true)
                  {
                      $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                  }
              }
          }
          else if (is_file($source) === true)
          {
              $zip->addFromString(basename($source), file_get_contents($source));
          }

      }

      return $zip->close();

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
	$dir = $this->get('kernel')->getRootDir().'/../web/';
  foreach($listeContributionsSon as $cont)
	{
    if($cont->getType() == "oral" || $cont->getType() == "fichier")
    {
      unlink($dir.$cont->getContents());
		  $emContribution->remove($cont);
    	$emContribution->flush();
    }
    else
    {
      $emContribution->remove($cont);
      $emContribution->flush();
    }
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
