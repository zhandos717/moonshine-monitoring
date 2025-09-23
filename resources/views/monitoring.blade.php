<x-moonshine::layout.grid xmlns:x-moonshine="http://www.w3.org/1999/html" x-data="monitoringData">
    <x-moonshine::layout.column colSpan="12">
        <x-moonshine::layout.box>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">{{ __('moonshine-monitoring::ui.monitoring') }}</h2>
                <div class="flex gap-2">
                    <x-moonshine::link-button href="#"
                                              x-on:click="fetchMonitoringData()"
                    >
                        <x-moonshine::icon icon="arrow-path"/>
                        <span>
                            {{ __('moonshine-monitoring::ui.refresh') }}
                        </span>
                    </x-moonshine::link-button>
                    <x-moonshine::link-button href="#"
                                              x-on:click="toggleAutoRefresh()"
                    >
                        <span x-show="!refreshIntervalId"><x-moonshine::icon icon="play"/></span>
                        <span x-show="refreshIntervalId"><x-moonshine::icon icon="pause"/></span>
                    </x-moonshine::link-button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <x-moonshine::layout.box>
                    <div class="text-center">
                        <h3 class="text-lg font-semibold mb-2">{{ __('moonshine-monitoring::ui.cpu') }}</h3>
                        <div class="text-3xl font-bold" x-text="currentData.cpu ? currentData.cpu + '%' : 'N/A'"></div>
                        <div class="mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" 
                                     :style="{ width: (currentData.cpu || 0) + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </x-moonshine::layout.box>
                
                <x-moonshine::layout.box>
                    <div class="text-center">
                        <h3 class="text-lg font-semibold mb-2">{{ __('moonshine-monitoring::ui.memory') }}</h3>
                        <div class="text-3xl font-bold" x-text="currentData.memory ? currentData.memory + '%' : 'N/A'"></div>
                        <div class="mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-green-600 h-2.5 rounded-full" 
                                     :style="{ width: (currentData.memory || 0) + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </x-moonshine::layout.box>
                
                <x-moonshine::layout.box>
                    <div class="text-center">
                        <h3 class="text-lg font-semibold mb-2">{{ __('moonshine-monitoring::ui.disk') }}</h3>
                        <div class="text-3xl font-bold" x-text="currentData.disk ? currentData.disk + '%' : 'N/A'"></div>
                        <div class="mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-purple-600 h-2.5 rounded-full" 
                                     :style="{ width: (currentData.disk || 0) + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </x-moonshine::layout.box>
            </div>
            
            <x-moonshine::table>
                <x-slot:thead>
                    <th>{{ __('moonshine-monitoring::ui.instance_name') }}</th>
                    <th>{{ __('moonshine-monitoring::ui.cpu') }} (%)</th>
                    <th>{{ __('moonshine-monitoring::ui.memory') }} (%)</th>
                    <th>{{ __('moonshine-monitoring::ui.disk') }} (%)</th>
                    <th>{{ __('moonshine-monitoring::ui.timestamp') }}</th>
                </x-slot:thead>
                <x-slot:tbody>
                    <template x-for="(record, index) in records" :key="index">
                        <tr>
                            <td x-text="record.instance_name"></td>
                            <td>
                                <div class="flex items-center">
                                    <span x-text="record.cpu || 'N/A'"></span>
                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-blue-600 h-1.5 rounded-full" 
                                             :style="{ width: (record.cpu || 0) + '%' }"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <span x-text="record.memory || 'N/A'"></span>
                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-green-600 h-1.5 rounded-full" 
                                             :style="{ width: (record.memory || 0) + '%' }"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <span x-text="record.disk || 'N/A'"></span>
                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-purple-600 h-1.5 rounded-full" 
                                             :style="{ width: (record.disk || 0) + '%' }"></div>
                                    </div>
                                </div>
                            </td>
                            <td x-text="record.created_at"></td>
                        </tr>
                    </template>
                </x-slot:tbody>
            </x-moonshine::table>
        </x-moonshine::layout.box>
    </x-moonshine::layout.column>
</x-moonshine::layout.grid>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('monitoringData', () => ({
            isLoading: false,
            records: [],
            currentData: {},
            refreshIntervalId: null,
            instanceName: '{{ gethostname() }}',

            init() {
                this.fetchMonitoringData();
            },

            fetchMonitoringData() {
                this.isLoading = true;
                fetch('{{ route('moonshine.monitoring.index') }}')
                    .then(res => res.json())
                    .then(data => {
                        this.isLoading = false;
                        if (data.status === 'success') {
                            this.records = data.records;
                            // Устанавливаем текущие данные (последняя запись)
                            if (data.records.length > 0) {
                                this.currentData = data.records[0];
                            }
                        }
                    })
                    .catch(error => {
                        this.isLoading = false;
                        console.error('Error fetching monitoring data:', error);
                    });
            },

            toggleAutoRefresh() {
                if (this.refreshIntervalId) {
                    clearInterval(this.refreshIntervalId);
                    this.refreshIntervalId = null;
                } else {
                    this.fetchMonitoringData();
                    this.refreshIntervalId = setInterval(() => this.fetchMonitoringData(), 5000);
                }
            },

            stopAutoRefresh() {
                if (this.refreshIntervalId) {
                    clearInterval(this.refreshIntervalId);
                    this.refreshIntervalId = null;
                }
            }
        }));
    });
</script>