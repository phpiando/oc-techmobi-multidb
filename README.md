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
After making the configurations mentioned above, it is necessary to edit the models of the plugins.

#### Trait UsesMultiConnection
This trait is responsible for managing the data that will be saved in the databases. It will be necessary to add the following trait to all models that have the replicated database, example below:

```php
class Product extends Model
{
	use \Techmobi\Multidb\Traits\UsesMultiConnection;

	...
}
```
#### Trait UsesMainConnection
This Trait allows you to relate a table from the main database to the child database, for example, you have a Customers plugin, this plugin is using the UsesMultiConnection trait, however ou have a Core plugin that has a Country Model, this plugin you are not replicating, but you need to relate the `country_id` attribute of the customers table to the country table, in this situation there would be an error because MultiDB change connections momentarily, so as not to have a problem, you need to add in your model Country the trait below.

```php
class Product extends Model
{
    use \Techmobi\Multidb\Traits\UsesMainConnection;

    ...
}
```

#### BONUS: MultiFiles with MultiDB
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

##### MediaLibrary
For now I was unable to make a class to manage MediaLibrary, however you can change the file `System\Classes\MediaLibrary`, adding the following codes:

```php
class MediaLibrary
{
    use \October\Rain\Support\Traits\Singleton;
    use \Techmobi\Multidb\Traits\UsesMultiConnection;

    ...

    protected function init()
    {
        $this->getDomainData();

        //$this->storageFolder = self::validatePath(Config::get('cms.storage.media.folder', 'media'), true);
        //$this->storagePath = rtrim(Config::get('cms.storage.media.path', '/storage/app/media'), '/');

        $this->storageFolder = "/{$this->getDatabaseName()}/media";
        $this->storagePath = "/storage/app/{$this->getDatabaseName()}/media";

        $this->ignoreNames = Config::get('cms.storage.media.ignore', FileDefinitions::get('ignoreFiles'));

        $this->ignorePatterns = Config::get('cms.storage.media.ignorePatterns', ['^\..*']);

        $this->storageFolderNameLength = strlen($this->storageFolder);
    }
    ...
}
```