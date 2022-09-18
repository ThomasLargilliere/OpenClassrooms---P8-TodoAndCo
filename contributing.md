# ToDo&Co - Project Contribution Guidelines

If you wish to contribute to the improvement of this project, your contribution is welcome!

Please follow the guidelines and conditions set out below.

## Preconditions

Please install the project locally by following the instructions [README.md](README.md).

## About Symfony

This project is developed with Symfony Framework. Please refer to the [Symfony Documentation](https://symfony.com/doc/current/index.html) to follow development best practices.

## Code quality

- Code quality is monitored by SymfonyInsight, as you can see the app got a score of 100/100 and a platinum medal.

[![SymfonyInsight](https://insight.symfony.com/projects/5d1c1fa2-64f9-4500-8d6d-630df6bfc8ed/big.svg)](https://insight.symfony.com/projects/5f1d119d-0d15-4859-bd06-1fa3f7a3a29c/analyses/75)

- Symfony, will automatically analyze your Pull Request for code quality.

- Please make sure to maintain a level of quality equivalent to the platinum medal.

- Please also run the following command to make sure you meet the minimum PSR requirements ([PSR-1](https://www.php-fig.org/psr/psr-1/), [PSR-12](https://www.php-fig.org/psr/psr-12/) and [PSR-4](https://www.php-fig.org/psr/psr-4/)):

## Test

Unit tests are implemented with PHPunit.

### Continuous integration

- Code coverage is > 70%. Please ensure this high level of test coverage.

#### Local PHPUnit

- To run tests on your local machine, you need a to set up environement and test database.[Symfony Testing Set up](https://symfony.com/doc/current/testing.html#set-up-your-test-environment)

- To run all the tests:

```powershell
symfony php bin/phpunit
```

- To run specific tests:

```powershell
symfony php bin/phpunit --filter `name of the test file without the .php`
```

Example:

```powershell
symfony php bin/phpunit --filter DefaultController
```

- To Get code coverage report:

```powershell
vendor/bin/phpunit --coverage-html public/test-coverage
```

Test-coverage will be accessible from the root of the project in the folder: public\test-coverage\index.html

## Instructions

- First, check the [Project Improvement Plan](https://github.com/ThomasLargilliere/Todo-Co/issues) to see suggestions for improving the app.

- If your contribution is not included in the existing issues, please create a new issue and we can discuss it.

- Project deploy branch is "main", please never work on it. Always start from the "develop" branch to create a new branch.

- After installing the project on your local computer, create a new branch according to the nomenclature:

  - 'bugfix/': for modifications/bugs
  - 'feature/': for a new feature

- Work on your own branch. Don't forget to implement your own tests to test your code.

- Verify that your code passed all tests.

- Commit your changes: `git commit -m "commit description"`

- Push your branch: `git push -u origin feature/my-feature`

- If SymfonyInsight please fix your bugs, commit and push.

## Author

**Thomas Largilliere** - Student School background Openclassrooms PHP / Symfony application developer

## Thank you for your contribution
