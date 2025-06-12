<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class OpenRouterService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire('%env(OPENROUTER_API_KEY)%')] private string $apiKey
    ) {}

    public function generateShortPath(string $url): ?string
    {
        $prompt = <<<PROMPT
            Ты — генератор коротких ссылок.
            Твоя задача: по URL придумать короткий, осмысленный, латинский path (до 16 символов, без пробелов и спецсимволов), который отражает суть или тематику сайта или страницы. 
            Не копируй просто домен или путь, а придумай осмысленное слово или фразу, отражающую назначение или тему ссылки.
            Если это магазин — используй название товара, если новость — суть новости, если профиль — имя пользователя и т.д.
            Только само слово, без пояснений.

            Примеры:
            https://youtube.com/watch?v=abc123 → youtubevideo
            https://github.com/user/repo → githubrepo
            https://news.ycombinator.com/item?id=123 → hackernews
            https://ozon.ru/product/iphone-15-256gb-123456 → iphone15
            https://twitter.com/elonmusk → elonmusk
            https://habr.com/ru/articles/123456/ → habrarticle
            https://avito.ru/moskva/kvartiry/2-komnatnaya-123456 → avito2room
            https://rbc.ru/finances/2024/bitcoin-news-123 → bitcoinnews

            URL: $url
            Ответ:
        PROMPT;

        $response = $this->httpClient->request('POST', 'https://openrouter.ai/api/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'mistralai/mistral-7b-instruct',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ],
        ]);

        $data = $response->toArray(false);
        $text = $data['choices'][0]['message']['content'] ?? null;

        if ($text) {
            $text = preg_replace('/[^a-zA-Z0-9\-]/', '', trim($text));
            $text = strtolower($text);
        }

        return $text ?: null;
    }
}