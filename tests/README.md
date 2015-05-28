# Running tests

The provided environment comes with all the tools you need to run tests.

From your LOCAL (not the VM) shell:

```shell
cd <GIT_REPOSITORY_ROOT>
vagrant ssh         // starts a SSH session on the virtual machine
```

at this point a ssh shell will be started on the VM. From there:

`cd /var/www/` => Now we are on the root of the project, in the VM.

Run the entire test suite:

`phpunit`

Run a specific test suite

`phpunit --testsuite TEST_SUITE_NAME`

Run only one specific test case:

`phpunit --filter "DartsGame_Some_Class"`

Run only one method in a given case:

`phpunit --filter "DartsGame_Some_Class::methodName"`

Run only one method in a given case, only for a specific data set:

`phpunit --filter "/DartsGame_Some_Class::methodName with data set #0/"`

Run tests and perform code coverage analysis:

`phpunit --coverage-html "/home/vagrant/coverage"`

and then browse to url http://localhost:8080/tests/coverage

## Running functional tests
Download and run the Selenium Server jar file from here: http://www.seleniumhq.org/download/

// TODO: Finish description
