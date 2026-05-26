<?php
/**
 * RBAC — Rol bazlı yetki matrisi.
 *
 * '*'           → her şey
 * 'admin.*'     → admin altındaki tüm permission'lar
 * 'blog.create' → tek izin
 *
 * Kullanım: can('admin.users.delete')  → bool
 *           is_role('admin', 'editor') → bool
 *
 * @package IEF Framework
 */

return [
    'roles' => [
        'superadmin' => [
            'label'       => 'Süper Admin',
            'permissions' => ['*'],
        ],
        'admin' => [
            'label'       => 'Admin',
            'permissions' => [
                'admin.access',
                'blog.*', 'media.*', 'messages.*', 'users.*',
                'settings.*', 'analytics.*', 'logs.*',
                'editor.*', 'appointments.*',
            ],
        ],
        'editor' => [
            'label'       => 'Editör',
            'permissions' => [
                'admin.access',
                'blog.*', 'media.*', 'messages.view',
                'editor.*',
            ],
        ],
        'staff' => [
            'label'       => 'Personel',
            'permissions' => [
                'admin.access',
                'messages.*', 'appointments.view', 'appointments.update',
            ],
        ],
        'user' => [
            'label'       => 'Kullanıcı',
            'permissions' => ['profile.*'],
        ],
    ],
];
