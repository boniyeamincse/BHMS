<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->foreignId('hospital_type_id')->nullable()->after('status')->constrained()->onDelete('set null');
            $table->foreignId('subscription_plan_id')->nullable()->after('hospital_type_id')->constrained()->onDelete('set null');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->nullable()->after('subscription_plan_id');
            $table->timestamp('subscription_start_date')->nullable()->after('billing_cycle');
            $table->timestamp('subscription_end_date')->nullable()->after('subscription_start_date');
            $table->timestamp('trial_end_date')->nullable()->after('subscription_end_date');
            $table->timestamp('last_billed_at')->nullable()->after('trial_end_date');
            $table->enum('payment_status', ['trial', 'active', 'past_due', 'cancelled', 'expired'])->default('trial')->after('last_billed_at');
            $table->boolean('auto_renew')->default(true)->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropForeign(['hospital_type_id']);
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn([
                'hospital_type_id',
                'subscription_plan_id',
                'billing_cycle',
                'subscription_start_date',
                'subscription_end_date',
                'trial_end_date',
                'last_billed_at',
                'payment_status',
                'auto_renew'
            ]);
        });
    }
};
