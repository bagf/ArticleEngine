<?php
/**
 * This class is used by the ArticlesController to manage article instances, use
 * this class to extend functionality within this application.
 *
 * @author bagf
 */
class ArticlesAPI {
  
  /**
   * This call will retrieve and output every article revision made since the
   * article was created.
   * @param int $articleID
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function listArticleRevisions($articleID) {
    return Article::findOrFail($articleID)->prevRevisions();
  }
  
  /**
   * This call will output all the available article objects including the
   * in chronological order.
   * @param int $start
   * @param int $limit
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function listArticles($start = null, $limit = null) {
    $articles = Article::with('revision');
    if (!is_null($start)) {
      if (!is_null($limit)) {
        $articles = $articles->take($limit)->skip($start);
      } else {
        $articles = $articles->take($start);
      }
    }
    return $articles->orderBy('created_at', 'desc');
  }
  
  /**
   * 
   * @param int $articleID
   * @return \Article
   */
  public function viewArticle($articleID) {
    return Article::findOrFail($articleID);
  }
  
  /**
   * 
   * @param int $articleID
   */
  public function deleteArticle($articleID) {
    Article::findOrFail($articleID)->delete();
  }
  
  /**
   * Use this call to modify an existing article, by doing so a revision will be
   * made as to record the articles previous values.
   * @param array $articleAttributes
   * @return \ArticleRevision
   */
  public function editArticle($articleAttributes) {
    $article = Article::findOrFail($articleAttributes['articleID']);
    $revision = $this->saveModelAttributes($article->revision, $articleAttributes);
    $revision->save();
    
    return Article::findOrFail($articleAttributes['articleID']);
  }
  
  /**
   * This call will create a new article in the system and return a new Article
   * instance.
   * @param array $articleAttributes
   * @return \Article
   */
  public function createArticle($articleAttributes) {
    $article = new Article;
    $revision = $this->saveModelAttributes(new ArticleRevision, $articleAttributes);
    // Set as first revision
    $revision->revisionID = 1;
    $article->save();
    $article->revision()->save($revision);
    
    return $article;
  }
  
  /**
   * This call will apply the selected revision ID to the specified article ID
   * in some cases effectively rolling back any changes made to an article.
   * @param int $articleID
   * @param int $revisionID
   * @return \Article
   */
  public function applyArticleRevision($articleID, $revisionID) {
    $article = Article::findOrFail($articleID);
    $revision = $article->prevRevisions()
                        ->where('revision_id', '=', $revisionID)
                        ->firstOrFail();
    $revision->status = 'HEAD';
    $article->revision()->save($revision);
    return $article;
  }
  
  /**
   * A clean array should be passed in the last parameter only including values
   * the model can be set without error.
   * @param \BaseModel $model
   * @param array $attributes
   */
  protected function saveModelAttributes(BaseModel $model, $attributes) {
    /**
     * Update each attribute this way instead of using the $revision->update()
     * method. This loop will use the overridden magic method __set to reference
     * attributes in camel-case.
     */
    foreach ($attributes AS $camelAttribute => $value) {
      $model->{$camelAttribute} = $value;
    }
    return $model;
  }
}
