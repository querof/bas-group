<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeBasController
{
    #[Route('/bas/welcome', name: 'bas_welcome')]
    public function number(): Response
    {
        return new Response(
            '<html><body> Welcome Bas!  </body></html>'
        );
    }
}