<?php

namespace Innova\ForumMultimodalBundle\Tests\Controller;
use Innova\ForumMultimodalBundle\Entity\Contribution;
use Innova\ForumMultimodalBundle\Entity\Subject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AjaxControllerTest extends WebTestCase
{
	public function testuploadAction()
    {
    	$client = static::createClient();
    	$contribution = new Contribution();
    	$subject = new Subject();
    	$contribution->setDate("12/12/2014");
    	$contribution->setUser($client);
    	$contribution->setExtension("null");
    	$contribution->setLien("null");
    	$contribution->setTime("14:32:38");
    	$contribution->setType("oral");
    	$contribution->SetListen(0);
    	$file = "uploads/fichier";
    	$contribution->setContents($file);
    	$contribution->setSubject($subject);
    	$contribution->setFather(0);

    }
    public function testlistenAction()
    {
    	$listen = 1;
    	$listenContrib = 2;
    	$updateListen = $listenContrib + $listen;
    	// vérifie que la classe a correctement calculé!
    	if($updateListen == 3)
    	{
    		echo "ok";
    	}

    }
	
}
