<?php

namespace Innova\ForumMultimodalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Innova\ForumMultimodalBundle\Form\ChoiceContributionForm;

class ContributionController extends Controller
{
  public function voirContributionAction($id)
  {
        // $form = $this->createForm(new ChoiceContributionForm());
        // $request = $this->getRequest();
        // return $this->render('InnovaForumMultimodalBundle:Forum:choiceContribution.html.twig', array('id' => $id, 'form' => $form->createView(),));
        // selectionner les sons dans le dossier uploads

        $tableauSons = array();
        $dirname = $this->get('kernel')->getRootDir().'/../web/uploads/';
        $dir = opendir($dirname); 

            while($file = readdir($dir)) {
              if($file != '.' && $file != '..' && !is_dir($dirname.$file))
              {
                $element = pathinfo($file);
                if ($element['extension']=="mp3"){
                  if($file != "test.mp3"){
                    $pathSons = 'uploads/'.$file;
                     array_push($tableauSons,$pathSons); 
                  }
                }

              }
            }

            closedir($dir);
            // print_r($tableauSons);
         //On crée le FormBuilder grâce à la méthode du contrôleur. Toujours sans entité
        $form = $this->createForm(new ChoiceContributionForm());

        //On récupère la requête
        $request = $this->getRequest();
        $id = $request->attributes->get('id');

        if($request->getMethod() == 'POST')
        {
            $form->bind($request);

            //On vérifie que les valeurs entrées sont correctes
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();

                //On récupère les données entrées dans le formulaire par l'utilisateur
                $choix = $form["Choix"]->getData();
                $data = $this->getRequest()->request->get('innova_forummultimodalbundle_ChoiceContributionForm');

                if($choix=="oral")
                {
                    // return $this->redirect($this->generateUrl('innova_forum_multimodal_voir_contribution', array('id' => $id)));
                    return $this->render('InnovaForumMultimodalBundle:Forum:voirContribution.html.twig', array('id' => $id,'tableauSons' => $tableauSons,));

                }
                else if($choix=="texte")
                {
                  echo "En construction";
                }
                else if($choix=="fichier")
                {
                  echo "En construction";
                  
                }

                //Puis on redirige vers la page de visualisation
                // return $this->render('HurricaneScriptAnnonceBundle:Annonce:listeAnnonces.html.twig', array('liste_annonces' => $liste_annonces));
            }
        }


        // À ce stade :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau
        return $this->render('InnovaForumMultimodalBundle:Forum:choiceContribution.html.twig', array('id' => $id, 'form' => $form->createView(),'tableauSons' => $tableauSons,));
  }
  public function addContributionAction()
  {
    return $this->render('InnovaForumMultimodalBundle:Forum:index.html.twig');
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
