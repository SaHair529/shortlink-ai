<?php

namespace App\Controller;

use App\Entity\ShortLink;
use App\Service\LinkNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShortLinkController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator, private EntityManagerInterface $em)
    {
        
    }

    #[Route('/', methods: ['GET'])]
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
            ], Response::HTTP_BAD_REQUEST);
        }

        $shortLinkEntity = $this->em->getRepository(ShortLink::class)->findOneBy(['originalLink' => $link]) ?? new ShortLink();
        if ($shortLinkEntity->getId()) {
            return $this->json($shortLinkEntity, Response::HTTP_OK, [], ['groups' => ['shortLink']]);
        }

        $shortLinkEntity->setShortLink($this->generateRandomShortPath(32));
        $shortLinkEntity->setOriginalLink($link);

        $this->em->persist($shortLinkEntity);
        $this->em->flush();

        return $this->json($shortLinkEntity, Response::HTTP_OK, [], ['groups' => ['shortLink']]);
    }

    private function generateRandomShortPath(int $length = 6): string
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
}