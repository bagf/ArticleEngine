<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */

Route::get('/', function() {
  $article1 = new ArticleRevision;
  $article2 = new ArticleRevision;
  $article3 = new ArticleRevision;
  
  $article1->articleBody = 'Well this is me testing in article 1';
  $article1->articleSection = 'Test Article';
  $article1->pageStart = 1;
  $article1->pageEnd = 1;
  $article1->pagination = '1-1';
  $article1->wordCount = count(explode(' ', $article1->articleBody));
  $article1->revisionID = 1;
  
//  $article2->article_body = 'Well this is me testing in article two, #yolo';
//  $article2->article_section = 'Test Article';
//  $article2->page_start = 1;
//  $article2->page_end = 1;
//  $article2->pagination = '1-1';
//  $article2->word_count = count(explode(' ', $article2->articleBody));
//  
//  $article3->article_body = 'Well this is me testing in article three (3) #swagger';
//  $article3->article_section = 'Testing 4 real';
//  $article3->page_start = 2;
//  $article3->page_end = 10;
//  $article3->pagination = '2-10';
//  $article3->word_count = count(explode(' ', $article3->articleBody));
  
//  $a1 = new Article;
//  $a1->save();
//  $a1->revisions()->save($article1);
//  $a2 = new Article;
//  $a2->save();
//  $a2->revisions()->save($article2);
//  $a3 = new Article;
//  $a3->save();
//  $a3->revisions()->save($article3);
  foreach (Article::all() AS $article) {
    
    $revision = $article->headRevision();
    if (is_null($revision)) {
      continue;
    }
    echo '<hr />';
    echo var_dump($revision->articleID);
  }
  
  
  return View::make('hello');
});
