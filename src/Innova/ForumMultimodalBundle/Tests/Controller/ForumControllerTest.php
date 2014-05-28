<?php

namespace Innova\ForumMultimodalBundle\Tests\Controller;
use Innova\ForumMultimodalBundle\Entity\Contribution;
use Innova\ForumMultimodalBundle\Entity\Subject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ForumControllerTest extends WebTestCase
{
	public function testaddSubjectAction()
    {
    	$client = static::createClient();
    	$contribution = new Contribution();
    	$subject = new Subject();
    	$subject->setDate("12/12/2014");
    	$subject->setAuteur($client);
        $subject->setConsigne("consigne de test");
        $subject->setSujet("sujet de test");

    }
	
}
