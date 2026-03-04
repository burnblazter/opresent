<?php
// \app\ThirdParty\MythAuth\Helpers\auth_helper.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

use App\ThirdParty\MythAuth\Entities\User;

// =============================================
// ORIGINAL FUNCTIONS (JANGAN DIUBAH)
// =============================================

if (! function_exists('logged_in')) {
    function logged_in()
    {
        return service('authentication')->check();
    }
}

if (! function_exists('user')) {
    function user()
    {
        $authenticate = service('authentication');
        $authenticate->check();
        return $authenticate->user();
    }
}

if (! function_exists('user_id')) {
    function user_id()
    {
        $authenticate = service('authentication');
        $authenticate->check();
        return $authenticate->id();
    }
}

// =============================================
// MODIFIED: in_groups() DENGAN ALIASING
// =============================================

if (! function_exists('in_groups')) {
    /**
     * Ensures that the current user is in at least one of the passed in
     * groups. The groups can be passed in as either ID's or group names.
     * SUPPORT ALIASING: 'presensi' -> 'pegawai' (ID 3)
     *
     * @param mixed $groups
     */
    function in_groups($groups): bool
    {
        // ✅ MAPPING ALIAS KE NAMA ASLI
        $groupAliases = [
            'presensi' => 'pegawai',  // alias 'presensi' jadi 'pegawai'
            // Bisa tambah alias lain di sini
            // 'staff' => 'user',
            // 'superadmin' => 'admin',
        ];
        
        // Convert alias jadi nama asli
        if (is_string($groups) && isset($groupAliases[$groups])) {
            $groups = $groupAliases[$groups];
        } elseif (is_array($groups)) {
            $groups = array_map(function($group) use ($groupAliases) {
                return is_string($group) && isset($groupAliases[$group]) 
                    ? $groupAliases[$group] 
                    : $group;
            }, $groups);
        }
        
        // Lanjut ke logic original
        $authenticate = service('authentication');
        $authorize    = service('authorization');
        
        if ($authenticate->check()) {
            return $authorize->inGroup($groups, $authenticate->id());
        }
        
        return false;
    }
}

if (! function_exists('has_permission')) {
    function has_permission($permission): bool
    {
        $authenticate = service('authentication');
        $authorize    = service('authorization');
        if ($authenticate->check()) {
            return $authorize->hasPermission($permission, $authenticate->id()) ?? false;
        }
        return false;
    }
}

// =============================================
// HELPER TAMBAHAN: GET DISPLAY NAME
// =============================================

if (!function_exists('user_group_display')) {
    /**
     * Get display name dari group user
     * pegawai (ID 3) -> tampil sebagai "Presensi"
     * 
     * @return string
     */
    function user_group_display(): string
    {
        $displayNames = [
            'pegawai' => 'Presensi',
            'admin'   => 'Administrator',
            'user'    => 'Pengguna',
            // Tambah sesuai kebutuhan
        ];
        
        $authenticate = service('authentication');
        if ($authenticate->check()) {
            $userId = $authenticate->id();
            $groupModel = new \App\ThirdParty\MythAuth\Models\GroupModel();
            $groups = $groupModel->getGroupsForUser($userId);
            
            if (!empty($groups)) {
                $groupName = $groups[0]['name']; // ambil group pertama
                return $displayNames[$groupName] ?? ucfirst($groupName);
            }
        }
        
        return 'Guest';
    }

if (!function_exists('user_role_badge')) {
    function user_role_badge($role = null): array
    {
        if ($role === null) {
            $authenticate = service('authentication');
            if ($authenticate->check()) {
                $userId = $authenticate->id();
                $groupModel = new \App\ThirdParty\MythAuth\Models\GroupModel();
                $groups = $groupModel->getGroupsForUser($userId);
                $role = !empty($groups) ? $groups[0]['name'] : 'guest';
            } else {
                $role = 'guest';
            }
        }
        
        // 🎨 SIMPLE & CLEAN
        $badges = [
            'admin' => [
                'name' => 'Admin',
                'style' => 'background: #d63939; color: white; padding: 0.4em 0.8em; border-radius: 6px; font-weight: 500; font-size: 0.875rem;'
            ],
            'head' => [
                'name' => 'Head',
                'style' => 'background: #ae3ec9; color: white; padding: 0.4em 0.8em; border-radius: 6px; font-weight: 500; font-size: 0.875rem;'
            ],
            'pegawai' => [
                'name' => 'Presensi',
                'style' => 'background: #206bc4; color: white; padding: 0.4em 0.8em; border-radius: 6px; font-weight: 500; font-size: 0.875rem;'
            ],
            'user' => [
                'name' => 'Pengguna',
                'style' => 'background: #0ca678; color: white; padding: 0.4em 0.8em; border-radius: 6px; font-weight: 500; font-size: 0.875rem;'
            ],
            'helper' => [
                'name' => 'Helper',
                'style' => 'background: #f76707; color: white; padding: 0.4em 0.8em; border-radius: 6px; font-weight: 500; font-size: 0.875rem;'
            ],
            'guest' => [
                'name' => 'Guest',
                'style' => 'background: #6c757d; color: white; padding: 0.4em 0.8em; border-radius: 6px; font-weight: 500; font-size: 0.875rem;'
            ],
        ];
        
        return $badges[$role] ?? [
            'name' => ucfirst($role),
            'style' => 'background: #6c757d; color: white; padding: 0.4em 0.8em; border-radius: 6px; font-weight: 500; font-size: 0.875rem;'
        ];
    }
}

if (!function_exists('role_badge_html')) {
    function role_badge_html($role = null): string
    {
        $badge = user_role_badge($role);
        return sprintf(
            '<span style="%s">%s</span>',
            $badge['style'],
            $badge['name']
        );
    }
}
}