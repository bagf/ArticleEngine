# Article Engine

Article Engine is a Laravel based application that allows users create, delete, edit and view articles via an easy to use JSON API. It supports non-destructive edits by creating article revisions when changes are applied. Articles can be rolled back or forward to any previous revisions using the provided API calls.

# Requirements

Please note that the following version requirements have been determined during the early stages of development. This application has not been tested in environments that differ from these requirements:

- PHP >= 5.6
- MySQL >= 5.5
- Composer >= 1.0
- Laravel = 4.2

# Testing

The following steps detail the process involved when deploying this application to staging or production environments.

### Cloning

Use Git to clone this repository and change directory by running these commands:

```
git clone https://github.com/bagf/ArticleEngine.git articleengine
cd articleengine/
```

### Installing

Once cloned install the projects dependencies using [Composer](https://getcomposer.org/), run this command:

```
composer install
```

Once composer has installed everything you should be able to configure Apache’s or Nginx’s document root to the `public/` folder. Article Engine can be deployed using [Laravel Homestead](http://laravel.com/docs/4.2/homestead) to provision Vagrant as both the development and testing environment. Make sure Homestead is installed and configured then run the following to add articleengine.app as a Homestead Site.

On Linux or OS X:
```
sudo sh -c "echo 192.168.10.10 articleengine.app >> /etc/hosts"
homestead up
homestead ssh
```

On Windows:
```
echo 192.168.10.10 articleengine.app >> %windir%\System32\drivers\etc\hosts
homestead.bat up
homestead.bat ssh
```

Once you receive as vagrant@homestead prompt add the articleengine.app site with the serve command and run the Artisan migrate command to automatically generate the database structure

```
serve domain.app /home/vagrant/Code/articleengine/public
cd ~/Code/articleengine
php artisan migrate
exit
```


Alternatively you can also setup the Site by adding articleengine.app to the sites section in the `~/.homestead/Homestead.yml` configuration file. Example sites section:

```
sites:
    - map: articleengine.app
      to: /home/vagrant/Code/articleengine/public
```

Once the Homestead site is added you should be able to access http://articleengine.app and being testing.

# Issues/limitations

There are few issues I’d like to mention before the software gets released, if other issues occur that are not in this list please create an new issue ticket.

- No user authentication currently implemented
- No working unit tests

# Documentation

In this API: articles are represented as serialized JSON objects, these object will contain properties as specified on the [schema.org](http://schema.org/Article) page. If a request fails HTTP error 400 will be returned with error content if applicable. (Note that the error content might not be in JSON format)

## Creating new articles

This call will create a new article in the system and return a unique article ID which can be used in other requests.

**Request:**
`POST http://articleengine.app/article`
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
        "revisionID": 1,
        "articleID": (unique article ID),
        "articleBody": "The actual body of the article.",
        "articleSection": "Articles may belong to one or more 'sections' in a magazine or newspaper, such as Sports, Lifestyle, etc.",
        "pageEnd": "138",
        "pageStart": "135",
        "pagination": "135-138",
        "wordCount": 23
    }
]
```

## Deleting existing articles

Use this call with caution as it will permanently delete the specified article including any reversions made to it.

**Request:**
`DELETE http://articleengine.app/article/(article ID)`

 **Response:**
 `HTTP 200`
 
## Applying article revisions

This call will apply the selected revision ID to the specified article ID in some cases effectively rolling back any changes made to an article. Note that the revision ID has to be a valid revision for the specified article ID, otherwise this call will return HTTP error 400.

**Request**
`PUT/PATCH http://articleengine.app/article/(article ID)/apply_revision/(revision ID)`

**Response**
`HTTP 200`

## Listing available articles

This call will output all the available article objects including the article ID in chronological order.

**Request**
`GET http://articleengine.app/article`

**Response**
`HTTP 200`
```
[
    {
        "revisionID": (revision ID),
        "articleID": (article ID),
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

You can request a specified range of articles if you wish to optimize the size of responses. The last two slugs in this request URL determine the list range for example: `GET http://articleengine.app/article/limit/10/25` will list 25 articles starting from the 10th latest entry. Optionally you may omit the last slug for example: `GET http://articleengine.app/article/limit/25` will list 25 articles starting at the latest entry.

## Editing existing articles

Use this call to modify an existing article, by doing so a revision will be made as to record the articles previous values. Note that if no value has been changed no revision will be made.

**Request:**
`PUT/PATCH http://articleengine.app/article/(article ID)`
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
        "revisionID": (revision ID),
        "articleID": (article ID),
        "articleBody": "The actual body of the article.",
        "articleSection": "Articles may belong to one or more 'sections' in a magazine or newspaper, such as Sports, Lifestyle, etc.",
        "pageEnd": "138",
        "pageStart": "135",
        "pagination": "135-138",
        "wordCount": 23
    }
]
```

## Viewing articles

This call will retrieve an article object matching the specified article ID, HTTP error 404 will be returned if the article ID is not found.

**Request**
`GET http://articleengine.app/article/(article ID)`

**Response**
`HTTP 200`
```
[
    {
        "revisionID": (revision ID),
        "articleID": (article ID),
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
        "revisionID": (revision ID),
        "articleID": (article ID),
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
