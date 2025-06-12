<?php

namespace App\Controller;

use App\Entity\ShortLink;
use App\Service\LinkNormalizer;
use App\Service\OpenRouterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShortLinkController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator, private EntityManagerInterface $em, private OpenRouterService $openRouterService) {}

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

        $shortLinkEntity = $this->em->getRepository(ShortLink::class)->findOneBy(['originalLink' => $normalizedLink]) ?? new ShortLink();
        if ($shortLinkEntity->getId()) {
            return $this->json([
                'shortLink' => $request->getSchemeAndHttpHost() . "/" . $shortLinkEntity->getShortLink()
            ]);
        }

        $aiShortPath = $this->openRouterService->generateShortPath($link);
        $exists = $this->em->getRepository(ShortLink::class)->findOneBy(['shortLink' => $aiShortPath]);
        while ($exists) {
            $suffix = '_'.substr(bin2hex(random_bytes(2)), 0, 3);
            $aiShortPath = $aiShortPath . $suffix;
            $exists = $this->em->getRepository(ShortLink::class)->findOneBy(['shortLink' => $aiShortPath]);
        }

        $shortLinkEntity->setShortLink($aiShortPath);
        $shortLinkEntity->setOriginalLink($link);

        $this->em->persist($shortLinkEntity);
        $this->em->flush();

        return $this->json([
            'shortLink' => $request->getSchemeAndHttpHost() . "/" . $shortLinkEntity->getShortLink()
        ]);
    }

    #[Route('/{shortLink}', methods: ['GET'])]
    public function getOriginalLink(string $shortLink): Response
    {
        $shortLinkEntity = $this->em->getRepository(ShortLink::class)->findOneBy(['shortLink' => $shortLink]);
        if (!$shortLinkEntity) {
            return $this->json([
                'error' => $this->translator->trans('request.shortlink.redirect_to_original.not_found')
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->redirect($shortLinkEntity->getOriginalLink());
    }

    private function generateRandomShortPath(int $length = 6): string
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
}
