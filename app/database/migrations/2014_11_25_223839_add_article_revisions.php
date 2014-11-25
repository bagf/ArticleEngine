<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddArticleRevisions extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::table('article_revisions', function(Blueprint $table) {
      $table->integer('revision_id')->unsigned();
      $table->integer('article_id')->unsigned();
      $table->longtext('article_body');
      $table->text('article_section');
      $table->string('page_end');
      $table->string('page_start');
      $table->string('pagination');
      $table->integer('word_count')->unsigned();
      $table->enum('status', array('HEAD', 'REVISED'))->default('HEAD');
      // Add Index
      $table->index(array('revision_id', 'article_id'));
      // Add foreign key
      $table->foreign('article_id')
              ->references('id')->on('articles')
              ->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::table('article_revisions', function(Blueprint $table) {
      // Drop indexes and foreign keys first
      $table->dropIndex('article_revisions_revision_id_article_id_index');
      $table->dropForeign('article_id');
      // Then drop the coloums
      $table->dropColumn('revision_id');
      $table->dropColumn('article_id');
      $table->dropColumn('article_body');
      $table->dropColumn('article_section');
      $table->dropColumn('page_end');
      $table->dropColumn('page_start');
      $table->dropColumn('pagination');
      $table->dropColumn('word_count');
      $table->dropColumn('status');
    });
  }

}
