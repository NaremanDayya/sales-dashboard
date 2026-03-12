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
        Schema::table('conversations', function (Blueprint $table) {
            // Index for sender_id and receiver_id lookups
            $table->index(['sender_id', 'receiver_id'], 'idx_conversations_sender_receiver');
            
            // Index for receiver_id and sender_id lookups (reverse)
            $table->index(['receiver_id', 'sender_id'], 'idx_conversations_receiver_sender');
            
            // Index for client_id lookups
            $table->index('client_id', 'idx_conversations_client');
            
            // Composite index for created_at ordering
            $table->index('created_at', 'idx_conversations_created_at');
        });

        Schema::table('messages', function (Blueprint $table) {
            // Composite index for conversation_id and created_at (for latest message queries)
            $table->index(['conversation_id', 'created_at'], 'idx_messages_conversation_created');
            
            // Index for read_at and sender_id (for unread count queries)
            $table->index(['conversation_id', 'read_at', 'sender_id'], 'idx_messages_unread');
            
            // Index for sender_id lookups
            $table->index('sender_id', 'idx_messages_sender');
            
            // Index for receiver_id lookups
            $table->index('receiver_id', 'idx_messages_receiver');
            
            // Index for finding max id per conversation (latest message)
            $table->index(['conversation_id', 'id'], 'idx_messages_conversation_id');
        });

        Schema::table('clients', function (Blueprint $table) {
            // Index for company_name search
            if (!Schema::hasIndex('clients', 'idx_clients_company_name')) {
                $table->index('company_name', 'idx_clients_company_name');
            }
            
            // Index for sales_rep_id lookups
            if (!Schema::hasIndex('clients', 'idx_clients_sales_rep')) {
                $table->index('sales_rep_id', 'idx_clients_sales_rep');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex('idx_conversations_sender_receiver');
            $table->dropIndex('idx_conversations_receiver_sender');
            $table->dropIndex('idx_conversations_client');
            $table->dropIndex('idx_conversations_created_at');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('idx_messages_conversation_created');
            $table->dropIndex('idx_messages_unread');
            $table->dropIndex('idx_messages_sender');
            $table->dropIndex('idx_messages_receiver');
            $table->dropIndex('idx_messages_conversation_id');
        });

        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasIndex('clients', 'idx_clients_company_name')) {
                $table->dropIndex('idx_clients_company_name');
            }
            if (Schema::hasIndex('clients', 'idx_clients_sales_rep')) {
                $table->dropIndex('idx_clients_sales_rep');
            }
        });
    }
};
