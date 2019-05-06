# Brave Forum Authentication

## Install phpBB

This *requires* a phpBB installation.

If you use the Vagrant installation you must first install phpBB at `http://[IP]/phpbb/install/app.php`.
If you skip this step there will be a redirection error when trying to access the app.

Don't forget to delete the install directory after this, or the forum will not be accessible.

The phpBB installation also needs two Custom profile fields `(Admin -> Users and Groups -> Custom profile fields)`, type: Single text fields:
- `core_corp_name`
- `core_alli_name`

I you use a fresh phpBB installation you must create groups if you want to test anything.
Go to `Admin -> General -> Manage groups` and add the first group with the name `brave`, it
should get the ID 8. See `config/config.php` `cfg_bb_groups` array for all groups.

## Install auth app

 ==============================
 
When updating and if there is still a table named `groups` you need to rename that manually first
(groups is a reserved keyword in MySQL 8):
```
RENAME TABLE `groups` TO `character_groups`;
```

 ==============================
 
Install
- `logs` must be writable by the web server.
- copy `config/config.dist.php` to `config/config.php` and adjust values.
- execute `composer install`
- execute `bin/console.php db:schema-update`

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

