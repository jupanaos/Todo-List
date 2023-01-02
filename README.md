# 🟣 ToDo & Co
## Upgrading and enhancing an existing todo list app
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/df503781387f4b43b870007f058c6807)](https://www.codacy.com/gh/jupanaos/Todo-List/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=jupanaos/Todo-List&amp;utm_campaign=Badge_Grade)

## 🧰 Getting started
### Prerequisites
You should use at least Symfony 5.4.
- Composer
- Node.js
- Download the [Symfony CLI](https://symfony.com/download)
- PHP 8.1
- MySQL 8.0
- Apache 2.4
> **NOTE** : I used Laragon on local.

### Installation
1. Clone the repo
   ```sh
   git clone https://github.com/jupanaos/Todo-List.git
   ```
2. Install required packages with composer
   ```sh
   composer install
   ```
3. Install required packages with node
   ```sh
   npm install
   ```

## 🔧 Configuration
Create your own `.env.local` in the main folder `/` and enter your datas. You can see an example in `.env`.

## ⚙️ Database
Create your database. This will use the DATABASE_URL from your `.env.local`.<br>
```bash
php bin/console doctrine:database:create
```
Generate the database schema.<br>
```bash
php bin/console doctrine:schema:update --force
```
Load fixtures.<br>
```bash
php bin/console doctrine:fixtures:load --append
```

#### Build web assets
```bash
npm run watch
```

You can now run your web server with
```bash
symfony server:start
```

## ✍ Contributing
If you would like to contribute to this project, please read `CONTRIBUTING.md`

## ✉️ Contact & links
Julie Pariona - https://github.com/jupanaos/
