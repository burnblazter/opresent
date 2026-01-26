<?php

namespace App\Models;

use CodeIgniter\Model;

class FileManagerModel extends Model
{
    protected $table = 'file_manager_settings';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['setting_key', 'setting_value'];

    public function getSetting($key)
    {
        $result = $this->where('setting_key', $key)->first();
        return $result ? $result['setting_value'] : null;
    }

    public function updateSetting($key, $value)
    {
        $existing = $this->where('setting_key', $key)->first();
        
        if ($existing) {
            return $this->update($existing['id'], ['setting_value' => $value]);
        } else {
            return $this->insert(['setting_key' => $key, 'setting_value' => $value]);
        }
    }
}