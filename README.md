# Project brief

This demo project attempts to solve the Divido engineering code challenge found here: https://github.com/dividohq/config-chg in PHP.

# Test suite

In order to run the test suite I've instrumented the project with a PHPUnit copy via composer. I'm adding to version control only the composer.json / lock files to allow for fresh installation at image building.

The tests are separated in two test files/suites - one testing the `FileLoaderInterface` and related classes and one for the `Config` class that uses it.

Test fixture json files are placed in the `tests/fixtures`. Should you choose to feed the tests different fixtures be sure to uncomment the `volumes` section in the `docker-compose.yaml` file.

# Proposed solution overview

As outlined in the test suite the intended solution revolves around the class `Config` which in turn uses the `JsonFileLoader` class to delegate the task of actually loading the data from a target file into array in memory. The `Config` class is responsible for managing the already parsed data and merging it with any already exising state. It also provides the `getByPath` method to allow access to the already parsed and merged configuration data as follows:

```php
$fileLoader = FileLoaderFactory::create('json');
$conf = new Config($fileLoader);

$conf->loadFromFiles(['conf.json', 'conf.local.json']); // <-- files order matters, as they are evaluated from left to right
$conf->getByPath('very.important.conf');
```

As hinted in the code challenge the solution is open to extension in the form of the ability to process configuration files in different formats. 

The way I decided to achieve it is by extracting the logic of reading and parsing files to an external class (also adhering to the single responsibility principle). This in turn gives the opportunity to define a generic interface to which all possible file loader implementations must conform. That introduces an abstraction layer between the actual implementation and the client code that uses it, hence the dependency is now upon an interface contract and not upon specific implementation/class. 

Despite not implementing additional file loaders, I've added a helper method to the `FileLoaderFactory` class to facilitate run-time extension of supported file loaders, so for example plug-ins or any other client code can hook into it:

```php
FileLoaderFactory::registerLoader($loaderType, $loaderImplementation)

```

Additional layer of assumption is added to the `FileLoaderFactory` class - it enforces singleton file loader instances returned. Since the file loader class itself is not holding on to any state, it is safe to use a single copy throughout the full application lifecycle. 

With the introduction of additional file loader classes the need to abstract and share certain parts of the concrete implementation might arise. To satisfy them one can easily extract them into an asbtract base class and inherit from it for all concrete sub-classes (NOTE: A good use case of inheritance being favored over composition, despite introducing strongest possible coupling.).

I'm using two custom exception classes to allow for escalating of errors deemed unrecoverable within the bounds of the class/module, so client code can be notified of those and potentially handle them in a meaningful way. This is possible as more abstract code can cope with handling more errors the higher it is in the call stack.

# Docker

The docker image for the project uses **php:7.4** base php image, on top of it is the checked out project copy available in /app inside the container. In addition I've hardcoded fixture files containing the JSON structure from the code challenge repo.

The main service introduced is the test suite execution. Available as follows:

```bash
docker-compose run --rm tests
```

For convenience I've added a second service called "inspect" to the docker-compose.yaml file that will run a container from the same image with `/bin/bash` being the entrypoint to facilitate inspection of the container.

```bash
docker-compose run --rm inspect
```