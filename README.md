# VILT Auth

Auth Module for VILT framework to convert any Model to Auth Model with full API

## Install

```bash
composer require queents/auth-module
```
`
Add Module to `modules_statuses.json` if not exists

```json
{
    "Auth": true
}
```

Make a migration

```bash
php artisan migrate
```

to create a new auth API use this command

```bash
php artisan vilt:auth {moduleName} {tableName}
```

## Support

you can join our discord server to get support [VILT Admin](https://discord.gg/HUNYbgKDdx)

## Docs

look to the new docs of v4.00 on my website [Docs](https://vilt.3x1.io/docs/)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [3x1](https://github.com/3x1io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

