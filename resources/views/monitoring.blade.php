<x-moonshine::layout.grid>
    <x-moonshine::layout.column colSpan="12">
        <x-moonshine::layout.box>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">{{ __('moonshine-monitoring::ui.monitoring') }}</h2>
                <div class="flex gap-2">
                    <x-moonshine::link-button 
                        icon="arrow-path"
                        @click="fetchMonitoringData()"
                    >
                        {{ __('moonshine-monitoring::ui.refresh') }}
                    </x-moonshine::link-button>
                </div>
            </div>

            @if(isset($records) && count($records) > 0)
                @php
                    $current = $records[0];
                    $cpuUsage = $current->cpu ?? 0;
                    $memoryUsage = $current->memory ?? 0;
                    $diskUsage = $current->disk ?? 0;
                    $cpuCores = $current->cpu_cores ?? null;
                    $memoryTotal = $current->memory_total_bytes ?? null;
                    $diskTotal = $current->disk_total_bytes ?? null;
                    
                    // Format memory info
                    $memorySubValue = '';
                    if ($memoryTotal && $memoryTotal > 0) {
                        $usedMemory = ($memoryUsage / 100) * $memoryTotal;
                        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                        $memoryBase = log($memoryTotal, 1024);
                        $memoryFloor = floor($memoryBase);
                        $memoryPow = pow(1024, $memoryFloor);
                        $memoryValue = round($memoryTotal / $memoryPow, 2);
                        $memoryUnit = $units[(int)$memoryFloor];
                        
                        $usedBase = log($usedMemory, 1024);
                        $usedFloor = floor($usedBase);
                        $usedPow = pow(1024, $usedFloor);
                        $usedValue = round($usedMemory / $usedPow, 2);
                        $usedUnit = $units[(int)$usedFloor];
                        
                        $memorySubValue = $usedValue . ' ' . $usedUnit . ' / ' . $memoryValue . ' ' . $memoryUnit;
                    }
                    
                    // Format disk info
                    $diskSubValue = '';
                    if ($diskTotal && $diskTotal > 0) {
                        $usedDisk = ($diskUsage / 100) * $diskTotal;
                        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                        $diskBase = log($diskTotal, 1024);
                        $diskFloor = floor($diskBase);
                        $diskPow = pow(1024, $diskFloor);
                        $diskValue = round($diskTotal / $diskPow, 2);
                        $diskUnit = $units[(int)$diskFloor];
                        
                        $usedDiskBase = log($usedDisk, 1024);
                        $usedDiskFloor = floor($usedDiskBase);
                        $usedDiskPow = pow(1024, $usedDiskFloor);
                        $usedDiskValue = round($usedDisk / $usedDiskPow, 2);
                        $usedDiskUnit = $units[(int)$usedDiskFloor];
                        
                        $diskSubValue = $usedDiskValue . ' ' . $usedDiskUnit . ' / ' . $diskValue . ' ' . $diskUnit;
                    }
                    
                    // Format CPU cores info
                    $cpuSubValue = '';
                    if ($cpuCores) {
                        $cpuSubValue = $cpuCores . ' ' . ($cpuCores == 1 ? __('moonshine-monitoring::ui.core') : __('moonshine-monitoring::ui.cores'));
                    }
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <!-- CPU Metric -->
                    <x-moonshine::metrics.value
                        title="{{ __('moonshine-monitoring::ui.cpu') }}"
                        value="{{ number_format($cpuUsage, 2) }}%"
                        subValue="{{ $cpuSubValue }}"
                    >
                        <x-slot:footer>
                            <div class="mt-2">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" 
                                         style="width: {{ $cpuUsage }}%"></div>
                                </div>
                            </div>
                        </x-slot:footer>
                    </x-moonshine::metrics.value>

                    <!-- Memory Metric -->
                    <x-moonshine::metrics.value
                        title="{{ __('moonshine-monitoring::ui.memory') }}"
                        value="{{ number_format($memoryUsage, 2) }}%"
                        subValue="{{ $memorySubValue }}"
                    >
                        <x-slot:footer>
                            <div class="mt-2">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" 
                                         style="width: {{ $memoryUsage }}%"></div>
                                </div>
                            </div>
                        </x-slot:footer>
                    </x-moonshine::metrics.value>

                    <!-- Disk Metric -->
                    <x-moonshine::metrics.value
                        title="{{ __('moonshine-monitoring::ui.disk') }}"
                        value="{{ number_format($diskUsage, 2) }}%"
                        subValue="{{ $diskSubValue }}"
                    >
                        <x-slot:footer>
                            <div class="mt-2">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full" 
                                         style="width: {{ $diskUsage }}%"></div>
                                </div>
                            </div>
                        </x-slot:footer>
                    </x-moonshine::metrics.value>
                </div>

                <!-- Historical Data Table -->
                <x-moonshine::table>
                    <x-slot:thead>
                        <th class="px-4 py-2">{{ __('moonshine-monitoring::ui.instance_name') }}</th>
                        <th class="px-4 py-2">{{ __('moonshine-monitoring::ui.cpu') }}</th>
                        <th class="px-4 py-2">{{ __('moonshine-monitoring::ui.memory') }}</th>
                        <th class="px-4 py-2">{{ __('moonshine-monitoring::ui.disk') }}</th>
                        <th class="px-4 py-2">{{ __('moonshine-monitoring::ui.timestamp') }}</th>
                    </x-slot:thead>
                    <x-slot:tbody>
                        @foreach($records as $record)
                            @php
                                // Format record data
                                $recordCpu = $record->cpu ?? 0;
                                $recordMemory = $record->memory ?? 0;
                                $recordDisk = $record->disk ?? 0;
                                
                                // Format CPU cores for record
                                $recordCpuCores = '';
                                if (isset($record->cpu_cores)) {
                                    $recordCpuCores = ' (' . $record->cpu_cores . ' ' . ($record->cpu_cores == 1 ? __('moonshine-monitoring::ui.core') : __('moonshine-monitoring::ui.cores')) . ')';
                                }
                                
                                // Format memory for record
                                $recordMemoryDetails = '';
                                if (isset($record->memory_total_bytes) && $record->memory_total_bytes > 0) {
                                    $usedMem = ($recordMemory / 100) * $record->memory_total_bytes;
                                    $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                                    $memBase = log($record->memory_total_bytes, 1024);
                                    $memFloor = floor($memBase);
                                    $memPow = pow(1024, $memFloor);
                                    $memValue = round($record->memory_total_bytes / $memPow, 2);
                                    $memUnit = $units[(int)$memFloor];
                                    
                                    $usedBase = log($usedMem, 1024);
                                    $usedFloor = floor($usedBase);
                                    $usedPow = pow(1024, $usedFloor);
                                    $usedValue = round($usedMem / $usedPow, 2);
                                    $usedUnit = $units[(int)$usedFloor];
                                    
                                    $recordMemoryDetails = ' (' . $usedValue . ' ' . $usedUnit . ' / ' . $memValue . ' ' . $memUnit . ')';
                                }
                                
                                // Format disk for record
                                $recordDiskDetails = '';
                                if (isset($record->disk_total_bytes) && $record->disk_total_bytes > 0) {
                                    $usedDisk = ($recordDisk / 100) * $record->disk_total_bytes;
                                    $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                                    $diskBase = log($record->disk_total_bytes, 1024);
                                    $diskFloor = floor($diskBase);
                                    $diskPow = pow(1024, $diskFloor);
                                    $diskValue = round($record->disk_total_bytes / $diskPow, 2);
                                    $diskUnit = $units[(int)$diskFloor];
                                    
                                    $usedDiskBase = log($usedDisk, 1024);
                                    $usedDiskFloor = floor($usedDiskBase);
                                    $usedDiskPow = pow(1024, $usedDiskFloor);
                                    $usedDiskValue = round($usedDisk / $usedDiskPow, 2);
                                    $usedDiskUnit = $units[(int)$usedDiskFloor];
                                    
                                    $recordDiskDetails = ' (' . $usedDiskValue . ' ' . $usedDiskUnit . ' / ' . $diskValue . ' ' . $diskUnit . ')';
                                }
                            @endphp
                            <tr>
                                <td class="px-4 py-2">{{ $record->instance_name }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center">
                                        <span>{{ number_format($recordCpu, 2) }}%</span>
                                        @if($recordCpuCores)
                                            <span class="ml-1 text-xs text-gray-500">
                                                {{ $recordCpuCores }}
                                            </span>
                                        @endif
                                        <div class="ml-2 w-16 bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-blue-600 h-1.5 rounded-full" 
                                                 style="width: {{ $recordCpu }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center">
                                        <span>{{ number_format($recordMemory, 2) }}%</span>
                                        @if($recordMemoryDetails)
                                            <span class="ml-1 text-xs text-gray-500">
                                                {{ $recordMemoryDetails }}
                                            </span>
                                        @endif
                                        <div class="ml-2 w-16 bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-green-600 h-1.5 rounded-full" 
                                                 style="width: {{ $recordMemory }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center">
                                        <span>{{ number_format($recordDisk, 2) }}%</span>
                                        @if($recordDiskDetails)
                                            <span class="ml-1 text-xs text-gray-500">
                                                {{ $recordDiskDetails }}
                                            </span>
                                        @endif
                                        <div class="ml-2 w-16 bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-purple-600 h-1.5 rounded-full" 
                                                 style="width: {{ $recordDisk }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2">{{ $record->created_at }}</td>
                            </tr>
                        @endforeach
                    </x-slot:tbody>
                </x-moonshine::table>
            @else
                <div class="text-center py-8 text-gray-500">
                    {{ __('No monitoring data available') }}
                </div>
            @endif
        </x-moonshine::layout.box>
    </x-moonshine::layout.column>
</x-moonshine::layout.grid>

<script>
    function fetchMonitoringData() {
        fetch('{{ route('moonshine.monitoring.data') }}')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Reload the page to show updated data
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error fetching monitoring data:', error);
                alert('Failed to refresh monitoring data');
            });
    }
</script>