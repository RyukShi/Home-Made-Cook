# Home-Made-Cook

This Web App is a recipe sharing platform. It allows users to share their recipes with other users. Users can also search for recipes by ingredients, categories, and other users. Users can also add recipes to their favorites.

## Install project

### Clone the project and install dependencies

```bash
git clone https://github.com/RyukShi/Home-Made-Cook.git

cd Home-Made-Cook && composer i && npm i
```

## Run project

### Requirements

To run the project, you need to have Node.js installed on your computer. You can download it [here](https://nodejs.org/en/download/).  

You also need to have PostgreSQL installed on your computer. You can download it [here](https://www.postgresql.org/download/).  

And to finish, uncomment options pdo_pgsql and pgsql in your php.ini file to connect to postgresql server.  

### Commands

Start Symfony server
```bash
symfony serve
```  

To run webpack-dev-server use this command (for CSS and JavaScript)
```bash
npm run dev-server
```

To run unit tests use this command
```bash
php .\vendor\bin\phpunit

php .\vendor\bin\phpunit --coverage-html ./public/test-coverage
```
