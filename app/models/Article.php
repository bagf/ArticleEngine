<?php

class Article extends BaseModel {

  // Add your validation rules here
  public static $rules = [
          // 'title' => 'required'
  ];
  // Don't forget to fill this array
  protected $guarded = [
      'id',
  ];
  
  /**
   * Returns the current article revision
   */
  public function revision()
  {
    return $this->hasOne('ArticleRevision', 'article_id')->whereStatus('HEAD');
  }
  
  /**
   * Returns all article revisions except the head/current one
   */
  public function prevRevisions()
  {
    return $this->hasMany('ArticleRevision', 'article_id')->whereStatus('REVISED');
  }
  
  /**
   * Returns all article revisions
   */
  public function allRevisions()
  {
    return $this->hasMany('ArticleRevision', 'article_id');
  }
}
