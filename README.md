#### Laravel search processor

Component for processing list requests in Laravel.

Service `Poruchik85\LaravelSearchProcessor\Services\SearchProcessorService`represents a single method `search(FormRequest $request): ListModel`. The method accepts an instance of a GET request and returns an object of `ListModel` class, which contains fields:
- `data` - result set sliced by pagination window;
- `count` - number of records selected by filters;
- `perPage` - number of records on one page;
- `currentPage` - current page number, starts with 0;

###### Installation:
since the package is not registered in packagist, you need to explicitly specify its url in composer.json:

```
"repositories": [
   {
      "type": "vcs",
      "url": "git@github.com:poruchik85/laravel-search-processor.git"
   }
]
```

and then:
```
composer require poruchik85/laravel-search-processor
```

and finally:
```
php artisan vendor:publish --provider="Poruchik85\LaravelSearchProcessor\Providers\ServiceProvider"
```


###### Creating a new search class
1. create a new class, which must extends `Poruchik85\LaravelSearchProcessor\Services\SearchProcessor`;  
2. in this class:  
2.1. implement the `mainQuery` method - the main method of the service. It should return the main request of the class. It is advisable to extract all the data that will be needed for display in the future, since extracting data from models, for example, in resource classes, creates many unnecessary queries to the database;  
2.2. implement the `mainTable` method - it should return the name of the table of the desired model. This table is needed for regular filters on model fields to work. 
3. you can also override the following settings in the class:  
3.1. `const DEFAULT_PAGE_SIZE` - integer, default page size. If there is no paginator information in the search query, then the page number will always be the first (0), and the size is determined by this parameter. Request fields responsible for sorting - `page` and `page_size`;  
3.2. `const DEFAULT_SORT` - array of the form
    ```php
    [
        [
            'by' => $sortField_1,
            'direction' => $sortDirection_1,
        ],
        [
            'by' => $sortField_2,
            'direction' => $sortDirection_2,
        ],
        ...
        [
            'by' => $sortField_N,
            'direction' => $sortDirection_N,
        ],
    ]
    ```
    , where `$sortField_` - the name of the field by which we are sorting, `$sortDirection_` - the sorting direction (global constants `SORT_ASC` and `SORT_DESC`). When specifying the name of a field of a non-main model through a dot, you need to be careful - the corresponding table must be joined in the `mainQuery`[2.1.] method. It is advisable to always set this constant, because in general databases do not guarantee any sorting at all without an explicit indication. Request field responsible for sorting - `sort`. It should contain a list of fields to sort by. The `-` sign at the beginning of the field name means reverse sorting. See also [5];  
3.3. `protected function filters()` - the method must return filters processed by the class. Filters not specified in this method will not be processed. The return value is an associative array of the form:
    ```php
    [
        'filter_1' => [
            'handler' => $handler_1,
            'field' => $field_1
            'interval' => true|false
        ],
        'filter_2' => [
            'handler' => $handler_2,
            'field' => $field_2,
            'interval' => true|false
        ],
        ...
        'filter_N' => [
            'handler' => $handler_N,
            'field' => $field_N
            'interval' => true|false
        ],
    ]
    ```
    , where `$field_` - optional value, needed to indicate a field not related to the main model; `$handler_` - required value, filter handler. Must be either a `callable`, which takes a request instance and a filter value and attaches conditions to the request, or one of the following string values:   
3.3.1. `text` - simple text filter by occurrence of a string;  
3.3.2. `number` - numeric filter. If this type is specified, the filter may also have the `interval` modifier. If it is present, the filter expects an array of two elements - for each it checks for presence and sets boundary conditions (the interval can be half-open on either side). If this modifier is not set, the filter waits for a single value and filters by exact match;  
3.3.3. `bool` - boolean filter. Takes a single value. If it is equal to `"1"` or `1`, the filtered field must be `true`, and if it is equal to `"0"` or `0` - `false`;  
3.3.4. `date` - filter by date. Accepts an array. If there is one element in the array, it filters by exact match. If two - filters by range similar to the `number` filter (the range can be half-open);  
3.3.5. `list` - list filter. Accepts a single value or an array. In the case of a single value, filters by exact match, in the case of an array, by a match with at least one element;

4. `protected function credentialsFilters($builder)` - in this method you can add specific conditions related to the calling user (restrictions on role, country, etc)
5. `protected function sortMapping()` - In this method you can map fields for sorting. If a request arrives to sort by some non-trivial field, here you can describe how you actually need to sort; 
6. match the class of the search query and the created processor class in a config file `config/search_processor.php`.
