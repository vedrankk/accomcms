# README #

This README would normally document whatever steps are necessary to get your application up and running.

### What is this repository for? ###

* Accommodation CMS
* 1.0
* [Learn Markdown](https://bitbucket.org/tutorials/markdowndemo)

### How do I get set up? ###

* Install advanced [Yii 2](http://www.yiiframework.com/) (you will need github access token)
     - composer global require "fxp/composer-asset-plugin:^1.2.0"
     - composer create-project --prefer-dist yiisoft/yii2-app-advanced accomcms
* Run 'php init' in project folder
* Add [Bitbucket](http://bitbucket.org) repo  with: git remote add origin https://{username}@bitbucket.org/accomcmsteam/accomcms.git
* Run 'git init', 'git fetch --all', 'git reset --hard origin/master', 'git branch --set-upstream-to=origin/master' in project folder
* Set dev hosts: admin.accomcms.dev, accomcms.dev (alias www.accomcms.dev)
* Set virtual hosts, see resource/accomcms.conf
* Dependencies
* Database configuration
* How to run tests
* Deployment instructions

### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

### Who do I talk to? ###

* CronosDev vlado@cronosdev.com and norbert@cronosdev.com
* --Other community or team contact--




DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application    
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```