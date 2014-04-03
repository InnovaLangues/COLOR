<?php

namespace Innova\UsersBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class InnovaUsersBundle extends Bundle
{
	public function getParent()
    {
    	return 'FOSUserBundle';
    }
}
