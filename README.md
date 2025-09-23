# Moonshine Monitoring

Server monitoring package for MoonShine admin panel.

## Description

Moonshine Monitoring is a Laravel package that provides system resource monitoring capabilities for applications using the MoonShine admin panel. It tracks CPU, memory, and disk usage of your server and displays this information in an intuitive dashboard within MoonShine.

## Features

- Real-time monitoring of CPU usage
- Memory consumption tracking
- Disk space monitoring
- Data visualization in MoonShine dashboard
- Command-line interface for manual data recording
- Database storage of monitoring records
- Automatic data purging based on configuration
- Multi-instance support with instance naming
- Pure PHP implementation (no shell scripts required)

## Installation

1. Install the package via composer:
   ```bash
   composer require zhandos717/moonshine-monitoring
   ```

2. Publish the configuration file:
   ```bash
   php artisan vendor:publish --provider="Zhandos717\MoonshineMonitoring\MonitoringServiceProvider" --tag=config
   ```

3. Run the migrations:
   ```bash
   php artisan migrate
   ```

4. The monitoring dashboard will automatically appear in your MoonShine menu.

## Usage

### Basic Usage

The package automatically adds a monitoring page to your MoonShine dashboard. You can configure whether this menu item is automatically added via the `auto_menu` option in the configuration file.

### Command Line

You can manually record resource usage via the command line:
```bash
php artisan moonshine-monitoring:record
```

### Scheduling

To continuously monitor your system, schedule the record command in your `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('moonshine-monitoring:record')->everyMinute();
}
```

## Configuration

After publishing the configuration file, you can modify the settings in `config/monitoring.php`:

- `auto_menu`: Whether to automatically add the monitoring page to the MoonShine menu
- `instance_name`: The name of this monitoring instance (defaults to your app name)
- `notifications`: Telegram notification settings (not yet implemented)
- `migrations`: Enable or disable package migrations
- `purge_before`: Automatically purge records older than this time period

## Roadmap

### Short-term Goals (v1.x)

1. **Complete Dashboard Implementation**
   - Add line charts for historical data visualization
   - Implement filtering by time periods
   - Add instance comparison capabilities

2. **Notification System**
   - Implement Telegram notifications for resource threshold breaches
   - Add email notification support
   - Create configurable alert thresholds

3. **Performance Improvements**
   - Optimize database queries for large datasets
   - Implement data aggregation for long-term storage
   - Add caching mechanisms for frequently accessed data

4. **UI/UX Enhancements**
   - Improve the visual design of metrics display
   - Add dark mode support
   - Implement responsive design for mobile devices

### Long-term Goals (v2.x)

1. **Extended Monitoring Capabilities**
   - Network usage monitoring
   - Process monitoring
   - Service status monitoring
   - Database performance metrics

2. **Advanced Analytics**
   - Predictive resource usage analysis
   - Anomaly detection algorithms
   - Usage pattern recognition

3. **Multi-server Support**
   - Centralized monitoring dashboard
   - Cross-server comparisons
   - Distributed monitoring agents

4. **Export and Reporting**
   - PDF report generation
   - CSV data export
   - Scheduled report delivery

## MoonShine 3.x Compatibility

This package now fully supports MoonShine 3.x with the following updates:

- Updated service provider for new MoonShine architecture
- Compatible page and component implementations
- Updated controller with proper JSON responses
- Fixed debug code issues
- Enhanced dashboard with historical data charts

## Issues and Improvements Needed

After analyzing the codebase and updating for MoonShine 3.x compatibility, here are the key technical issues and improvements needed:

### Resolved Issues

1. **Debug Code in Controllers**
   - Removed `dd()` statements from `MonitoringController` and `MonitoringComponent`

2. **Incomplete Implementation**
   - Enhanced controller to return proper JSON responses
   - Improved monitoring dashboard with historical data visualization

### Remaining Issues

1. **Configuration Issues**
   - The notifications section in the config file is not implemented but has a placeholder
   - The purge functionality for old records is configured but not implemented

2. **Platform Support**
   - Shell scripts only exist for Darwin (macOS) and Linux, with no Windows support
   - The script resolution logic may not work correctly on all systems

3. **Code Quality**
   - Missing input validation in several components
   - Limited error handling in shell script execution
   - No unit tests exist for the package

### Enhancement Opportunities

1. **Data Visualization**
   - Add more detailed historical data charts
   - Implement time range filtering for metrics
   - Add comparison capabilities between different time periods

2. **Performance Optimization**
   - Implement caching for frequently accessed data
   - Add batch processing for recording multiple metrics
   - Optimize database queries for large datasets

3. **User Experience**
   - Add customizable alert thresholds
   - Implement dashboard widgets that can be rearranged
   - Add export functionality for monitoring data

## Testing

The package includes comprehensive tests for all components:

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage
```

### Test Coverage

- Unit tests for all models, actions, and system resources
- Feature tests for controllers and pages
- Configuration tests
- Shell script path validation

### Running Tests

1. Install development dependencies:
```bash
composer install
```

2. Run the test suite:
```bash
./vendor/bin/phpunit
```

### Test Structure

The tests are organized as follows:
- `tests/Unit/` - Unit tests for individual components
- `tests/Feature/` - Feature tests for integrated functionality
- `tests/TestCase.php` - Base test case with package configuration

### Current Test Status

Currently, we have implemented:
- Simple unit tests that don't require the full Laravel/MoonShine environment
- Tests for system resources (CPU, Memory, Disk)
- Tests for the MonitoringRecord model
- Basic configuration tests

Some tests that require the full MoonShine environment are currently failing due to dependency injection issues. These will be resolved in future updates.

## Contributing

Contributions are welcome! Here's how you can help:

1. Fork the repository
2. Create a new branch for your feature or bug fix
3. Write your code and tests
4. Submit a pull request with a clear description of your changes

### Development Guidelines

- Follow PSR-12 coding standards
- Write meaningful commit messages
- Include tests for new functionality
- Update documentation when needed
- Ensure all tests pass before submitting a pull request

### Areas Needing Contribution

1. **Windows Support**
   - Create PowerShell scripts for Windows systems
   - Test cross-platform compatibility

2. **Additional Metrics**
   - Network usage monitoring
   - Process monitoring
   - Custom metric support

3. **Testing**
   - Expand test coverage
   - Add integration tests
   - Set up continuous integration

4. **Documentation**
   - Expand usage examples
   - Add troubleshooting guides
   - Create API documentation

## Requirements

- PHP 8.1 or higher
- Laravel 8.0 or higher
- MoonShine 3.0 or higher

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.