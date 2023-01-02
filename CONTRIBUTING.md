# Contributing to this project
You can contribute to this project whether it's:

- Reporting a bug
- Discussing the current state of the code
- Submitting a fix
- Proposing new features

## Develop with Github
We use Github to host code and feature requests, as well as accept pull requests.

## Use [Github Flow](https://docs.github.com/get-started/quickstart/github-flow), So All Code Changes Happen Through Pull Requests
If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement". Any contributions you make are greatly appreciated.

1. Fork the Project
2. Create your Feature Branch `git checkout -b feature/YourFeature`
3. Commit your Changes `git commit -m 'Add some YourFeature'` (see [Commit message format](#commit-message-format))
4. Push to the Branch `git push origin feature/YourFeature`
5. Ensure the test passes (see [Test your code](#test-your-code))
6. Make sure your code lints (see [Lint your code](#lint-your-code))
7. Open a Pull Request

### Create issues using Github's [issues](https://github.com/jupanaos/Todo-List/issues)
Report an issue by [opening a new issue]().

#### ⚠️ Before creating an issue
- Make sure you fulfill all the **requirements and packages versions** stated in `README.md`
- **Investigate the issue** - Search for exising issues (open or closed) that address the issue, and might have even resolved it already

**Good issue reports** tend to have:

- A quick summary and/or background (e.g. OS, environment, packages, etc.)
- Steps to reproduce
  - Be specific!
  - Give sample code if you can
- What you expected would happen
- What actually happens
- Notes (possibly including why you think this might be happening, or stuff you tried that didn't work)

### Test your code
Before pushing your contribution, please make sure you run the following tests in the `tests` branch.

First create a `.env.test.local.` file for your test environment datas and create your test database:
```bash
php bin/console --env=test doctrine:database:create
```
```bash
php bin/console --env=test doctrine:fixtures:load --append
```

Then run the tests:
```bash
 ./vendor/bin/phpunit tests
 ```

You can add this flag to generate a HTML render of your tests.
```bash
--coverage-html public/test-coverage
```

### Commit message format
You can refer to the [Angular commit message guidelines](https://github.com/angular/angular/blob/22b96b9/CONTRIBUTING.md#type) to make your commits more readable.

### Lint your code
We strongly recommend using a linter and analysing your code to help you detect bugs and error before pushing your contribution.
We used [PHPCSFixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) and [PHPStan](https://phpstan.org/user-guide/getting-started) (from level 0  to 9) but you can use any tools of your liking.