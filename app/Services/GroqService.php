<?php
/**
 * GroqService — Groq Cloud Chat Completion API wrapper.
 *
 * Settings: ai.groq_api_key, ai.groq_model
 * Endpoint: https://api.groq.com/openai/v1/chat/completions
 *
 * Kullanım:
 *   if (GroqService::isReady()) {
 *       $post = GroqService::generateBlogPost('Direksiyonda dikkat etmeniz gereken 5 şey');
 *       // → ['title' => '...', 'excerpt' => '...', 'content' => '...']
 *   }
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\Logger;

class GroqService
{
    protected const API_URL        = 'https://api.groq.com/openai/v1/chat/completions';
    protected const DEFAULT_MODEL  = 'llama-3.3-70b-versatile';
    protected const REQUEST_TIMEOUT = 60;

    /** API key var mı? Yoksa "AI ile Oluştur" butonu disabled olur. */
    public static function isReady(): bool
    {
        return self::apiKey() !== '';
    }

    public static function apiKey(): string
    {
        return trim((string) Config::get('services.groq.api_key', ''));
    }

    public static function model(): string
    {
        return (string) (Config::get('services.groq.model') ?: self::DEFAULT_MODEL);
    }

    /**
     * Genel chat completion.
     * @return string AI yanıtı (text)
     * @throws \RuntimeException
     */
    public static function chat(array $messages, array $options = []): string
    {
        $key = self::apiKey();
        if ($key === '') {
            throw new \RuntimeException('Groq API key tanımlı değil. config/services.php → groq.api_key alanına yapıştırın.');
        }

        $payload = [
            'model'       => $options['model']       ?? self::model(),
            'messages'    => $messages,
            'temperature' => (float) ($options['temperature'] ?? 0.7),
            'max_tokens'  => (int)   ($options['max_tokens']  ?? 2048),
        ];
        if (!empty($options['json_mode'])) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $ch = curl_init(self::API_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $key,
            ],
            CURLOPT_POSTFIELDS    => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT       => self::REQUEST_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('Groq API bağlantı hatası: ' . $err);
        }

        $body = json_decode($response, true);
        if (!is_array($body)) {
            throw new \RuntimeException('Groq API geçersiz yanıt (json değil).');
        }

        if ($httpCode >= 400) {
            $msg = $body['error']['message'] ?? "HTTP $httpCode";
            Logger::error('Groq API error', ['code' => $httpCode, 'msg' => $msg]);
            throw new \RuntimeException('Groq API: ' . $msg);
        }

        $content = $body['choices'][0]['message']['content'] ?? '';
        if ($content === '') {
            throw new \RuntimeException('Groq API boş yanıt döndü.');
        }
        return (string) $content;
    }

    /**
     * Blog yazısı üret (Türkçe). Konu/anahtar fikirden başlık+özet+içerik (markdown).
     *
     * @return array{title:string,slug:string,excerpt:string,content:string,seo_title:string,seo_description:string,seo_keywords:string}
     */
    public static function generateBlogPost(string $topic, ?string $style = null): array
    {
        $style = $style ?: 'profesyonel, akıcı, SEO uyumlu';

        $system = <<<SYS
Sen Türkçe içerik üreten kıdemli bir blog editörüsün. Verilen konuya uygun, $style bir blog yazısı üretirsin.
ÇIKTI KESİNLİKLE şu JSON formatında olmalı (başka bir şey yazma, ham JSON):
{
  "title": "60 karakteri geçmeyen, çekici Türkçe başlık",
  "excerpt": "150-180 karakter arası özet (meta description için de uygun)",
  "content": "Markdown formatında 400-700 kelime arası ana metin. ## başlıklar, paragraflar, gerektiğinde madde listeleri kullan. Markdown link, kod, alıntı serbest. HTML kullanma.",
  "seo_title": "65 karakteri geçmeyen SEO başlığı",
  "seo_description": "155 karakteri geçmeyen meta description",
  "seo_keywords": "5-8 adet virgülle ayrılmış Türkçe anahtar kelime"
}
JSON dışı hiçbir karakter ekleme. Markdown blok başlatma. Direkt { ile başla, } ile bitir.
SYS;

        $raw = self::chat(
            [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user',   'content' => "Konu: $topic"],
            ],
            ['temperature' => 0.7, 'max_tokens' => 2400, 'json_mode' => true]
        );

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            // JSON gelmediyse — bazen modeller markdown ile sarar
            $clean = trim(preg_replace('/^```(?:json)?|```$/m', '', $raw) ?? '');
            $data = json_decode($clean, true);
        }
        if (!is_array($data)) {
            throw new \RuntimeException('Groq JSON parse edilemedi. Modelden geçersiz yanıt.');
        }

        return [
            'title'           => (string) ($data['title']           ?? ''),
            'excerpt'         => (string) ($data['excerpt']         ?? ''),
            'content'         => (string) ($data['content']         ?? ''),
            'seo_title'       => (string) ($data['seo_title']       ?? $data['title'] ?? ''),
            'seo_description' => (string) ($data['seo_description'] ?? $data['excerpt'] ?? ''),
            'seo_keywords'    => (string) ($data['seo_keywords']    ?? ''),
        ];
    }
}
