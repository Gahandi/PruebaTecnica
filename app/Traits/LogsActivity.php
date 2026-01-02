<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    public static function bootLogsActivity()
    {
        // Registrar cuando se crea un modelo
        static::created(function ($model) {
            $model->logActivity('created', $model->getCreatedDescription(), $model->getLoggableAttributes());
        });

        // Registrar cuando se actualiza un modelo
        static::updated(function ($model) {
            $changes = $model->getChanges();
            $original = $model->getOriginal();

            // Filtrar campos que no queremos registrar
            $ignoredFields = $model->getIgnoredLogFields();
            $changes = array_diff_key($changes, array_flip($ignoredFields));

            if (!empty($changes)) {
                $oldValues = [];
                $newValues = [];

                foreach ($changes as $key => $value) {
                    if (!in_array($key, $ignoredFields)) {
                        $oldValues[$key] = $original[$key] ?? null;
                        $newValues[$key] = $value;
                    }
                }

                $model->logActivity(
                    'updated',
                    $model->getUpdatedDescription(),
                    ['changed_fields' => array_keys($changes)],
                    $oldValues,
                    $newValues
                );
            }
        });

        // Registrar cuando se elimina un modelo
        static::deleted(function ($model) {
            $model->logActivity('deleted', $model->getDeletedDescription(), $model->getLoggableAttributes());
        });

        // Registrar cuando se restaura un modelo (si usa SoftDeletes)
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->logActivity('restored', $model->getRestoredDescription(), $model->getLoggableAttributes());
            });
        }
    }

    /**
     * Registrar una actividad para este modelo
     */
    public function logActivity($action, $description, $properties = [], $oldValues = null, $newValues = null)
    {
        ActivityLog::log(
            $action,
            $description,
            get_class($this),
            $this->getKey(),
            $properties,
            $oldValues,
            $newValues
        );
    }

    /**
     * Obtener campos que se deben ignorar al registrar cambios
     */
    protected function getIgnoredLogFields(): array
    {
        return property_exists($this, 'logIgnoredFields')
            ? $this->logIgnoredFields
            : ['updated_at', 'created_at', 'remember_token', 'email_verified_at'];
    }

    /**
     * Obtener atributos loguables del modelo
     */
    protected function getLoggableAttributes(): array
    {
        $attributes = $this->attributesToArray();
        $ignored = $this->getIgnoredLogFields();

        // También ocultar campos sensibles
        $hidden = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];

        return array_diff_key($attributes, array_flip(array_merge($ignored, $hidden)));
    }

    /**
     * Obtener el nombre del modelo para descripciones
     */
    protected function getModelDisplayName(): string
    {
        $className = class_basename($this);
        return ActivityLog::$modelTranslations[$className] ?? $className;
    }

    /**
     * Obtener identificador legible del modelo
     */
    protected function getModelIdentifier(): string
    {
        // Intentar obtener un nombre o título descriptivo
        if (property_exists($this, 'logIdentifierField') && isset($this->{$this->logIdentifierField})) {
            return $this->{$this->logIdentifierField};
        }

        // Campos comunes para identificar
        $identifierFields = ['name', 'title', 'email', 'code', 'slug'];
        foreach ($identifierFields as $field) {
            if (isset($this->$field) && !empty($this->$field)) {
                return $this->$field;
            }
        }

        return "#{$this->getKey()}";
    }

    /**
     * Descripción para creación
     */
    protected function getCreatedDescription(): string
    {
        return "Se creó {$this->getModelDisplayName()}: {$this->getModelIdentifier()}";
    }

    /**
     * Descripción para actualización
     */
    protected function getUpdatedDescription(): string
    {
        return "Se actualizó {$this->getModelDisplayName()}: {$this->getModelIdentifier()}";
    }

    /**
     * Descripción para eliminación
     */
    protected function getDeletedDescription(): string
    {
        return "Se eliminó {$this->getModelDisplayName()}: {$this->getModelIdentifier()}";
    }

    /**
     * Descripción para restauración
     */
    protected function getRestoredDescription(): string
    {
        return "Se restauró {$this->getModelDisplayName()}: {$this->getModelIdentifier()}";
    }

    /**
     * Registrar acción personalizada
     */
    public function logCustomAction($action, $description, $properties = [])
    {
        ActivityLog::log(
            $action,
            $description,
            get_class($this),
            $this->getKey(),
            $properties
        );
    }
}
