# OctoberCMS Multi Databases

[OctoberCMS](http://octobercms.com/) Plugin to create SaaS applications in different databases, making it easier to manage multiple clients.
To develop this plugin I reused several classes that OctoberCMS itself has, trying to create the simplest code possible.

**Important**
This plugin is still in the testing and development stages, use at your own risk.

## REQUERIMENTS
[Multisite](https://octobercms.com/plugin/voipdeploy-multisite)


## Usage
### Installation
You can install this plugin either via composer.

#### With Composer
Execute below at the root of your project.
```
composer require techmobi/multidb
```

#### Configuring file database.php
You will need to make a small change to the file in `config/database.php`
```php
...
'original' => env('DB_CONNECTION', 'mysql'),

'connections' => [
	...

	'multidb' => [
        'driver' => 'mysql',
        'engine' => 'InnoDB',
        'host' => env('MULTIDB_HOST', 'localhost'),
        'port' => env('MULTIDB_PORT', 3306),
        'database' => env('MULTIDB_DATABASE', 'database'),
        'username' => env('MULTIDB_USERNAME', ''),
        'password' => env('MULTIDB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'varcharmax' => 191,
    ],

	...
]
...
```


### Settings MultiDB - Backend
To use MultiDB you will need to follow the steps below.

**Important**
It is necessary to have a user in the database who can Read, Edit and Update data, as well as create new databases.

#### MultiDB Plugin
First Navigate to `Settings -> MultiDB -> Settings Plugin`, in this place you can configure the prefixes that the databases may have, configure what will be the names of each databases, and also, you can select which plugins will be replicated for the new created instances.

**Save changes to update data**

#### Added new Hosts MultiDB
Navigate to `Settings -> MultiDB -> Hosts`, in this place you can add new databases and list which domains will be used. At this point comes the multisite relationship.
With this configuration, it will be possible to identify the databases according to each domain accessed.

### Using in PHP Code
After all configurations, it will be necessary to add the following trait to all models that have the replicated database, example:

```php
class Product extends Model
{
	use \Techmobi\Multidb\Traits\UsesMultiConnection;

	...
}
```

If in case you want to save the files in the new database, you will need to use the model `Techmobi\Multidb\Models\File`, example use:

```php
class Product extends Model
{
	use \Techmobi\Multidb\Traits\UsesMultiConnection;

	...

	public $attachMany = [
        'images' => [
            'Techmobi\Multidb\Models\File',
            'softDelete' => true,
        ],
    ];

    ...
}
```