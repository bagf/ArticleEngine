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
  
  public function revisions()
  {
    return $this->hasMany('ArticleRevision', 'article_id');
  }
  
  /**
   * Returns the current article revision
   */
  public function headRevision()
  {
    return $this->revisions()->where('status', '=', 'HEAD')->first();
  }
  
  /**
   * Returns all article revisions except the head/current one
   */
  public function previousRevisions()
  {
    return $this->revisions()->where('status', '=', 'REVISED');
  }
}
