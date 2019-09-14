@extends('pipe::app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">PROJECT {{ strtoupper($project->name) }}</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4" style="border-right: 5px solid #1cc88a">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Downloading Source
                        <i class="fas fa-check-circle float-right text-success"></i>
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">Connecting to repo
                            <i class="fa fa-check-circle float-right text-success"></i></li>
                        <li class="list-group-item">Pulling source
                            <i class="fa fa-check-circle float-right text-success"></i></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <i class="p-l-30 fas fa-chevron-down fa-2x"></i>
                    <br><br>
                </div>
            </div>
            <div class="card shadow mb-4" style="border-right: 5px solid #1cc88a">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Building
                        <i class="fas fa-check-circle float-right text-success"></i>
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">Running Composer install
                            <i class="fa fa-check-circle float-right text-success"></i></li>
                        <li class="list-group-item">Running NPM Install
                            <i class="fa fa-check-circle float-right text-success"></i></li>
                        <li class="list-group-item">Running NPM Build
                            <i class="fa fa-check-circle float-right text-success"></i></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <i class="p-l-30 fas fa-chevron-down fa-2x"></i>
                    <br><br>
                </div>
            </div>
            <div class="card shadow mb-4" style="border-right: 5px solid #1cc88a">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Testing
                        <i class="fas fa-check-circle float-right text-success"></i>
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">Running PHPUnit
                            <i class="fa fa-check-circle float-right text-success"></i></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <i class="p-l-30 fas fa-chevron-down fa-2x"></i>
                    <br><br>
                </div>
            </div>
            <div class="card shadow mb-4" style="border-right: 5px solid #1cc88a">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Deploying
                        <i class="fas fa-check-circle float-right text-success"></i>
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">Connecting to instance
                            <i class="fa fa-check-circle float-right text-success"></i></li>
                        <li class="list-group-item">Backing up old release<i class="fa fa-check-circle float-right text-success"></i>
                        </li>
                        <li class="list-group-item">Set up new release<i class="fa fa-check-circle float-right text-success"></i>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
{{--            <div class="card">--}}
{{--                <div class="card-body">--}}
                    <div style="background: #282c34; color: #ffffff; font-size: 0.98em; font-family: Consolas, Menlo, Courier, monospace; padding: 10px">
                        $ composer install
                        <br>
                        <pre style="color: #ffffff; font-size: 0.98em; font-family: Consolas, Menlo, Courier, monospace">
Loading composer repositories with package information
Installing dependencies (including require-dev) from lock file
Package operations: 76 installs, 0 updates, 0 removals
  - Installing doctrine/inflector (v1.3.0): Loading from cache
  - Installing doctrine/lexer (v1.0.1): Loading from cache
  - Installing dragonmantank/cron-expression (v2.3.0): Loading from cache
  - Installing erusev/parsedown (1.7.3): Loading from cache
  - Installing symfony/polyfill-ctype (v1.11.0): Loading from cache
  - Installing phpoption/phpoption (1.5.0): Loading from cache
  - Installing vlucas/phpdotenv (v3.3.3): Loading from cache
  - Installing symfony/css-selector (v4.2.5): Loading from cache
  - Installing tijsverkoyen/css-to-inline-styles (2.2.1): Loading from cache
  - Installing symfony/polyfill-php72 (v1.11.0): Loading from cache
  - Installing symfony/polyfill-mbstring (v1.11.0): Loading from cache
  - Installing symfony/var-dumper (v4.2.5): Loading from cache
  - Installing symfony/routing (v4.2.5): Loading from cache
  - Installing symfony/process (v4.2.5): Loading from cache
  - Installing symfony/http-foundation (v4.2.5): Loading from cache
  - Installing symfony/contracts (v1.0.2): Loading from cache
  - Installing symfony/event-dispatcher (v4.2.5): Loading from cache
  - Installing psr/log (1.1.0): Loading from cache
  - Installing symfony/debug (v4.2.5): Loading from cache
  - Installing symfony/http-kernel (v4.2.5): Loading from cache
  - Installing symfony/finder (v4.2.5): Loading from cache
  - Installing symfony/console (v4.2.5): Loading from cache
  - Installing symfony/polyfill-intl-idn (v1.11.0): Loading from cache
  - Installing symfony/polyfill-iconv (v1.11.0): Loading from cache
  - Installing egulias/email-validator (2.1.7): Loading from cache
  - Installing swiftmailer/swiftmailer (v6.2.0): Loading from cache
  - Installing paragonie/random_compat (v9.99.99): Loading from cache
  - Installing ramsey/uuid (3.8.0): Loading from cache
  - Installing psr/simple-cache (1.0.1): Loading from cache
  - Installing psr/container (1.0.0): Loading from cache
  - Installing opis/closure (3.1.6): Loading from cache
  - Installing symfony/translation (v4.2.5): Loading from cache
  - Installing nesbot/carbon (2.16.3): Loading from cache
  - Installing monolog/monolog (1.24.0): Loading from cache
  - Installing league/flysystem (1.0.51): Loading from cache
  - Installing laravel/framework (v5.8.11): Loading from cache
  - Installing fideloper/proxy (4.1.0): Loading from cache
  - Installing jakub-onderka/php-console-color (v0.2): Loading from cache
  - Installing nikic/php-parser (v4.2.1): Loading from cache
  - Installing jakub-onderka/php-console-highlighter (v0.4): Loading from cache
  - Installing dnoegel/php-xdg-base-dir (0.1): Loading from cache
  - Installing psy/psysh (v0.9.9): Loading from cache
  - Installing laravel/tinker (v1.0.8): Loading from cache
  - Installing beyondcode/laravel-dump-server (1.2.2): Loading from cache
  - Installing fzaninotto/faker (v1.8.0): Loading from cache
  - Installing hamcrest/hamcrest-php (v2.0.0): Loading from cache
  - Installing mockery/mockery (1.2.2): Loading from cache
  - Installing filp/whoops (2.3.1): Loading from cache
  - Installing nunomaduro/collision (v3.0.1): Loading from cache
  - Installing webmozart/assert (1.4.0): Loading from cache
  - Installing phpdocumentor/reflection-common (1.0.1): Loading from cache
  - Installing phpdocumentor/type-resolver (0.4.0): Loading from cache
  - Installing phpdocumentor/reflection-docblock (4.3.0): Loading from cache
  - Installing phpunit/php-token-stream (3.0.1): Loading from cache
  - Installing sebastian/version (2.0.1): Loading from cache
  - Installing sebastian/resource-operations (2.0.1): Loading from cache
  - Installing sebastian/recursion-context (3.0.0): Loading from cache
  - Installing sebastian/object-reflector (1.1.1): Loading from cache
  - Installing sebastian/object-enumerator (3.0.3): Loading from cache
  - Installing sebastian/global-state (2.0.0): Loading from cache
  - Installing sebastian/exporter (3.1.0): Loading from cache
  - Installing sebastian/environment (4.1.0): Loading from cache
  - Installing sebastian/diff (3.0.2): Loading from cache
  - Installing sebastian/comparator (3.0.2): Loading from cache
  - Installing phpunit/php-timer (2.1.1): Loading from cache
  - Installing phpunit/php-text-template (1.2.1): Loading from cache
  - Installing phpunit/php-file-iterator (2.0.2): Loading from cache
  - Installing theseer/tokenizer (1.1.2): Loading from cache
  - Installing sebastian/code-unit-reverse-lookup (1.0.1): Loading from cache
  - Installing phpunit/php-code-coverage (6.1.4): Loading from cache
  - Installing doctrine/instantiator (1.2.0): Loading from cache
  - Installing phpspec/prophecy (1.8.0): Loading from cache
  - Installing phar-io/version (2.0.1): Loading from cache
  - Installing phar-io/manifest (1.0.3): Loading from cache
  - Installing myclabs/deep-copy (1.9.1): Loading from cache
  - Installing phpunit/phpunit (7.5.8): Loading from cache
symfony/routing suggests installing doctrine/annotations (For using the annotation loader)
symfony/routing suggests installing symfony/config (For using the all-in-one router or any loader)
symfony/routing suggests installing symfony/expression-language (For using expression matching)
symfony/routing suggests installing symfony/yaml (For using the YAML loader)
symfony/contracts suggests installing psr/cache (When using the Cache contracts)
symfony/contracts suggests installing symfony/cache-contracts-implementation
symfony/contracts suggests installing symfony/service-contracts-implementation
symfony/event-dispatcher suggests installing symfony/dependency-injection
symfony/http-kernel suggests installing symfony/browser-kit
symfony/http-kernel suggests installing symfony/config
symfony/http-kernel suggests installing symfony/dependency-injection
symfony/console suggests installing symfony/lock
swiftmailer/swiftmailer suggests installing true/punycode (Needed to support internationalized email addresses, if ext-intl is not installed)
paragonie/random_compat suggests installing ext-libsodium (Provides a modern crypto API that can be used to generate random bytes.)
ramsey/uuid suggests installing ext-libsodium (Provides the PECL libsodium extension for use with the SodiumRandomGenerator)
ramsey/uuid suggests installing ext-uuid (Provides the PECL UUID extension for use with the PeclUuidTimeGenerator and PeclUuidRandomGenerator)
ramsey/uuid suggests installing ircmaxell/random-lib (Provides RandomLib for use with the RandomLibAdapter)
ramsey/uuid suggests installing moontoast/math (Provides support for converting UUID to 128-bit integer (in string form).)
ramsey/uuid suggests installing ramsey/uuid-console (A console application for generating UUIDs with ramsey/uuid)
ramsey/uuid suggests installing ramsey/uuid-doctrine (Allows the use of Ramsey\Uuid\Uuid as Doctrine field type.)
symfony/translation suggests installing symfony/config
symfony/translation suggests installing symfony/yaml
monolog/monolog suggests installing aws/aws-sdk-php (Allow sending log messages to AWS services like DynamoDB)
monolog/monolog suggests installing doctrine/couchdb (Allow sending log messages to a CouchDB server)
monolog/monolog suggests installing ext-amqp (Allow sending log messages to an AMQP server (1.0+ required))
monolog/monolog suggests installing ext-mongo (Allow sending log messages to a MongoDB server)
monolog/monolog suggests installing graylog2/gelf-php (Allow sending log messages to a GrayLog2 server)
monolog/monolog suggests installing mongodb/mongodb (Allow sending log messages to a MongoDB server via PHP Driver)
monolog/monolog suggests installing php-amqplib/php-amqplib (Allow sending log messages to an AMQP server using php-amqplib)
monolog/monolog suggests installing php-console/php-console (Allow sending log messages to Google Chrome)
monolog/monolog suggests installing rollbar/rollbar (Allow sending log messages to Rollbar)
monolog/monolog suggests installing ruflin/elastica (Allow sending log messages to an Elastic Search server)
monolog/monolog suggests installing sentry/sentry (Allow sending log messages to a Sentry server)
league/flysystem suggests installing ext-ftp (Allows you to use FTP server storage)
league/flysystem suggests installing league/flysystem-aws-s3-v2 (Allows you to use S3 storage with AWS SDK v2)
league/flysystem suggests installing league/flysystem-aws-s3-v3 (Allows you to use S3 storage with AWS SDK v3)
league/flysystem suggests installing league/flysystem-azure (Allows you to use Windows Azure Blob storage)
league/flysystem suggests installing league/flysystem-cached-adapter (Flysystem adapter decorator for metadata caching)
league/flysystem suggests installing league/flysystem-eventable-filesystem (Allows you to use EventableFilesystem)
league/flysystem suggests installing league/flysystem-rackspace (Allows you to use Rackspace Cloud Files)
league/flysystem suggests installing league/flysystem-sftp (Allows you to use SFTP server storage via phpseclib)
league/flysystem suggests installing league/flysystem-webdav (Allows you to use WebDAV storage)
league/flysystem suggests installing league/flysystem-ziparchive (Allows you to use ZipArchive adapter)
league/flysystem suggests installing spatie/flysystem-dropbox (Allows you to use Dropbox storage)
league/flysystem suggests installing srmklive/flysystem-dropbox-v2 (Allows you to use Dropbox storage for PHP 5 applications)
laravel/framework suggests installing aws/aws-sdk-php (Required to use the SQS queue driver and SES mail driver (^3.0).)
laravel/framework suggests installing doctrine/dbal (Required to rename columns and drop SQLite columns (^2.6).)
laravel/framework suggests installing guzzlehttp/guzzle (Required to use the Mailgun and Mandrill mail drivers and the ping methods on schedules (^6.0).)
laravel/framework suggests installing league/flysystem-aws-s3-v3 (Required to use the Flysystem S3 driver (^1.0).)
laravel/framework suggests installing league/flysystem-cached-adapter (Required to use the Flysystem cache (^1.0).)
laravel/framework suggests installing league/flysystem-rackspace (Required to use the Flysystem Rackspace driver (^1.0).)
laravel/framework suggests installing league/flysystem-sftp (Required to use the Flysystem SFTP driver (^1.0).)
laravel/framework suggests installing moontoast/math (Required to use ordered UUIDs (^1.1).)
laravel/framework suggests installing nexmo/client (Required to use the Nexmo transport (^1.0).)
laravel/framework suggests installing pda/pheanstalk (Required to use the beanstalk queue driver (^4.0).)
laravel/framework suggests installing predis/predis (Required to use the redis cache and queue drivers (^1.0).)
laravel/framework suggests installing pusher/pusher-php-server (Required to use the Pusher broadcast driver (^3.0).)
laravel/framework suggests installing symfony/dom-crawler (Required to use most of the crawler integration testing tools (^4.2).)
laravel/framework suggests installing symfony/psr-http-message-bridge (Required to use PSR-7 bridging features (^1.1).)
laravel/framework suggests installing wildbit/swiftmailer-postmark (Required to use Postmark mail driver (^3.0).)
psy/psysh suggests installing ext-pdo-sqlite (The doc command requires SQLite to work.)
psy/psysh suggests installing hoa/console (A pure PHP readline implementation. You'll want this if your PHP install doesn't already support readline or libedit.)
filp/whoops suggests installing whoops/soap (Formats errors as SOAP responses)
sebastian/global-state suggests installing ext-uopz (*)
phpunit/php-code-coverage suggests installing ext-xdebug (^2.6.0)
phpunit/phpunit suggests installing ext-soap (*)
phpunit/phpunit suggests installing ext-xdebug (*)
phpunit/phpunit suggests installing phpunit/php-invoker (^2.0)
Generating optimized autoload files
> Illuminate\Foundation\ComposerScripts::postAutoloadDump
> php artisan package:discover --ansi
Discovered Package: [32mbeyondcode/laravel-dump-server[39m
Discovered Package: [32mfideloper/proxy[39m
Discovered Package: [32mlaravel/tinker[39m
Discovered Package: [32mnesbot/carbon[39m
Discovered Package: [32mnunomaduro/collision[39m
[32mPackage manifest generated successfully.[39m
                </pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
