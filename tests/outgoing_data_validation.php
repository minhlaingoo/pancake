<?php

/**
 * Pancake Project - Outgoing Data Validation
 * 
 * This script validates all outgoing data structures and API endpoints
 * without requiring a running server instance
 */

class PancakeDataValidator
{
    private $testResults = [];
    private $projectPath;
    
    public function __construct()
    {
        $this->projectPath = dirname(__DIR__);
        echo "🔬 Pancake Project - Outgoing Data Validation\n";
        echo "============================================\n\n";
    }
    
    /**
     * Run all validation tests
     */
    public function runAllValidations()
    {
        $this->validateRouteStructure();
        $this->validateControllerMethods();
        $this->validateLivewireComponents();
        $this->validateApiEndpoints();
        $this->validateMqttConfiguration();
        $this->validateDatabaseStructure();
        $this->validateDataModels();
        
        $this->generateReport();
    }
    
    /**
     * Validate route structure and API endpoints
     */
    private function validateRouteStructure()
    {
        echo "🛣️ Validating Route Structure...\n";
        
        $routeFile = $this->projectPath . '/routes/web.php';
        
        if (!file_exists($routeFile)) {
            $this->logTest('Routes', 'File Existence', false, 'routes/web.php not found');
            return;
        }
        
        $routeContent = file_get_contents($routeFile);
        
        // Check for critical route patterns
        $criticalRoutes = [
            'dashboard' => 'Dashboard route',
            'devices' => 'Device management routes',
            'protocols' => 'Protocol management routes',
            'sensors' => 'Sensor data routes',
            'users' => 'User management routes',
            'mqtt' => 'MQTT communication route'
        ];
        
        foreach ($criticalRoutes as $pattern => $description) {
            $exists = strpos($routeContent, $pattern) !== false;
            $this->logTest('Routes', $pattern, $exists, 
                          $exists ? "$description found" : "$description missing");
        }
        
        // Validate API route structure
        $this->validateApiRoutePatterns($routeContent);
    }
    
    /**
     * Validate API route patterns
     */
    private function validateApiRoutePatterns($routeContent)
    {
        echo "📡 Validating API Route Patterns...\n";
        
        // Expected API patterns for outgoing data
        $apiPatterns = [
            '/api/devices' => 'Device data API',
            '/api/sensors' => 'Sensor data API',  
            '/api/protocols' => 'Protocol management API',
            '/api/mqtt' => 'MQTT communication API',
            '/api/dashboard' => 'Dashboard data API'
        ];
        
        // Since routes/api.php might not exist, check for API-like patterns in web.php
        foreach ($apiPatterns as $pattern => $description) {
            // Look for Livewire components that would handle API-like functionality
            $componentExists = $this->checkForApiLikeComponent($pattern);
            $this->logTest('API Routes', $pattern, $componentExists, 
                          $componentExists ? "$description component found" : "$description missing");
        }
    }
    
    /**
     * Check for API-like Livewire components
     */
    private function checkForApiLikeComponent($pattern)
    {
        $componentPattern = str_replace('/api/', '', $pattern);
        $componentPaths = [
            $this->projectPath . '/app/Livewire/' . ucfirst($componentPattern) . '.php',
            $this->projectPath . '/app/Http/Controllers/' . ucfirst($componentPattern) . 'Controller.php'
        ];
        
        foreach ($componentPaths as $path) {
            if (file_exists($path)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Validate controller methods for data output
     */
    private function validateControllerMethods()
    {
        echo "🎛️ Validating Controller Methods...\n";
        
        $controllerDir = $this->projectPath . '/app/Http/Controllers';
        
        if (!is_dir($controllerDir)) {
            $this->logTest('Controllers', 'Directory', false, 'Controllers directory not found');
            return;
        }
        
        $controllers = glob($controllerDir . '/*.php');
        
        foreach ($controllers as $controller) {
            $this->validateControllerDataMethods($controller);
        }
    }
    
    /**
     * Validate individual controller data methods
     */
    private function validateControllerDataMethods($controllerPath)
    {
        $content = file_get_contents($controllerPath);
        $controllerName = basename($controllerPath, '.php');
        
        // Look for methods that output data
        $dataMethods = [
            'json' => 'JSON response method',
            'response()' => 'Response method',
            'return response' => 'Return response',
            'export' => 'Data export method'
        ];
        
        foreach ($dataMethods as $pattern => $description) {
            $exists = strpos($content, $pattern) !== false;
            if ($exists) {
                $this->logTest('Controllers', "$controllerName - $pattern", true, $description);
            }
        }
    }
    
    /**
     * Validate Livewire components for data output
     */
    private function validateLivewireComponents()
    {
        echo "⚡ Validating Livewire Components...\n";
        
        $livewireDir = $this->projectPath . '/app/Livewire';
        
        if (!is_dir($livewireDir)) {
            $this->logTest('Livewire', 'Directory', false, 'Livewire directory not found');
            return;
        }
        
        $this->validateLivewireDirectory($livewireDir);
    }
    
    /**
     * Recursively validate Livewire directory
     */
    private function validateLivewireDirectory($dir, $prefix = '')
    {
        $items = scandir($dir);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $fullPath = $dir . '/' . $item;
            
            if (is_dir($fullPath)) {
                $this->validateLivewireDirectory($fullPath, $prefix . $item . '/');
            } elseif (is_file($fullPath) && pathinfo($fullPath, PATHINFO_EXTENSION) === 'php') {
                $this->validateLivewireComponent($fullPath, $prefix . pathinfo($item, PATHINFO_FILENAME));
            }
        }
    }
    
    /**
     * Validate individual Livewire component
     */
    private function validateLivewireComponent($componentPath, $componentName)
    {
        $content = file_get_contents($componentPath);
        
        // Check for data output patterns
        $dataPatterns = [
            'public function render' => 'Render method',
            '$this->emit' => 'Event emission',
            '$this->dispatch' => 'Event dispatch',
            'response()' => 'HTTP response',
            'download(' => 'File download'
        ];
        
        foreach ($dataPatterns as $pattern => $description) {
            $exists = strpos($content, $pattern) !== false;
            if ($exists) {
                $this->logTest('Livewire Components', "$componentName - $pattern", true, $description);
            }
        }
    }
    
    /**
     * Validate API endpoints structure
     */
    private function validateApiEndpoints()
    {
        echo "🌐 Validating API Endpoint Structure...\n";
        
        // Expected outgoing data endpoints
        $expectedEndpoints = [
            'Device Status' => ['devices/status', 'devices/{id}/sensors'],
            'Sensor Data' => ['sensors/{id}/readings', 'sensors/stream'],
            'Protocol Data' => ['protocols/{id}/status', 'protocols/{id}/export'],
            'System Data' => ['dashboard/stats', 'activity-logs/export'],
            'MQTT Data' => ['mqtt/publish', 'mqtt/status']
        ];
        
        foreach ($expectedEndpoints as $category => $endpoints) {
            foreach ($endpoints as $endpoint) {
                // Check if corresponding Livewire component or controller exists
                $componentExists = $this->checkEndpointImplementation($endpoint);
                $this->logTest('API Endpoints', "$category - $endpoint", $componentExists,
                              $componentExists ? 'Implementation found' : 'Implementation missing');
            }
        }
    }
    
    /**
     * Check if endpoint has implementation
     */
    private function checkEndpointImplementation($endpoint)
    {
        // Convert endpoint to possible component names
        $parts = explode('/', $endpoint);
        $mainComponent = ucfirst($parts[0]);
        
        $possiblePaths = [
            $this->projectPath . "/app/Livewire/{$mainComponent}",
            $this->projectPath . "/app/Http/Controllers/{$mainComponent}Controller.php"
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path) || is_dir($path)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Validate MQTT configuration
     */
    private function validateMqttConfiguration()
    {
        echo "📨 Validating MQTT Configuration...\n";
        
        $composerFile = $this->projectPath . '/composer.json';
        $envFile = $this->projectPath . '/.env.example';
        
        // Check for MQTT package in composer.json
        if (file_exists($composerFile)) {
            $composer = json_decode(file_get_contents($composerFile), true);
            $mqttPackage = isset($composer['require']['php-mqtt/client']);
            $this->logTest('MQTT', 'Package Installation', $mqttPackage,
                          $mqttPackage ? 'php-mqtt/client package found' : 'MQTT package missing');
        }
        
        // Check for MQTT configuration in routes
        $webRoutes = file_get_contents($this->projectPath . '/routes/web.php');
        $mqttRoute = strpos($webRoutes, 'mqtt') !== false;
        $this->logTest('MQTT', 'Route Configuration', $mqttRoute,
                      $mqttRoute ? 'MQTT routes configured' : 'MQTT routes missing');
        
        // Check for MQTT-related Livewire component
        $mqttComponent = file_exists($this->projectPath . '/app/Livewire/MqttComponent.php');
        $this->logTest('MQTT', 'Component Implementation', $mqttComponent,
                      $mqttComponent ? 'MQTT component found' : 'MQTT component missing');
    }
    
    /**
     * Validate database structure
     */
    private function validateDatabaseStructure()
    {
        echo "🗄️ Validating Database Structure...\n";
        
        $migrationDir = $this->projectPath . '/database/migrations';
        
        if (!is_dir($migrationDir)) {
            $this->logTest('Database', 'Migration Directory', false, 'Migration directory not found');
            return;
        }
        
        $migrations = glob($migrationDir . '/*.php');
        
        // Expected tables for IoT lab management
        $expectedTables = [
            'users' => 'User management table',
            'devices' => 'Device management table',
            'sensors' => 'Sensor data table',
            'protocols' => 'Protocol management table',
            'activity' => 'Activity logs table'
        ];
        
        foreach ($expectedTables as $table => $description) {
            $tableExists = $this->checkTableMigration($migrations, $table);
            $this->logTest('Database Tables', $table, $tableExists,
                          $tableExists ? "$description migration found" : "$description migration missing");
        }
    }
    
    /**
     * Check if table migration exists
     */
    private function checkTableMigration($migrations, $tableName)
    {
        foreach ($migrations as $migration) {
            $migrationContent = file_get_contents($migration);
            if (strpos($migrationContent, $tableName) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Validate data models
     */
    private function validateDataModels()
    {
        echo "📊 Validating Data Models...\n";
        
        $modelDir = $this->projectPath . '/app/Models';
        
        if (!is_dir($modelDir)) {
            $this->logTest('Models', 'Directory', false, 'Models directory not found');
            return;
        }
        
        $models = glob($modelDir . '/*.php');
        
        foreach ($models as $model) {
            $this->validateModelStructure($model);
        }
    }
    
    /**
     * Validate individual model structure
     */
    private function validateModelStructure($modelPath)
    {
        $content = file_get_contents($modelPath);
        $modelName = basename($modelPath, '.php');
        
        // Check for essential model features
        $modelFeatures = [
            'protected $fillable' => 'Fillable attributes',
            'protected $casts' => 'Attribute casting',
            'public function' => 'Model methods',
            'belongsTo' => 'Relationship methods',
            'hasMany' => 'Relationship methods'
        ];
        
        foreach ($modelFeatures as $pattern => $description) {
            $exists = strpos($content, $pattern) !== false;
            if ($exists) {
                $this->logTest('Models', "$modelName - $pattern", true, $description);
            }
        }
    }
    
    /**
     * Log test results
     */
    private function logTest($category, $test, $passed, $message)
    {
        $this->testResults[] = [
            'category' => $category,
            'test' => $test,
            'passed' => $passed,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        $status = $passed ? '✅' : '❌';
        echo "  $status $test: $message\n";
    }
    
    /**
     * Generate comprehensive validation report
     */
    private function generateReport()
    {
        echo "\n📊 VALIDATION REPORT SUMMARY\n";
        echo "============================\n\n";
        
        $totalTests = count($this->testResults);
        $passedTests = count(array_filter($this->testResults, function($test) {
            return $test['passed'];
        }));
        $failedTests = $totalTests - $passedTests;
        
        echo "Total Validations: $totalTests\n";
        echo "Passed: $passedTests ✅\n";
        echo "Failed: $failedTests ❌\n";
        echo "Validation Rate: " . round(($passedTests / $totalTests) * 100, 2) . "%\n\n";
        
        // Group results by category
        $categorizedResults = [];
        foreach ($this->testResults as $result) {
            $categorizedResults[$result['category']][] = $result;
        }
        
        foreach ($categorizedResults as $category => $tests) {
            echo "### $category\n";
            $categoryPassed = count(array_filter($tests, function($test) {
                return $test['passed'];
            }));
            echo "Passed: $categoryPassed/" . count($tests) . "\n";
            
            // Show failed tests for attention
            $failedTests = array_filter($tests, function($test) {
                return !$test['passed'];
            });
            
            if (!empty($failedTests)) {
                echo "⚠️ Issues found:\n";
                foreach ($failedTests as $test) {
                    echo "   ❌ {$test['test']}: {$test['message']}\n";
                }
            }
            echo "\n";
        }
        
        // Generate recommendations
        $this->generateRecommendations();
        
        // Save detailed report
        $this->saveValidationReport();
    }
    
    /**
     * Generate improvement recommendations
     */
    private function generateRecommendations()
    {
        echo "💡 RECOMMENDATIONS FOR OUTGOING DATA\n";
        echo "===================================\n\n";
        
        echo "1. **API Structure Enhancement:**\n";
        echo "   - Create dedicated routes/api.php for RESTful endpoints\n";
        echo "   - Implement API controllers for structured data output\n";
        echo "   - Add API versioning for future scalability\n\n";
        
        echo "2. **MQTT Data Streaming:**\n";
        echo "   - Ensure MQTT client configuration is properly set up\n";
        echo "   - Implement real-time sensor data publishing\n";
        echo "   - Add MQTT topic management for device communications\n\n";
        
        echo "3. **Data Export Capabilities:**\n";
        echo "   - Add CSV/JSON export for protocol results\n";
        echo "   - Implement real-time data streaming endpoints\n";
        echo "   - Create scheduled data backup functionality\n\n";
        
        echo "4. **Security & Validation:**\n";
        echo "   - Add API rate limiting for outgoing data\n";
        echo "   - Implement data validation before output\n";
        echo "   - Add authentication for sensitive data endpoints\n\n";
    }
    
    /**
     * Save detailed validation report
     */
    private function saveValidationReport()
    {
        $report = [
            'validation_suite' => 'Pancake Project Outgoing Data Validation',
            'timestamp' => date('Y-m-d H:i:s'),
            'summary' => [
                'total_validations' => count($this->testResults),
                'passed_validations' => count(array_filter($this->testResults, function($test) {
                    return $test['passed'];
                })),
                'failed_validations' => count(array_filter($this->testResults, function($test) {
                    return !$test['passed'];
                }))
            ],
            'results' => $this->testResults,
            'project_structure' => $this->analyzeProjectStructure()
        ];
        
        file_put_contents('pancake_validation_report.json', json_encode($report, JSON_PRETTY_PRINT));
        echo "📄 Detailed validation report saved to: pancake_validation_report.json\n";
    }
    
    /**
     * Analyze overall project structure
     */
    private function analyzeProjectStructure()
    {
        return [
            'framework' => 'Laravel',
            'frontend_stack' => 'Livewire + MijnUI',
            'iot_integration' => 'MQTT Client',
            'database_type' => 'MySQL/SQLite',
            'key_features' => [
                'Device Management',
                'Sensor Data Collection',
                'Protocol Processing',
                'User Management',
                'Activity Logging',
                'MQTT Communication'
            ]
        ];
    }
}

// Run the validation
if (php_sapi_name() === 'cli') {
    $validator = new PancakeDataValidator();
    $validator->runAllValidations();
}