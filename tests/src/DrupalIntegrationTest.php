<?php declare(strict_types=1);

namespace PHPStan\Drupal;

final class DrupalIntegrationTest extends AnalyzerTestBase {

    public function testInstallPhp() {
        $errors = $this->runAnalyze(__DIR__ . '/../fixtures/drupal/core/install.php');
        $this->assertCount(0, $errors);
    }

    public function testTestSuiteAutoloading() {
        $paths = [
            __DIR__ . '/../fixtures/drupal/core/tests/TestSuites/FunctionalJavascriptTestSuite.php',
            __DIR__ . '/../fixtures/drupal/core/tests/TestSuites/FunctionalTestSuite.php',
            __DIR__ . '/../fixtures/drupal/core/tests/TestSuites/KernelTestSuite.php',
            __DIR__ . '/../fixtures/drupal/core/tests/TestSuites/TestSuiteBase.php',
            __DIR__ . '/../fixtures/drupal/core/tests/TestSuites/UnitTestSuite.php',
        ];
        foreach ($paths as $path) {
            $errors = $this->runAnalyze($path);
            $this->assertCount(0, $errors, print_r($errors, true));
        }
    }

    public function testDrupalTestInChildSiteContant() {
        $errors = $this->runAnalyze(__DIR__ . '/../fixtures/drupal/modules/phpstan_fixtures/src/DrupalTestInChildSiteContant.php');
        $this->assertCount(0, $errors);
    }

    public function testExtensionReportsError() {
        $errors = $this->runAnalyze(__DIR__ . '/../fixtures/drupal/modules/phpstan_fixtures/phpstan_fixtures.module');
        $this->assertCount(2, $errors, var_export($errors, true));
        $error = array_shift($errors);
        $this->assertEquals('If condition is always false.', $error->getMessage());
        $error = array_shift($errors);
        $this->assertEquals('Function phpstan_fixtures_MissingReturnRule() should return string but return statement is missing.', $error->getMessage());
    }

    public function testServiceMapping() {
        $errorMessages = [
            '\Drupal calls should be avoided in classes, use dependency injection instead',
            'Call to an undefined method Drupal\Core\Entity\EntityManager::thisMethodDoesNotExist().',
        ];
        $errors = $this->runAnalyze(__DIR__ . '/../fixtures/drupal/modules/phpstan_fixtures/src/TestServicesMappingExtension.php');
        $this->assertCount(2, $errors);
        foreach ($errors as $key => $error) {
            $this->assertEquals($errorMessages[$key], $error->getMessage());
        }
    }
}
