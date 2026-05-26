<?php
/**
 * Admin\SettingsController — site ayarları (genel, mail, SEO).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Setting;

class SettingsController extends Controller
{
    protected const TABS = ['general', 'social', 'appearance', 'mail', 'seo', 'security', 'ai'];

    public function index(?string $tab = null): Response
    {
        $tab = $tab ?: (string) $this->request->query('tab', 'general');
        if (!in_array($tab, self::TABS, true)) $tab = 'general';

        return $this->view('admin.settings.index', [
            'tab'        => $tab,
            'general'    => Setting::group('general'),
            'social'     => Setting::group('social'),
            'appearance' => Setting::group('appearance'),
            'mail'       => Setting::group('mail'),
            'seo'        => Setting::group('seo'),
            'security'   => Setting::group('security'),
            'ai'         => Setting::group('ai'),
        ]);
    }

    public function save(): Response
    {
        $this->abortIfInvalidCsrf();
        $tab = (string) $this->request->input('_tab', 'general');

        $allowed = match ($tab) {
            'social' => [
                'social.facebook'   => 'string',
                'social.instagram'  => 'string',
                'social.twitter'    => 'string',
                'social.linkedin'   => 'string',
                'social.youtube'    => 'string',
                'social.github'     => 'string',
                'social.tiktok'     => 'string',
                'social.show_in_footer' => 'bool',
            ],
            'appearance' => [
                'appearance.logo_url'        => 'string',
                'appearance.logo_dark_url'   => 'string',
                'appearance.favicon_url'     => 'string',
                'appearance.og_default_url'  => 'string',
                'appearance.primary_color'   => 'string',
                'appearance.accent_color'    => 'string',
                'appearance.font_family'     => 'string',
                'appearance.footer_text'     => 'string',
                'appearance.copyright_text'  => 'string',
            ],
            'mail' => [
                'mail.driver'         => 'string',
                'mail.from_address'   => 'string',
                'mail.from_name'      => 'string',
                'mail.reply_to'       => 'string',
                'mail.admin_inbox'    => 'string',
                'mail.smtp_host'      => 'string',
                'mail.smtp_port'      => 'int',
                'mail.smtp_user'      => 'string',
                'mail.smtp_pass'      => 'string',
                'mail.smtp_encryption'=> 'string',
                'mail.error_reporter_enabled'     => 'bool',
                'mail.error_reporter_to'          => 'string',
                'mail.error_reporter_max_per_day' => 'int',
            ],
            'seo' => [
                'seo.meta_title'         => 'string',
                'seo.meta_description'   => 'string',
                'seo.meta_keywords'      => 'string',
                'seo.og_image'           => 'string',
                'seo.ga4_id'             => 'string',
                'seo.gtm_id'             => 'string',
                'seo.fb_pixel_id'        => 'string',
                'seo.google_verification'=> 'string',
                'seo.bing_verification'  => 'string',
                'seo.canonical_base'     => 'string',
                'seo.robots'             => 'string',
                'seo.sitemap_enabled'    => 'bool',
            ],
            'security' => [
                'security.rate_limit_per_hour'    => 'int',
                'security.max_login_attempts'     => 'int',
                'security.lockout_minutes'        => 'int',
                'security.session_lifetime_min'   => 'int',
                'security.csrf_lifetime_min'      => 'int',
                'security.honeypot_enabled'       => 'bool',
                'security.force_https'            => 'bool',
                'security.allow_registration'     => 'bool',
                'security.blocked_ips'            => 'string',
            ],
            // 'ai' dosya bazlı — form save edilmez.
            default => [
                'general.site_name'        => 'string',
                'general.site_tagline'     => 'string',
                'general.contact_email'    => 'string',
                'general.contact_phone'    => 'string',
                'general.address'          => 'string',
                'general.whatsapp_number'  => 'string',
                'general.timezone'         => 'string',
                'general.locale'           => 'string',
                'general.per_page'         => 'int',
                'general.maintenance'         => 'bool',
                'general.maintenance_message' => 'string',
                'general.maintenance_eta'     => 'string',
                'general.debug_bar'           => 'bool',
            ],
        };

        foreach ($allowed as $dotKey => $type) {
            $raw = $this->request->input(str_replace('.', '_', $dotKey));
            if ($type === 'bool') $raw = (string) (bool) $raw;
            Setting::set($dotKey, $raw, $type);
        }

        $this->flash('success', 'Ayarlar kaydedildi.');
        return $this->redirect($tab === 'general' ? '/admin/settings' : "/admin/settings/$tab");
    }
}
