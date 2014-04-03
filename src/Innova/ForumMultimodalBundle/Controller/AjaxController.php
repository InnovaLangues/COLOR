<?php

namespace Innova\ForumMultimodalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AjaxController extends Controller
{
	 /**
	 * @Route("/forum/contribution/ajax", name="innova_forum_multimodal_upload", options={"expose"=true})
	 */
	  public function uploadAction()
	  {
		$request = $this->container->get('request');
		$webPath = $this->get('kernel')->getRootDir().'/../web/uploads/';
		// $webPath = $this->container->getParameter('kernel.root_dir').'/../web/uploads/';

		// if (is_writable("/Users/Mahmoud/htdocs/Symfony/web/uploads"))
		// {
		// 	echo 'c_est bon on peux écrire';
		// 	exit();
		// }
		// else
		// {
		//  	echo 'ha ba non en fait XD';
		//  	exit();
		// }

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