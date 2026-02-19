<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Some environments have an old "lembretes" table created outside migrations.
        DB::statement("ALTER TABLE lembretes ADD COLUMN IF NOT EXISTS data_aviso TIMESTAMP NULL");

        DB::statement("
            DO $$
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM information_schema.columns
                    WHERE table_schema = 'public'
                      AND table_name = 'lembretes'
                      AND column_name = 'data_lembrete'
                ) THEN
                    UPDATE lembretes
                    SET data_aviso = data_lembrete
                    WHERE data_aviso IS NULL;
                END IF;
            END $$;
        ");
    }

    public function down(): void
    {
        // No destructive rollback for legacy data patch.
    }
};
