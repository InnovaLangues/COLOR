<?php

namespace Innova\ForumMultimodalBundle\Tests\Controller;
use Innova\ForumMultimodalBundle\Entity\Contribution;
use Innova\ForumMultimodalBundle\Entity\Subject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContributionControllerTest extends WebTestCase
{
	public function testshowContributionAction()
	{
		$client = static::createClient();
    	$contribution = new Contribution();
    	$subject = new Subject();
    	$contribution->setDate("12/12/2014");
    	$contribution->setUser($client);
    	$contribution->setExtension("null");
    	$contribution->setLien("null");
    	$contribution->setTime("14:32:38");
    	$contribution->setType("texte");
    	$contribution->SetListen(0);
    	$contribution->setContents("test");
    	$contribution->setSubject($subject);
    	$contribution->setFather(0);
	}
	public function testaddContributionFileAction()
	{
		$client = static::createClient();
    	$contribution = new Contribution();
    	$subject = new Subject();
    	$contribution->setDate("12/12/2014");
    	$contribution->setUser($client);
    	$contribution->setExtension("jpg");
    	$contribution->setLien("mon test");
    	$contribution->setTime("14:32:38");
    	$contribution->setType("file");
    	$contribution->SetListen(0);
    	$contribution->setContents("test.jpg");
    	$contribution->setSubject($subject);
    	$contribution->setFather(0);
		
	}
	public function testAddCommentaireTextAction()
	{
		$client = static::createClient();
		$contribution = new Contribution();
    	$subject = new Subject();
    	$contribution->setDate("12/12/2014");
    	$contribution->setUser($client);
    	$contribution->setExtension("null");
    	$contribution->setLien("null");
    	$contribution->setTime("14:32:38");
    	$contribution->setType("texte");
    	$contribution->SetListen(0);
    	$contribution->setContents("test");
    	$contribution->setSubject($subject);
    	$contribution->setFather(1);
		
	}
	public function testAddCommentaireFileAction()
	{
		$client = static::createClient();
		$contribution = new Contribution();
    	$subject = new Subject();
    	$contribution->setDate("12/12/2014");
    	$contribution->setUser($client);
    	$contribution->setExtension("jpg");
    	$contribution->setLien("mon test");
    	$contribution->setTime("14:32:38");
    	$contribution->setType("file");
    	$contribution->SetListen(0);
    	$contribution->setContents("test.jpg");
    	$contribution->setSubject($subject);
    	$contribution->setFather(3);
		
	}
	
}
