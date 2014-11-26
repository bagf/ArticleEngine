<?php

abstract class BaseController extends Controller {

  /**
   * Setup the layout used by the controller.
   *
   * @return void
   */
  protected function setupLayout()
  {
    if (!is_null($this->layout)) {
      $this->layout = View::make($this->layout);
    }
  }

  protected function getArticleAttributes($attr, $except)
  {
    $attributes = array_keys(ArticleRevision::$rules);
    return array_only($attr, array_except($attributes, $except));
  }

  protected function getJsonRequest()
  {
    if (!Request::isJson()) {
      App::abort(400, 'Invalid request format expecting JSON');
    }
    $request = Request::instance();
    return $request->json()->all();
  }

}
