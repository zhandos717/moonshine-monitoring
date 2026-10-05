# Changelog

## [1.4.1] - 2026-10-05

### Changed
- Dashboard and widget colors follow the installed MoonShine theme: primary color for charts and buttons, success / warning / error colors for statuses (`--ms-cm-*` on MoonShine 4, `--primary`, `--success-bg`, `--warning-bg`, `--error` on MoonShine 3). The Apple palette is used only when a theme variable is missing.

## [1.4.0] - 2026-10-05

### Added
- Alerts by email and Telegram when CPU, memory or disk stay above a threshold for N minutes, with a cooldown between repeats.
- Server switcher on the dashboard for several instances writing to one database; other servers show their last sample.
- Dashboard auto refresh without page reload (`auto_refresh`, toggle on the page).
- `MonitoringWidget` for the MoonShine home page.
- Automatic purge of old records in `moonshine-monitoring:record`; `MonitoringRecord` is `MassPrunable`.
- `Monitoring::fake()` to replace measurements in application tests.
- Composite index on `(instance_name, created_at)`.
- `disk_path` config option.
- PHPStan (Larastan, level 6) in CI.

### Fixed
- CPU on Linux was the average since boot; it is now measured over a 250 ms interval.
- Memory on macOS counted only active pages and was understated; it now matches Activity Monitor (app + wired + compressed).
- Windows metrics used `wmic`, removed in Windows 11 24H2; they now use PowerShell `Get-CimInstance`.
- The record command measured every resource twice.

### Changed
- `purge_before` now works and defaults to `-30 days` (was `-1 day`, never applied). Check it before upgrading if you need longer history.
- Resources no longer return fake 50% values when `APP_ENV=testing`; use `Monitoring::fake()`.
- `/monitoring/data` returns `current` separately from `records` and only for the current instance.

### Removed
- `GET /admin/monitoring` route (the dashboard is the MoonShine page), `Support\Format`, duplicate factory in `database/factories`.

## [1.3.0] - 2026-10-05

### Added
- Redesigned dashboard: status tiles (normal / high / critical), memory, CPU and disk charts with hover tooltips, light and dark theme support.
- Memory spike detection: spikes are marked on the chart and listed with peak, time, duration, rise over the usual level and used memory.
- Time ranges: 1 hour, 24 hours, 7 days; long ranges are downsampled to 240 points, memory keeps the bucket maximum so peaks are not lost.
- Config: `thresholds.warning`, `thresholds.critical`, `memory_spikes.min_rise`, `memory_spikes.window`.

### Changed
- Dashboard history shows only records of the current instance.

## [1.2.0] - 2026-10-05

### Added
- MoonShine 4.x support (MoonShine 3.x is still supported).
- CI matrix: PHP 8.2–8.4 × MoonShine 3.x / 4.x.

### Fixed
- Package could not be installed in projects with `minimum-stability: stable`: removed the unused `moonshine/apexcharts: dev-master` dependency and the hardcoded `version` field.
- Monitoring routes were registered without MoonShine authentication middleware; `/admin/monitoring/data` is now available to authenticated admins only.
- Routes broke `route:list` and request handling because the auth middleware array was nested inside the middleware list.
- Menu item argument order on MoonShine 4 (`MenuItem::make($filler, $label)`).
- Refresh button rendered the icon name as text instead of the icon.
- CPU usage on macOS ignored the number of cores and showed values close to 100%.
- Package test suite: registered the MoonShine service provider, removed tests for deleted shell scripts.

### Changed
- Requires PHP 8.2+ (same as MoonShine 3.x/4.x).
- `moonshine-monitoring:record` prints a confirmation line.
- README: correct publish tag, Laravel 11+ scheduling, requirements table.

## [1.1.3] - 2025-09-23

### Fixed
- Fixed "Typed property must not be accessed before initialization" error in AbstractResource
- Initialized $usage and $total properties with null values by default

## [1.1.2] - 2025-09-23

### Added
- Custom monitoring view with resource usage visualization
- Language files for English and Russian translations
- Auto-refresh functionality for real-time monitoring
- Progress bars for better visualization of resource usage

### Fixed
- Migration registration in service provider
- Resource publishing configuration
- View component registration
- Factory namespace issues

### Changed
- Updated monitoring view template
- Improved data visualization in monitoring dashboard
- Enhanced monitoring controller to support both AJAX and regular requests

## [1.1.0] - 2025-09-23

### Added
- Full compatibility with MoonShine 3.x
- Historical data charts on the monitoring dashboard
- Proper JSON API endpoint in the controller
- Default view template for the monitoring component

### Fixed
- Removed debug code (`dd()` statements) from controllers and components
- Improved service provider registration for MoonShine 3.x
- Enhanced error handling and response formatting

### Changed
- Updated README with MoonShine 3.x compatibility information
- Improved dashboard with historical data visualization
- Enhanced component view data handling

## [1.0.0] - 2024-05-02

### Added
- Initial release
- CPU, memory, and disk monitoring
- Basic dashboard with current resource usage
- Command-line recording capability
- Database storage for monitoring records