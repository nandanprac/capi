from fabric.api import lcd, local, settings, task, puts, hide
from fabric.colors import green
import os
import sys

ROOT_DIR = os.path.abspath(os.path.dirname(__file__))


def info(text):
    puts(green(text))


@task
def polish():
    found_errors = False
    with lcd(ROOT_DIR):
        with settings(hide('running')):
            # Get sudo password
            info('Checking sudo access...')
            local('sudo -v')
        with settings(hide('running'), warn_only=True):
            info('Fixing directory permissions...')
            result = local(
                '! find . '
                '-path ./.git -prune -o '  # Exclude .git directory
                '-path ./vendor -prune -o '  # Exclude vendor directory
                '-path ./app/cache -prune -o '  # Exclude app/cache directory
                '-path ./app/logs -prune -o '  # Exclude app/logs directory
                '-path ./bin -prune -o '  # Exclude bin directory
                '-type d -not -perm 0775 -print0 | '
                'tee /dev/stderr | '
                'xargs -0 chmod 0775 >/dev/null 2>&1'
            )
            found_errors = found_errors or result.return_code != 0
            info('Fixing file permissions...')
            result = local(
                '! find . '
                '-path ./.git -prune -o '  # Exclude .git directory
                '-path ./vendor -prune -o '  # Exclude vendor directory
                '-path ./app/cache -prune -o '  # Exclude app/cache directory
                '-path ./app/logs -prune -o '  # Exclude app/logs directory
                '-path ./bin -prune -o '  # Exclude bin directory
                '-wholename ./app/console -o '  # Exclude app/console
                '-name *.sh -o '  # Exclude shell scripts
                '-type f -not -perm 0664 -print0 | '
                'tee /dev/stderr | '
                'xargs -0 chmod 0664 >/dev/null 2>&1'
            )
            found_errors = found_errors or result.return_code != 0
            info('Fixing script permissions...')
            result = local(
                '! find . '
                '-path ./.git -prune -o '  # Exclude .git directory
                '-path ./vendor -prune -o '  # Exclude vendor directory
                '-path ./app/cache -prune -o '  # Exclude app/cache directory
                '-path ./app/logs -prune -o '  # Exclude app/logs directory
                '-path ./bin -prune -o '  # Exclude bin directory
                '-type f -name *.sh -perm 0775 -print0 | '
                'tee /dev/stderr | '
                'xargs -0 chmod 0775 >/dev/null 2>&1'
            )
            found_errors = found_errors or result.return_code != 0

            info('Fixing whitespace errors...')
            result = local(
                "! find . "
                "-path ./.git -prune -o "  # Exclude .git directory
                "-path ./vendor -prune -o "  # Exclude vendor directory
                "-path ./app/cache -prune -o "  # Exclude app/cache directory
                "-path ./app/logs -prune -o "  # Exclude app/logs directory
                '-wholename ./app/bootstrap.php.cache -o '
                "-iregex '.*\\.\\(ico\\|jpg\\|png\\|gif\\|eot\\|ttf\\|"
                "woff\\|pyc\\|pyo\\|wav\\)$' -o "
                "-type f -print0 | "
                "xargs -0 grep -PlZn '(\\s+$)|(\\t)' | "
                "tee /dev/stderr | "
                "xargs -0 sed -i -e 's/\\s\\+$//' -e 's/\\t/    /g' "
                ">/dev/null 2>&1"
            )
            found_errors = found_errors or result.return_code != 0

            info('Finding merge conflict leftovers...')
            result = local(
                '! find . '
                '-path ./.git -prune -o '
                '-path ./vendor -prune -o '
                '-path ./app/cache -prune -o '
                '-path ./app/logs -prune -o '
                '-type d -o '
                '-print0 | '
                'xargs -0 grep -Pn "^(<|=|>){7}(?![<=>])"'
            )
            found_errors = found_errors or result.return_code != 0

            info('Running coding standards check...')
            result = local('./bin/phpcs --standard=Symfony2 '
                           './src/')
            found_errors = found_errors or result.return_code != 0

            info('Checking for var_dump, echo or die statements...')
            result = local(
                '! find ./src -name "*.php" -print0 | '
                'xargs -0 egrep -n "var_dump|echo|die" | grep -v "NOCHECK"'
            )
            found_errors = found_errors or result.return_code != 0

            info('Validating doctrine orm schema...')
            result = local(
                './app/console doctrine:schema:validate | '
                'grep \'\\[Mapping\\]\\s*OK\'')
            found_errors = found_errors or result.return_code != 0

            info('Checking use of symfony 2.0 validation...')
            result = local(
                '! find ./src '
                '-name "*.php" -print0 | '
                'xargs -0 grep -Pn "Assert\\\\\(Min|Max|MinLength|MaxLength)|'
                '(?<!context->)addViolation\(|setPropertyPath" | '
                'grep -v "NOCHECK"'
            )
            found_errors = found_errors or result.return_code != 0

            info('Checking outdated imports...')
            result = local(
                '! find . '
                '-path ./.git -prune -o '
                '-path ./vendor -prune -o '
                '-path ./app/cache -prune -o '
                '-path ./app/logs -prune -o '
                '-name "*.php" -print0 | '
                'xargs -0 grep -Pn \'Symfony\\\\Bundle\\\\DoctrineBundle|'
                'AmazonS3|FOS\\\\RestBundle\\\\Response\\\\Codes\''
            )
            found_errors = found_errors or result.return_code != 0

            info('Checking use of outdated doctrine...')
            result = local(
                '! find src '
                '-name "*.php" -print0 | '
                'xargs -0 grep -Pn "(?<!this->)getEntityManager|'
                '\\\\\(prePersist|preUpdate|postLoad)"'
            )
            found_errors = found_errors or result.return_code != 0

            info('Checking use of queue name parameters...')
            result = local(
                '! find . '
                '-path ./.git -prune -o '
                '-path ./vendor -prune -o '
                '-path ./app/cache -prune -o '
                '-path ./app/logs -prune -o '
                '-name "*.php" -print0 | '
                'xargs -0 grep -n "_queue_name"'
            )
            found_errors = found_errors or result.return_code != 0

            info('Checking no-servername compatibility...')
            result = local(
                '! find . '
                '-path ./.git -prune -o '
                '-path ./vendor -prune -o '
                '-path ./app/cache -prune -o '
                '-path ./app/logs -prune -o '
                '-name "*.php" -print0 | '
                'xargs -0 grep -Pn "accounts_host"'
            )
            found_errors = found_errors or result.return_code != 0

            info('Checking direct use of pheanstalk...')
            result = local(
                '! find src -name "BeanstalkQueue.php" -o '
                '-name "QueueFactory.php" -o '
                '-name "*.php" -print0 | '
                'xargs -0 grep -Pin "pheanstalk|beanstalk"'
            )
            found_errors = found_errors or result.return_code != 0

       #     info('Checking accounts dependency in tests...')
       #     result = local(
       #         '! find src/Practo/ApiBundle/Tests/Controller '
       #         '-name "SessionControllerTest.php" -o '
       #         '-name "*Test.php" -print0 | '
       #         'xargs -0 grep -n "sessions"'
       #     )
       #     found_errors = found_errors or result.return_code != 0

            info('Checking use of preg_replace...')
            result = local(
                '! find src -name "*.php" -print0 | '
                'xargs -0 grep -n "preg_replace("'
            )
            found_errors = found_errors or result.return_code != 0

            info('Finding use of old deprecated records functions...')
            result = local(
                '! git grep -P "sub_nav_option|[Ss]ubNavOption" |'
                'grep -v "fabfile\.py" |'
                'grep -v "DoctrineMigrations\/*"'
            )
            found_errors = found_errors or result.return_code != 0

        with settings(hide('running')):
            info('Running tests...')
       #     result = local('./bin/test.sh')
            found_errors = found_errors or result.return_code != 0
    if found_errors:
        sys.exit(1)
