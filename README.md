# coffee-web

# Quick Start

## To run this project locally:
1. Prerequisites: Make sure you've installed [Node.js] ≥ 12 and [php] ≥ 7
2. From folder src, install dependencies: `yarn install`
3. From folder backend, install dependencies: `composer install`
4. 
## Copy .env of app
```

$cp .env.example .env

```

## Migrate and seed data
```

$php artisan storage:link

$php artisan key:generate

$php artisan migrate

$php artisan db:seed

```
5. Run the local development server: `yarn dev` in folder src and `php artisan serve` in folder backend (see `package.json` for a
   full list of `scripts` you can run with `yarn`)

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
