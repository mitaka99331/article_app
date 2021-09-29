# Article App
**Article App** is my solution for the given task:

Using Symfony 4



Create a small project for maintaining Articles and Article Categories - with just a basic html.

- Category has a name, Article has a name, category, image, summary and description;

- Have two forms - for entering new  Categories and Articles;

- Have a view for articles with a filter - to list articles by category. Articles are sorted by date, with information for name and category and a link to detail page;

- Detail Article page shows all the available article information with a back to list link.



Bonus:
- Create authentication for the system
- set REST API for getting Articles.

## Requirements
- PHP 7.4

## Steps to run the project
1. Run `composer install`
2. Configure DATABASE_URL in the .env file
3. Run `php bin/console doctrine:migrations:migrate`
4. Run `php bin/console doctrine:fixtures:load`
5. Run `symfony serve`

## To sign use credentials:
- username: `admin` 
- password: `pass`

## For the API:
To get all articles make a GET request to `/api/v1/get/articles`. Make sure to add `X-AUTH-TOKEN` to the header.
To obtain a token a POST request needs to be sand `/api/authenticate`containing in the body user credentials in json format.

### Examples
- For the token : `curl -X POST -H "Content-Type: application/json" "https://localhost:8000/api/authenticate" -d '{"username":"admin","password":"pass"}'`
- For the articles : `curl -H "X-AUTH-TOKEN: %token%" "https://localhost:8000/api/v1/get/articles"`
