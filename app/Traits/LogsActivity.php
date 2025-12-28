<?php

namespace App\Traits;

use App\Models\ActivityLog;

/**
 * Trait untuk menambahkan activity logging ke controller
 * 
 * Usage:
 * use LogsActivity;
 * 
 * $this->logActivity('create', 'Membuat berita baru', $news, null, $news->toArray());
 */
trait LogsActivity
{
    /**
     * Log an activity from controller
     */
    protected function logActivity(
        string $action,
        string $description,
        $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ActivityLog {
        return ActivityLog::log($action, $description, $model, $oldValues, $newValues);
    }

    /**
     * Log create action
     */
    protected function logCreate(string $modelName, $model): void
    {
        $this->logActivity(
            'create',
            "Membuat {$modelName} baru: {$this->getModelTitle($model)}",
            $model,
            null,
            $model->toArray()
        );
    }

    /**
     * Log update action
     */
    protected function logUpdate(string $modelName, $model, array $oldValues): void
    {
        $this->logActivity(
            'update',
            "Mengupdate {$modelName}: {$this->getModelTitle($model)}",
            $model,
            $oldValues,
            $model->toArray()
        );
    }

    /**
     * Log delete action
     */
    protected function logDelete(string $modelName, $model): void
    {
        $this->logActivity(
            'delete',
            "Menghapus {$modelName}: {$this->getModelTitle($model)}",
            $model,
            $model->toArray(),
            null
        );
    }

    /**
     * Get a displayable title for the model
     */
    protected function getModelTitle($model): string
    {
        return $model->title
            ?? $model->name
            ?? $model->nama
            ?? $model->judul
            ?? "ID: {$model->id}";
    }
}
