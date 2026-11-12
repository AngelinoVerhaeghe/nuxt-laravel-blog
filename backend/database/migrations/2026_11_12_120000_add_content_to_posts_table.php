<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Splits a post into a short summary (`description`, for cards and previews) and
 * the full text (`content`, in Markdown). Until now `description` held the full text.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->longText('content')->nullable()->after('description');
        });

        DB::table('posts')->orderBy('id')->each(function (object $post) {
            DB::table('posts')->where('id', $post->id)->update([
                'content' => $post->description,
                'description' => Str::limit(trim(strip_tags(Str::markdown($post->description))), 160),
            ]);
        });
    }

    public function down(): void
    {
        DB::table('posts')->orderBy('id')->each(function (object $post) {
            DB::table('posts')->where('id', $post->id)->update([
                'description' => $post->content ?? $post->description,
            ]);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
};
