<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait ModelTraits
{

    public function getColumns(): array
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function getIndexes(): array
    {
        $indexes = [];
        if (App::environment('testing')){
            return $indexes;
        }

        $table_name = $this->getTable();
        $result = DB::select("SHOW INDEX FROM $table_name;");

        if (!empty($result)) {
            $indexes = collect($result)->pluck('Column_name')->toArray();
        }

        return $indexes;
    }

    public function getForeignKeys(): array
    {
        $foreign_keys = collect([]);

        if (App::environment('testing')){
            return [];
        }

        $table_name = $this->getTable();
        $db_name = $this->getConnection()->getDatabaseName();

        $result = DB::select("SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, "
            . "REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE "
            . "WHERE TABLE_NAME = '$table_name' AND CONSTRAINT_SCHEMA='$db_name' AND REFERENCED_COLUMN_NAME IS NOT NULL;");

        if (!empty($result)) {
            $foreign_keys = collect($result)->filter( function ($index) {
                return Str::of($index->CONSTRAINT_NAME)->endsWith('_foreign');
            })->pluck('COLUMN_NAME');
        }

        return $foreign_keys->toArray();
    }

    public function hasIndex(string $index): bool
    {
        $indexed_columns = $this->getIndexes();
        return in_array($index, $indexed_columns);
    }

    public function hasForeignKey(string $index): bool
    {
        $foreign_keys = $this->getForeignKeys();
        return in_array($index, $foreign_keys);
    }

    public function filterFillableAttributes(array $values, array $appends=[]): array
    {
        $all_values = array_merge($values, $appends);

        return array_filter($all_values, function ($element) {
            return in_array($element, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function filterEditableAttributes(array $values): array
    {
        return array_filter($values, function ($element) {
            if (!empty($this->editable)) {
                return in_array($element, $this->editable);
            }
            return in_array($element, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);
    }
}
