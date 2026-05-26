<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete()->after('issued_at');
            $table->timestamp('received_at')->nullable()->after('received_by');
            $table->string('received_note', 500)->nullable()->after('received_at');
        });
    }

    public function down(): void
    {
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('received_by');
            $table->dropColumn(['received_at', 'received_note']);
        });
    }
};
