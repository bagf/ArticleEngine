<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;

class ArticlesController extends BaseController {
  /**
   *
   * @var \ArticlesAPI
   */
  protected $api;
  
  public function __construct(ArticlesAPI $api) {
    $this->api = $api;
  }

  /**
   * Display a listing of articles
   *
   * @param $start Optional starting article or limit if the last param is null
   * @param $limit Optional article limit
   * 
   * @return Response
   */
  public function index($start = null, $limit = null)
  {
    $articles = $this->api->listArticles($start, $limit);
    $jsonResponse = array();
    foreach ($articles->get() AS $article) {
      $revision = $article->revision;
      if (is_null($revision)) {
        continue;
      }
      $jsonResponse[] = $revision->toCamelArray();
    }
    
    return Response::json($jsonResponse, 200);
  }

  /**
   * Store a newly created article in storage.
   *
   * @return Response
   */
  public function store()
  {
    $responseArray = [];
    $failFlag = false;
    foreach ($this->getJsonRequest() AS $attributes) {
      $attributes = $this->getArticleAttributes($attributes, array('articleID', 'revisionID'));
      $validator = Validator::make($attributes, ArticleRevision::$rules);
      if ($validator->fails()) {
        $responseArray[] = $validator->messages();
        $failFlag = true;
      } else {
        $revision = $this->api->createArticle($attributes)->revision;
        $responseArray[] = $revision->toCamelArray();
      }
    }
    return Response::json($responseArray, ($failFlag?400:200));
  }

  /**
   * Display the specified article.
   *
   * @param  int  $id
   * @return Response
   */
	public function show($id)
  {
    try {
      $article = $this->api->viewArticle($id);
      $revision = $article->revision;
      return Response::json(array($revision->toCamelArray()), 200);
    } catch (ModelNotFoundException $ex) {
      App::abort(404);
    }
  }

  /**
   * Update the specified article in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
    $allAttributes = $this->getJsonRequest();
    $inputAttributes = end($allAttributes);
    $inputAttributes['articleID'] = $id;
    
    $attributes = $this->getArticleAttributes($inputAttributes, array('revisionID'));
    $validator = Validator::make($attributes, array('wordCount' => 'integer'));
    if ($validator->fails()) {
      return Response::json(array($validator->messages()), 400);
    } else {
      try {
        $article = $this->api->editArticle($attributes);
        $revision = $article->revision;
        return Response::json(array($revision->toCamelArray()), 200);
      } catch (ModelNotFoundException $ex) {
        App::abort(404);
      }
    }
  }

  /**
   * Remove the specified article from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
    try {
      $this->api->deleteArticle($id);
      return Response::json(array(), 200);
    } catch (ModelNotFoundException $ex) {
      App::abort(404);
    }
  }

  /**
   * 
   * @param int $id
   * @param int $revisionID
   */
  public function applyRevision($id, $revisionID)
  {
    try {
      $this->api->applyArticleRevision($id, $revisionID);
      return Response::json(array(), 200);
    } catch (ModelNotFoundException $ex) {
      App::abort(400);
    }
  }
  
  /**
   * 
   * @param int $id
   */
  public function viewRevisions($id)
  {
    $revisions = $this->api->listArticleRevisions($id);
    $jsonResponse = array();
    foreach ($revisions->get() AS $revision) {
      $jsonResponse[] = $revision->toCamelArray();
    }
    
    return Response::json($jsonResponse, 200);
  }
  
}
