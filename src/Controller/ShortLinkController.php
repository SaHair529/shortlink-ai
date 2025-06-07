<?php

namespace App\Controller;

use App\Service\LinkNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShortLinkController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {
        
    }

    #[Route('/', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $link = $request->query->get('link');
        if (!$link) {
            return $this->json([
                'error' => $this->translator->trans('request.invalid'),
                'missing_fields' => ['link']
            ], Response::HTTP_BAD_REQUEST);
        }

        $normalizedLink = LinkNormalizer::normalizeLink($link);
        if (!$normalizedLink) {
            return $this->json([
                'error' => $this->translator->trans('request.shortlink.create.invalid_link_param')
            ]);
        }

        return $this->json(['message' => 'ok']);
    }
}