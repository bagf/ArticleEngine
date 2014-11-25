<?php

class Article extends BaseModel {

  // Add your validation rules here
  public static $rules = [
          // 'title' => 'required'
  ];
  // Don't forget to fill this array
  protected $guarded = [
      'id'
  ];

}
