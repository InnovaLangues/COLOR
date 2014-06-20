<?php

namespace Innova\ForumMultimodalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Innova\ForumMultimodalBundle\Entity\Contribution;

/**
 * Ajax Controller
 * @category   Controller
 * @package    Innova
 * @author Mahmoud Charfeddine <[charfeddine.mahmoud@gmail.com]>
 * @copyright  2014 Mahmoud Charfeddine.
 * @version    0.1
 */
class AjaxController extends Controller
{
	 /**
	 * @Route("/forum/contribution/ajax", name="innova_forum_multimodal_upload", options={"expose"=true})
	 */
	
	  /**
	   * add a contribution or comment type audio recording
	   * 
	   * [uploadAction description]
	   * @return Response
	   */
	  public function uploadAction()
	  {
	  	$idsujet = $_POST["idsujet"];
	  	$pereoufils = $_POST["pereoufils"];
		$request = $this->container->get('request');
		$contribution = new Contribution();
		$user = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getManager();
		$subject = $em->getRepository('InnovaForumMultimodalBundle:Subject')->find($idsujet);
		$contribution->setDate(new \Datetime());
    	$contribution->setUser($user);
    	$contribution->setExtension("null");
    	$contribution->setLien("null");
    	$contribution->setTime(new \Datetime());
    	$contribution->setType("oral");
    	$contribution->SetListen(0);
    	$file = "uploads/".$_POST["audio-filename"];
    	$contribution->setContents($file);
    	$contribution->setSubject($subject);
    	$contribution->setFather($pereoufils);
    	$em = $this->getDoctrine()->getManager();
        $em->persist($contribution);
        $em->flush();
		$webPath = $this->get('kernel')->getRootDir().'/../web/uploads/';
		// $webPath = $this->container->getParameter('kernel.root_dir').'/../web/uploads/';
		foreach(array('audio') as $type) {
		    if (!empty($_FILES["${type}-blob"])) { 
		    	// print_r($_FILES);
		        $fileName = $_POST["${type}-filename"];
		        // $uploadDirectory = "/Users/Mahmoud/htdocs/Symfony/web/uploads/".$fileName;
		        $uploadDirectory = $webPath.$fileName;
		        if (!move_uploaded_file($_FILES["${type}-blob"]["tmp_name"], $uploadDirectory)) {
		            echo(" problem moving uploaded file");
		        }
		               $response = $uploadDirectory;
		                return new Response($response); 
		    }
		}


	  }

	  /**
	 * @Route("/forum/contribution/Listen", name="innova_forum_multimodal_listen", options={"expose"=true})
	 */
	
	  /**
	   * increment the number of listening for oral contributions
	   * 
	   * [listenAction description]
	   * @return Response
	   */
	  public function listenAction()
	  {
	  		$request = $this->get('request');
			$em = $this->getDoctrine()->getManager();

	  		$idContribution = $request->request->get('idContribution');

	        if($contrib = $em->getRepository('InnovaForumMultimodalBundle:Contribution')->findOneById($idContribution)){
		        $listenContrib = $contrib->getListen();
		        $contrib->setListen($listenContrib + 1);

		        $em->persist($contrib);
	            $em->flush();
		  	}
		  	else{
		  		echo "pas trouvé";
		  	}
		  	
	  		return new Response();
	  }
}