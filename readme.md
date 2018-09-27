# Permission module for Laradium cms

# Installation

## For local use

- Add this to your project repositories list in `composer.json` file

```
"repositories": [
    {
        "type": "path",
        "url": "../packages/laradium-permission"
    }
  ]
```

Directory structure should look like this

```
-Project
-packages
    --laradium
    --laradium-permission
```

## For global use

```
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/laradium/laradium-permission.git"
    }
]
```

- Run ```composer require laradium/laradium-permission dev-master```
- Run ```php artisan vendor:publish --tag=laradium-permission```
- Add ```UserPermissions``` trait to User model