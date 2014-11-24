# Article Engine

Article Engine is a Laravel based application that allows users create, delete, edit and view articles via an easy to use JSON API. It supports non-destructive edits by creating article revisions when changes are applied. Articles can be rolled back or forward to any previous revisions using the provided API calls.

# Requirements

Please note that the following version requirements have been determined during the early stages of development. This application has not been tested in environments that differ from these requirements:

- PHP >= 5.6
- MySQL >= 5.5
- Composer >= 1.0
- Laravel = 4.2

# Testing

Article Engine uses [Laravel Homestead](http://laravel.com/docs/4.2/homestead) to provision our Vagrant development environment.
(Steps to get started coming soon)

# Issues/limitations

There are few issues I’d like to mention before the software gets released, if other issues occur that are not in this list please create an new issue ticket.

- No user authentication currently implemented
- No working unit tests

# Documentation

In this API: articles are represented as serialized JSON objects, these object will contain properties as specified on the [schema.org](http://schema.org/Article) page. If a request fails HTTP error 500 will be returned with error content if applicable. (Note that the error content might not be in JSON format)

## Creating new articles

This call will create a new article in the system and return a unique article ID which can be used in other requests.

**Request:**
`POST http://articleengine.app/article/new`
```
[
    {
        "articleBody": "The actual body of the article.",
        "articleSection": "Articles may belong to one or more 'sections' in a magazine or newspaper, such as Sports, Lifestyle, etc.",
        "pageEnd": "138",
        "pageStart": "135",
        "pagination": "135-138",
        "wordCount": 23
    }
]
```

 **Response:**
 `HTTP 200`
```
[
    {
        "articleID": 1
    }
]
```

## Deleting existing articles

Use this call with caution as it will permanently delete the specified article including any reversions made to it.

**Request:**
`DELETE http://articleengine.app/article/(article ID)/delete`

 **Response:**
 `HTTP 200`
 
## Applying article revisions

This call will apply the selected revision ID to the specified article ID in some cases effectively rolling back any changes made to an article. Note that the revision ID has to be a valid revision for the specified article ID, otherwise this call will return HTTP error 500.

**Request**
`PUT/PATCH http://articleengine.app/article/(article ID)/apply_revision/(revision ID)`

**Response**
`HTTP 200`

## Listing available articles

This call will output all the available article objects including the article ID in chronological order.

**Request**
`GET http://articleengine.app/article/list`

**Response**
`HTTP 200`
```
[
    {
        "articleID": 1,
        "articleBody": "The actual body of the article.",
        "articleSection": "Articles may belong to one or more 'sections' in a magazine or newspaper, such as Sports, Lifestyle, etc.",
        "pageEnd": "138",
        "pageStart": "135",
        "pagination": "135-138",
        "wordCount": 23
    },
    ...
]
```

##### Listing a range of articles

You can request a specified range of articles if you wish to optimize the size of responses. The last two slugs in the request URL determine the list range for example: `GET http://articleengine.app/article/list/10/25` will list 25 articles starting from the 10th latest entry. Optionally you may omit the last slug for example: `GET http://articleengine.app/article/list/25` will list 25 articles starting at the latest entry.

## Editing existing articles

Use this call to modify an existing article, by doing so a revision will be made as to record the articles previous values. Note that if no value has been changed no revision will be made.

**Request:**
`PUT/PATCH http://articleengine.app/article/(article ID)/edit`
```
[
    {
        "articleBody": "The actual body of the article.",
        "articleSection": "Articles may belong to one or more 'sections' in a magazine or newspaper, such as Sports, Lifestyle, etc.",
        "pageEnd": "138",
        "pageStart": "135",
        "pagination": "135-138",
        "wordCount": 23
    }
]
```

 **Response:**
 `HTTP 200`
```
[
    {
        "articleID": 1,
        "revisionID": 2,
    }
]
```

## Viewing articles

This call will retrieve an article object matching the specified article ID, HTTP error 404 will be returned if the article ID is not found.

**Request**
`GET http://articleengine.app/article/(article ID)/view`

**Response**
`HTTP 200`
```
[
    {
        "articleID": (articleID),
        "articleBody": "The actual body of the article.",
        "articleSection": "Articles may belong to one or more 'sections' in a magazine or newspaper, such as Sports, Lifestyle, etc.",
        "pageEnd": "138",
        "pageStart": "135",
        "pagination": "135-138",
        "wordCount": 23
    }
]
```

## Listing article revisions

This call will retrieve and output every article revision made since the article was created. Each article reversion will include a reversion ID property which can be used within other reversion API calls.

**Request**
`GET http://articleengine.app/article/(article ID)/view_revisions`

**Response**
`HTTP 200`
```
[
    {
        "articleID": (article ID),
        "revisionID": (revision ID),
        "articleBody": "The actual body of the article.",
        "articleSection": "Articles may belong to one or more 'sections' in a magazine or newspaper, such as Sports, Lifestyle, etc.",
        "pageEnd": "138",
        "pageStart": "135",
        "pagination": "135-138",
        "wordCount": 23
    },
    ...
]
```
