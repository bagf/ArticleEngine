<?php

class ArticleRevision extends BaseModel {

  // Add your validation rules here
  public static $rules = [
      'articleID' => 'integer',
      'revisionID' => 'integer',
      'articleBody' => 'required',
      'articleSection' => 'required',
      'pageEnd' => 'required',
      'pageStart' => 'required',
      'pagination' => 'required',
      'wordCount' => 'required|integer',
  ];
  // Don't forget to fill this array
  protected $guarded = [
      'id',
      'article_id',
  ];
  
  protected $hidden = [
      'created_at',
      'updated_at',
      'id',
      'status',
      'article',
  ];
  
  public function article()
  {
    return $this->belongsTo('Article', 'article_id');
  }
  
  public static function boot()
  {
    parent::boot();
    
    static::creating(function($model) {
      if (!$model->exists) {
        // Check to see if Article relationship exists
        $article = $model->article;
        $currentRevID = null;
        if (!is_null($article)) {
          $currentRevID = $article->allRevisions()->max('revision_id');
        }
        if (is_null($currentRevID)) {
          $currentRevID = 0;
        }
        $model->revision_id = (intval($currentRevID) + 1);
      }
      return true;
    });
    
    static::saving(function($model){
      $dirtyAttributes = $model->getDirty();
      // Check if changes exist
      if  (count($dirtyAttributes) > 0 && !is_null($model->article_id)) {
        $article = $model->article;
        /*
         * Check if the model:
         * - exists
         * - is HEAD
         * - something has changed besides the IDs
         */
        if ($model->exists &&
            $model->status === 'HEAD' &&
            count(array_except($dirtyAttributes, array('article_id', 'revision_id', 'status'))) > 0) {
          $newRevision = $model->replicate();
          $newRevision->status = 'HEAD';
          // Get new revision ID
          $article->revision()->save($newRevision);
          $model->syncOriginal();
          $model->status = 'REVISED';
        }
        // Check to see if the revisions status as changed to HEAD
        if (isset($dirtyAttributes['status']) && $dirtyAttributes['status'] === 'HEAD') {
          /*
           * When the revision has a HEAD status set the current HEAD to
           * REVISED (if it exists)
           */
            $articleRevision = $article->revision;
            if (!is_null($articleRevision)) {
              $articleRevision->status = 'REVISED';
            }
            $articleRevision->save();
        }
        return true;
      }
      return false;
    });
  }
}
