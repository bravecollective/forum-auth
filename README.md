# Brave Forum Authentication

## Install

This *requires* a phpBB installation.

If you use Vagrant, install phpBB at `http://localhost:8080/phpbb/install/index.php`.
If you skip this step there will be a redirection error when trying to access `http://localhost:8080`

The phpBB installation also needs two `Custom profile fields` (Single text fields):
- `core_corp_name`
- `core_alli_name`

Install
- `logs` must be writable by the web server.
- copy `config/config.dist.php` to `config/config.php` and adjust values.
- execute `composer install`, this will also create/update the database schema.

## Migration

The old forum auth can be found at:
https://github.com/bravecollective/oldcore-forum-auth

If both databases are on the same server, just execute (change database names as needed):

```
INSERT IGNORE INTO forum_auth.characters (id, name, username)
SELECT character_id, character_name, character_name FROM old_forum_auth.auth_core
```

Now run the sync job:

`bin/console.php groups:sync`
