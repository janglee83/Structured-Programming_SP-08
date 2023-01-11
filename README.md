# Structured-Programming_SP-08

# Quick Start

## To run this project locally
1. Prerequisites: Make sure you've installed [docker]
2. You must run commands in Linux or Powershell environment on Windows

## Copy .env of app
```
$ cp .env.example .env
```

## Config DB in .env
```
APP_URL=https://localhost/
...
DB_CONNECTION=mysql
DB_HOST=sp-db
DB_PORT=3306
DB_DATABASE=sp_database
DB_USERNAME=root
DB_PASSWORD=123456
```

## Build docker container
>
> After Docker runs, the process takes about 5p to complete the processes. 
> You can watch your process in server's log terminal by click View details at sp08-server.
>

```
$ docker-compose up -d --build
```

## Migrate and seed data
```
$ docker-compose exec sp08-server ash

[/var/www/html]

$ php artisan storage:link

$ php artisan key:generate

$ php artisan migrate

$ php artisan db:seed

```

Go ahead and play with the app and the code. As you make code changes, the app will automatically reload.

[react]: https://reactjs.org/
[create-near-app]: https://github.com/near/create-near-app
[node.js]: https://nodejs.org/en/download/package-manager/
[jest]: https://jestjs.io/
[near accounts]: https://docs.near.org/docs/concepts/account
[near wallet]: https://wallet.testnet.near.org/
[near-cli]: https://github.com/near/near-cli
[gh-pages]: https://github.com/tschaub/gh-pages
[php]: https://www.php.net/downloads.php
[postgresql lts version]: https://www.postgresql.org/download/
[docker]: https://www.docker.com/
