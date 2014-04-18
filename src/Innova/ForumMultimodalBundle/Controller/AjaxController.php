<?php

namespace Innova\ForumMultimodalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Innova\ForumMultimodalBundle\Entity\Contribution;

class AjaxController extends Controller
{
	 /**
	 * @Route("/forum/contribution/ajax", name="innova_forum_multimodal_upload", options={"expose"=true})
	 */
	  public function uploadAction()
	  {
	  	$idsujet = $_POST["idsujet"];
		$request = $this->container->get('request');
		$contribution = new Contribution();
		$user = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getManager();
		$subject = $em->getRepository('InnovaForumMultimodalBundle:Subject')->find($idsujet);
		$contribution->setDate(new \Datetime());
    	$contribution->setUser($user);
    	$contribution->setExtension("null");
    	$contribution->setTime(new \Datetime());
    	$contribution->setType("oral");
    	$file = "uploads/".$_POST["audio-filename"];
    	$contribution->setContents($file);
    	$contribution->setSubject($subject);
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
}