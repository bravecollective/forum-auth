# Brave Forum Authentication

## Install

This *requires* a phpBB installation.

If you use Vagrant, install phpBB at `http://localhost:8080/phpbb/install/index.php`.
If you skip this step there will be a redirection error when trying to access `http://localhost:8080`

The phpBB installation also needs two `Custom profile fields` (Single text fields):
- `core_corp_name`
- `core_alli_name`

- `logs` must be writable by the web server.
- copy `config/config.dist.php` to `config/config.php` and adjust values.
- execute `composer install`, this will also create/update the database schema.
