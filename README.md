### About project
The project is a sample implementation of email notification through an API

### Technology stack
- PHP
- Doctrine
- sqlite3

### Installation

composer init-project

### Run application

symfony server:start

### Code quality check

composer check-code-quality (rector, php-cs-fixer, phpstan, phpunit)

### Architecture

The project uses a modular monolith architecture with a separation between the framework (Symfony) itself and the business logic.
Of course, this is not a full separation, but it is a direction that can be taken depending on business requirements.
This kind of separation at the initial stage of the project is time-consuming, but the larger the project grows, the less time needs to be spent on it.
Naturally, this type of separation depends on business needs; the longer the project lifetime, the more worthwhile it is to consider such a solution.

In the project, the package dave-liddament/phpstan-php-language-extensions is also used to preserve encapsulation (among other things).

A more purist approach would probably be to start with a minimalist version of Symfony and then install the additional required packages, but in the case of this project, it doesn't really matter.

### Directory structure

The directory structure looks as follows:

- modules, e.g., EmailNotification
- Infrastructure, which contains all adapters connecting the application with external libraries or service providers
- Shared, common things used by all modules, most often interfaces, generic classes, but also Events, which can be handled in other modules

Each module has the following structure:
- Features – clearly stores the behaviors and functionalities of the given module (e.g., Create, Send, Delete, etc.). Each Feature also contains an Entrypoint directory, which is the access point to the module, e.g., through Http controllers, CLI, or cron jobs (scheduler)
- Infrastructure – infrastructure specific to the given module (e.g., configuration of external libraries such as Doctrine, repository implementation, etc.)
- Shared – shared things for the entire module, e.g., Model, Exceptions, etc., which occur in the given module

Class names could be shorter in many places (e.g., CreateEmailNotificationDto -> CreateDto if it is within the EmailNotification namespace), but such names provide easier and more pleasant work with the IDE.

### Commands / Events

The application is built around commands and handlers, where commands represent specific actions or intentions,
and handlers are responsible for executing the corresponding business logic.
This clear separation ensures that each command encapsulates a distinct piece of functionality, making the codebase organized and maintainable.
Additionally, the application uses events to handle asynchronous communication and side effects.
Events allow different parts of the system to react to significant occurrences in a decoupled manner,
promoting scalability and flexibility in handling complex workflows.

By default, all commands are synchronous, but there is nothing preventing changing them to asynchronous - it all depends on business requirements.
All events are asynchronous due to the implementation of EventInterface.

By default, all asynchronous messages are handled by Doctrine (DSN), which unfortunately forced overriding the DoctrineTransactionMiddleware.
Otherwise, the message would not be saved to the database due to rollback.
This class is unnecessary if using another transport (e.g., Redis or RabbitMQ).

### Next iteration @todo

- unifying error handling (a different structure is used for validation, another for business logic errors)
- using dama/doctrine-test-bundle to speed up testing
- creating custom attributes (e.g., for controllers) and automatic registration of such classes with appropriate tags, to further separate from the framework
- using deptrac/deptrac to limit and #[NamespaceVisibility] to a more organic and automated form
