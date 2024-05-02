<x-moonshine::grid xmlns:x-moonshine="http://www.w3.org/1999/html" x-data="data()">
    <x-moonshine::column colSpan="12">
        <x-moonshine::box>
            <div class="flex justify-between">
                <div class="flex gap-2">
                    <x-moonshine::link-button href="#"
                                              icon="heroicons.outline.arrow-path"
                                              x-on:click="fetchLogs()"
                    >
                    <span>
                        {{ __('moonshine-monitoring::monitoring.refresh') }}
                    </span>
                    </x-moonshine::link-button>
                </div>
            </div>
            <x-moonshine::table>
                <x-slot:thead>
                    <th>{{ __('moonshine-log-viewer::log-viewer.level') }}</th>
                    <th>{{ __('moonshine-log-viewer::log-viewer.env') }}</th>
                    <th>{{ __('moonshine-log-viewer::log-viewer.time') }}</th>
                    <th>{{ __('moonshine-log-viewer::log-viewer.message') }}</th>
                    <th></th>
                </x-slot:thead>
                <x-slot:tbody x-init="fetchLogs()">
                    <template x-for="(log, index) in logs" :key="index">
                        <tr>
                            <td>
                                <span class="badge" x-bind:class="levelColor(log.level)" x-text="log.level"></span>
                            </td>
                            <td><strong x-html="log.env"></strong></td>
                            <td style="width:150px;" x-text="log.time"></td>
                            <td><code style="word-break: break-all;" x-text="log.info"></code></td>
                            <td>

                            </td>
                        </tr>
                    </template>
                </x-slot:tbody>
            </x-moonshine::table>
        </x-moonshine::box>
    </x-moonshine::column>
</x-moonshine::grid>

<script>
    function data() {
        return {
            isLoading: false,
            end: 0,
            fileName: null,
            logFiles: [],
            logs: [],
            size: "Unknown",
            refreshIntervalId: null,
            file: '',
            nextUrl: null,
            prevUrl: null,
            lastUpdate: null,
            filter_level: [],
            filter_env: '',
            filter_time_start: '',
            filter_time_end: '',
            filter_info: '',
            {{--        fetch(url, params = {}, rewrite = true) {--}}
            {{--            let _url = new URL(url);--}}

            {{--            _url.searchParams.append('filter_level', this.filter_level);--}}
            {{--            _url.searchParams.append('filter_env', this.filter_env);--}}
            {{--            _url.searchParams.append('filter_time_start', this.filter_time_start);--}}
            {{--            _url.searchParams.append('filter_time_end', this.filter_time_end);--}}
            {{--            _url.searchParams.append('filter_info', this.filter_info);--}}

            {{--            for (let key in params) {--}}
            {{--                if (params.hasOwnProperty(key)) {--}}
            {{--                    _url.searchParams.append(key, params[key]);--}}
            {{--                }--}}
            {{--            }--}}

            {{--            fetch(_url.toString())--}}
            {{--                .then(res => res.json())--}}
            {{--                .then(data => {--}}
            {{--                    this.isLoading = false;--}}
            {{--                    this.end = data.end;--}}
            {{--                    this.file = data.fileName;--}}
            {{--                    this.fileName = data.fileName;--}}
            {{--                    this.logFiles = data.logFiles;--}}
            {{--                    this.logs = rewrite ? data.logs : [...data.logs, ...this.logs];--}}
            {{--                    this.size = data.size;--}}
            {{--                    this.nextUrl = data.nextUrl;--}}
            {{--                    this.prevUrl = data.prevUrl;--}}
            {{--                    this.lastUpdate = data.lastUpdate;--}}
            {{--                });--}}
            {{--        },--}}
            {{--        fetchLogs() {--}}
            {{--            this.isLoading = true;--}}
            {{--            this.fetch(`{{ route('moonshine.log.viewer.file') }}/${this.file}`);--}}
            {{--        },--}}
            {{--        fetchLastLogs() {--}}
            {{--            this.fetch(`{{ route('moonshine.log.viewer.file') }}/${this.file}`, {offset: this.end}, false);--}}
            {{--        },--}}
            {{--        selectFile(file) {--}}
            {{--            this.file = file;--}}
            {{--            this.end = 0;--}}
            {{--            this.stopPlay();--}}
            {{--            this.filterReset();--}}
            {{--        },--}}
            {{--        togglePlay() {--}}
            {{--            if (this.refreshIntervalId) {--}}
            {{--                clearInterval(this.refreshIntervalId);--}}
            {{--                this.refreshIntervalId = null;--}}
            {{--            } else {--}}
            {{--                this.fetchLogs();--}}
            {{--                this.refreshIntervalId = setInterval(() => this.fetchLastLogs(), 2000);--}}
            {{--            }--}}
            {{--        },--}}
            {{--        stopPlay() {--}}
            {{--            if (this.refreshIntervalId) {--}}
            {{--                clearInterval(this.refreshIntervalId);--}}
            {{--                this.refreshIntervalId = null;--}}
            {{--            }--}}
            {{--        },--}}
            {{--        prevPage() {--}}
            {{--            this.stopPlay();--}}
            {{--            this.fetch(this.prevUrl);--}}
            {{--        },--}}
            {{--        nextPage() {--}}
            {{--            this.stopPlay();--}}
            {{--            this.fetch(this.nextUrl);--}}
            {{--        },--}}
            {{--        levelColor(level) {--}}
            {{--            const levelColors = {--}}
            {{--                EMERGENCY: 'badge-gray',--}}
            {{--                ALERT: 'badge-primary',--}}
            {{--                CRITICAL: 'badge-red',--}}
            {{--                ERROR: 'badge-error',--}}
            {{--                WARNING: 'badge-warning',--}}
            {{--                NOTICE: 'badge-blue',--}}
            {{--                INFO: 'badge-info',--}}
            {{--                DEBUG: 'badge-green',--}}
            {{--            };--}}
            {{--            return levelColors[level];--}}
            {{--        },--}}
            {{--        filterApply() {--}}
            {{--            this.fetch(`{{ route('moonshine.log.viewer.file') }}/${this.file}`);--}}
            {{--        },--}}
            {{--        filterReset() {--}}
            {{--            this.filter_level = [];--}}
            {{--            this.filter_env = '';--}}
            {{--            this.filter_time_start = '';--}}
            {{--            this.filter_time_end = '';--}}
            {{--            this.filter_info = '';--}}
            {{--            this.fetchLogs();--}}
            {{--        }--}}
        }
    }
</script>