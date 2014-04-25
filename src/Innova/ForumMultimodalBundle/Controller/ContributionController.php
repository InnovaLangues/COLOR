<?php

namespace Innova\ForumMultimodalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Innova\ForumMultimodalBundle\Form\ChoiceContributionForm;
use Innova\ForumMultimodalBundle\Form\TinymceForm;
use Innova\ForumMultimodalBundle\Form\FileForm;
use Innova\ForumMultimodalBundle\Entity\Contribution;
use Innova\ForumMultimodalBundle\Entity\Subject;

class ContributionController extends Controller
{
  
  public function showContributionAction(Subject $subj)
  {
        // selectionner les sons dans le dossier uploads
        $tableauSons = array();
        // $dirname = $this->get('kernel')->getRootDir().'/../web/uploads/';
        // $dir = opendir($dirname); 

        //     while($file = readdir($dir)) {
        //       if($file != '.' && $file != '..' && !is_dir($dirname.$file))
        //       {
        //         $element = pathinfo($file);
        //         if ($element['extension']=="mp3"){
        //           if($file != "test.mp3"){
        //             $pathSons = 'uploads/'.$file;
        //              array_push($tableauSons,$pathSons); 
        //           }
        //         }

        //       }
        //     }

        //   closedir($dir);
         //On crée le FormBuilder grâce à la méthode du contrôleur. Toujours sans entité
        $form = $this->createForm(new ChoiceContributionForm());

        //On récupère la requête
        $request = $this->getRequest();
        // l'id de sujet
        // $id = $contribution->getSubject()->getId();
        $id = $subj->getId();
        // la consigne donner avec le sujet
        // $consigne = $contribution->getSubject()->getConsigne();
        $consigne = $subj->getConsigne();
        // le sujet
        // $subject = $contribution->getSubject()->getSujet();
        $subject = $subj->getSujet();
        $emContribution = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('InnovaForumMultimodalBundle:Contribution');
        $listeContributions = $emContribution->findBy(array('subject' => $id));
        $countContribution = count($listeContributions);
        // foreach($listeContributions as $cont)
        // {
        //   // $cont est une instance de Contribution
        //  $file = $cont->getcontents();
        //  if($file != "test.mp3"){
        //             $pathSons = 'uploads/'.$file;
        //              array_push($tableauSons,$pathSons); 
        //           }
        //  // echo $cont->getUser();
        // }
        if($request->getMethod() == 'POST')
        {
            $form->bind($request);
            //On vérifie que les valeurs entrées sont correctes
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                //On récupère les données entrées dans le formulaire par l'utilisateur
                $choix = $form["Choix"]->getData();
                // tokenId nous permet de savoir si la contribution est une contribution mere ou une contribution fils
                $tokenId = $form["token"]->getData();
                $data = $this->getRequest()->request->get('innova_forummultimodalbundle_ChoiceContributionForm');
                // si c'est une contribution mere
                if($tokenId == 0)
                {
                //Puis on redirige vers la page de visualisation
                    if($choix=="oral")
                    {
                        // return $this->redirect($this->generateUrl('innova_forum_multimodal_voir_contribution', array('id' => $id)));
                        return $this->render('InnovaForumMultimodalBundle:Forum:showContribution.html.twig', array('id' => $id,'listeContributions' => $listeContributions,'countContribution' => $countContribution,'subject' => $subject,'consigne' => $consigne,));
                    }
                    else if($choix=="texte")
                    {
                      
                      return $this->redirect($this->generateUrl('innova_forum_multimodal_add_contribution_text', array('id' => $id)));
                      // le probleme de forward ==> il va garder le test sur le post de premier fonction
                      // par la suite dans la deuxieme methode il va garder la validiter de POST, quand il va essayer de valider le formulaire 
                      // il va pas trouver ce qu'il attend ==> merci à Axel
                      // return $this->forward('InnovaForumMultimodalBundle:Contribution:addContribution', array('id' => $id));
                      // return $this->render('InnovaForumMultimodalBundle:Forum:addTextContribution.html.twig', array('form' => $form2->createView(),'id' => $id,'listeContributions' => $listeContributions,'countContribution' => $countContribution,'subject' => $subject,'consigne' => $consigne,));
                    }
                    else if($choix=="fichier")
                    {
                      return $this->redirect($this->generateUrl('innova_forum_multimodal_add_contribution_file', array('id' => $id)));             
                    }
                }
                else
                {
                    echo "c'est une contribution fils";
                }
            }
        }
        // À ce stade :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau
        return $this->render('InnovaForumMultimodalBundle:Forum:choiceContribution.html.twig', array('id' => $id, 'form' => $form->createView(),'listeContributions' => $listeContributions,'countContribution' => $countContribution,'countContribution' => $countContribution,'subject' => $subject,'consigne' => $consigne,));
  }
  public function addContributionAction(Subject $subj)
  {
        
        $form = $this->createForm(new TinymceForm());
        //On récupère la requête
        $request = $this->getRequest();
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
        // on affecte l'extension
        $contributionEntity->setExtension("null");
        // on met l'heure actuelle pour l'ajout d'une novelle contribution
        $contributionEntity->setTime(new \Datetime());
        // cette contribution et de type texte
        // la contribution de type oral est dans AjaxController
        $contributionEntity->setType("texte");
        if($request->getMethod() == 'POST')
        {
          $form->bind($request);
          if ($form->isValid()) 
          {
            // recuperer le contenu de l'editeur
            $contenuEditeur = $form["Editeur"]->getData();
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

    return $this->render('InnovaForumMultimodalBundle:Forum:addTextContribution.html.twig', array('form' => $form->createView(),'id' => $id,'listeContributions' => $listeContributions,'countContribution' => $countContribution,'subject' => $subject,'consigne' => $consigne,));
  }
  public function addContributionFileAction(Subject $subj)
  {
        $form = $this->createForm(new FileForm());
        //On récupère la requête
        $request = $this->getRequest();
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
        if($request->getMethod() == 'POST')
        {
          $form->bind($request);
          if ($form->isValid()) 
          {
            // on recupere l'extension
            $extension = $form['attachment']->getData()->guessExtension();
            // on affecte l'extension
            $contributionEntity->setExtension($extension);
            // On garde le nom original du fichier de l'internaute
            // $form['attachment']->getData()->getClientOriginalName());
            $name = "file".rand(1, 99999).$subject.".".$extension;
            // mettre le fichier dans le bon dossier avec le bon nom
            $form['attachment']->getData()->move($dir, $name);
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
            // fin : enregistrer notre objet contribution dans la base de données
            // on redirige vers la page de visualisation, ici vers notre premiere action de controller
            return $this->redirect($this->generateUrl('innova_forum_multimodal_voir_contribution', array('id' => $id)));
          }
        }

    return $this->render('InnovaForumMultimodalBundle:Forum:addFileContribution.html.twig', array('form' => $form->createView(),'id' => $id,'listeContributions' => $listeContributions,'countContribution' => $countContribution,'subject' => $subject,'consigne' => $consigne,));
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
